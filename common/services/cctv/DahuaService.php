<?php

namespace common\services\cctv;

use common\models\Camera;
use common\models\Dvr;
use common\models\ScanLog;
use Yii;

/**
 * Сервис для интеграции с Dahua HTTP API
 * Документация: DAHUA IPC HTTP API
 */
class DahuaService extends BaseCctvService
{
    private string $baseUrl;
    private string $auth;

    public function __construct(Dvr $dvr)
    {
        parent::__construct($dvr);
        $this->baseUrl = sprintf('http://%s:%d', $dvr->ip_address, $dvr->port);
        $this->auth = sprintf('%s:%s',
            $dvr->username ?? 'admin',
            $dvr->password ?? 'admin'
        );
    }

    private function apiGet(string $endpoint): ?array
    {
        $url = $this->baseUrl . $endpoint;
        return $this->httpGet($url, [
            'auth' => $this->auth,
        ]);
    }

    private function parseCgIResponse(string $content): array
    {
        $result = [];
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $result[trim($key)] = trim($value);
            }
        }
        return $result;
    }

    public function scanDvr(): array
    {
        // Информация об устройстве
        $response = $this->apiGet('/cgi-bin/magicBox.cgi?action=getSystemInfo');

        // Если пришёл raw-ответ (Dahua возвращает plain text), парсим
        $deviceInfo = is_array($response) && isset($response['raw'])
            ? $this->parseCgIResponse($response['raw'])
            : ($response ?? []);

        // Информация о дисках
        $storageRaw = $this->apiGet('/cgi-bin/storageDevice.cgi?action=factory.get');
        $storageInfo = is_array($storageRaw) && isset($storageRaw['raw'])
            ? $this->parseCgIResponse($storageRaw['raw'])
            : ($storageRaw ?? []);

        // Сетевые интерфейсы
        $networkRaw = $this->apiGet('/cgi-bin/configManager.cgi?action=getConfig&name=Network');
        $networkInfo = is_array($networkRaw) && isset($networkRaw['raw'])
            ? $this->parseCgIResponse($networkRaw['raw'])
            : ($networkRaw ?? []);

        if (empty($deviceInfo)) {
            $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_ERROR, 'Dahua: cannot get system info');
            return ['success' => false, 'error' => 'Cannot connect'];
        }

        $dvrData = [
            'model' => $deviceInfo['DeviceType'] ?? ($deviceInfo['table.DeviceType'] ?? null),
            'firmware' => $deviceInfo['SoftwareVersion'] ?? ($deviceInfo['table.SoftwareVersion'] ?? null),
            'serial_number' => $deviceInfo['SerialNo'] ?? ($deviceInfo['table.SerialNo'] ?? null),
            'mac_address' => $deviceInfo['MacAddress'] ?? ($deviceInfo['table.MacAddress'] ?? null),
            'channel_count' => (int)($deviceInfo['ChannelNum'] ?? ($deviceInfo['table.ChannelNum'] ?? 0)),
            'disk_info' => $storageInfo,
            'extra_data' => [
                'device_info' => $deviceInfo,
                'network' => $networkInfo,
            ],
        ];

        $this->updateDvr($dvrData);
        $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_SUCCESS, 'Dahua DVR scanned', $dvrData);

        return ['success' => true, 'data' => $dvrData];
    }

    public function scanCameras(): array
    {
        // Получаем список каналов
        $channelRaw = $this->apiGet('/cgi-bin/configManager.cgi?action=getConfig&name=ChannelTitle');
        $channelInfo = is_array($channelRaw) && isset($channelRaw['raw'])
            ? $this->parseCgIResponse($channelRaw['raw'])
            : ($channelRaw ?? []);

        // Получаем статус видеовходов
        $videoRaw = $this->apiGet('/cgi-bin/videoStatus.cgi?action=get');
        $videoStatus = is_array($videoRaw) && isset($videoRaw['raw'])
            ? $this->parseCgIResponse($videoRaw['raw'])
            : ($videoRaw ?? []);

        // Получаем конфигурацию видео
        $encodeRaw = $this->apiGet('/cgi-bin/configManager.cgi?action=getConfig&name=VideoEncode');
        $videoConfig = is_array($encodeRaw) && isset($encodeRaw['raw'])
            ? $this->parseCgIResponse($encodeRaw['raw'])
            : ($encodeRaw ?? []);

        $cameras = [];
        $channelCount = $this->dvr->channel_count ?: 32;

        for ($i = 1; $i <= $channelCount; $i++) {
            $channelKey = "table.ChannelTitle[$i]";
            $name = $channelInfo[$channelKey] ?? ($channelInfo["ChannelTitle[$i]"] ?? "Channel $i");

            // Проверяем статус
            $statusKey = "status[$i]";
            $isOnline = isset($videoStatus[$statusKey]) && $videoStatus[$statusKey] !== 'None';

            // Получаем PTZ-информацию
            $ptzRaw = $this->apiGet("/cgi-bin/ptz.cgi?action=getCurrentProtocolCaps&channel=$i");
            $ptzInfo = is_array($ptzRaw) && isset($ptzRaw['raw'])
                ? $this->parseCgIResponse($ptzRaw['raw'])
                : ($ptzRaw ?? []);

            $camData = [
                'channel_no' => $i,
                'name' => $name,
                'status' => $isOnline ? Camera::STATUS_ONLINE : Camera::STATUS_OFFLINE,
                'stream_main' => sprintf(
                    'rtsp://%s:%s@%s:554/cam/realmonitor?channel=%d&subtype=0',
                    $this->dvr->username ?? 'admin',
                    $this->dvr->password ?? 'admin',
                    $this->dvr->ip_address,
                    $i
                ),
                'stream_sub' => sprintf(
                    'rtsp://%s:%s@%s:554/cam/realmonitor?channel=%d&subtype=1',
                    $this->dvr->username ?? 'admin',
                    $this->dvr->password ?? 'admin',
                    $this->dvr->ip_address,
                    $i
                ),
                'ptz_supported' => !empty($ptzInfo),
                'ptz_protocol' => $ptzInfo['caps.Name'] ?? null,
                'extra_data' => [
                    'ptz_caps' => $ptzInfo,
                ],
            ];

            // Извлекаем разрешение из конфигурации
            $resKey = "table.VideoEncode[$i][0].Resolution";
            if (isset($videoConfig[$resKey])) {
                $camData['resolution'] = $videoConfig[$resKey];
            }

            $cameras[] = $camData;
        }

        $this->syncCameras($cameras);
        $this->log(ScanLog::TYPE_CAMERA, ScanLog::STATUS_SUCCESS,
            sprintf('Dahua: %d cameras processed', count($cameras)),
            ['count' => count($cameras)]
        );

        return ['success' => true, 'count' => count($cameras)];
    }
}