<?php

namespace common\services\cctv;

use common\models\Camera;
use common\models\Dvr;
use common\models\ScanLog;
use Yii;

/**
 * Сервис для интеграции с Hikvision ISAPI
 */
class HikvisionService extends BaseCctvService
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

    private function apiGet(string $endpoint, bool $xml = true): ?array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Basic ' . base64_encode($this->auth),
        ];

        if ($xml) {
            $headers[] = 'Accept: application/xml';
        } else {
            $headers[] = 'Accept: application/json';
        }

        $result = $this->httpGet($url, [
            'headers' => $headers,
        ]);

        if ($result === null) {
            return null;
        }

        // Если пришёл XML — парсим
        if (isset($result['raw']) && str_starts_with(trim($result['raw']), '<')) {
            return $this->xmlToArray($result['raw']);
        }

        return $result;
    }

    private function xmlToArray(string $xml): array
    {
        try {
            $simpleXml = simplexml_load_string($xml);
            if ($simpleXml === false) {
                return ['raw' => $xml];
            }
            return json_decode(json_encode($simpleXml), true) ?: [];
        } catch (\Exception $e) {
            return ['raw' => $xml];
        }
    }

    public function scanDvr(): array
    {
        // Информация об устройстве
        $deviceInfo = $this->apiGet('/ISAPI/System/deviceInfo');

        // Статус устройства
        $deviceStatus = $this->apiGet('/ISAPI/System/status');

        // Информация о хранилище
        $storageInfo = $this->apiGet('/ISAPI/ContentMgmt/Storage');

        if (!$deviceInfo) {
            $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_ERROR, 'Hikvision: cannot get device info');
            return ['success' => false, 'error' => 'Cannot connect'];
        }

        // ИСПРАВЛЕНО: убран дублирующийся ключ
        $dvrData = [
            'model' => $deviceInfo['model'] ?? null,
            'firmware' => $deviceInfo['firmwareVersion']
                ?? $deviceInfo['firmwareReleasedDate']
                    ?? null,
            'serial_number' => $deviceInfo['serialNumber'] ?? null,
            'mac_address' => $deviceInfo['macAddress'] ?? null,
            'channel_count' => (int)($deviceInfo['digitChannelNum']
                ?? ($deviceInfo['analogChannelNum'] ?? 0)),
            'disk_info' => $storageInfo,
            'extra_data' => [
                'device_info' => $deviceInfo,
                'status' => $deviceStatus,
            ],
        ];

        $this->updateDvr($dvrData);
        $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_SUCCESS, 'Hikvision DVR scanned', $dvrData);

        return ['success' => true, 'data' => $dvrData];
    }

    public function scanCameras(): array
    {
        // Получаем список каналов
        $channels = $this->apiGet('/ISAPI/ContentMgmt/InputProxy/channels');

        // Получаем статус каналов
        $channelStatus = $this->apiGet('/ISAPI/ContentMgmt/InputProxy/channels/status');

        $cameras = [];

        $channelList = $channels['InputProxyChannel'] ?? [];
        if (isset($channelList['id'])) {
            $channelList = [$channelList]; // Если один канал, оборачиваем в массив
        }

        $statusList = $channelStatus['InputProxyChannelStatus'] ?? [];
        if (isset($statusList['id'])) {
            $statusList = [$statusList];
        }

        // Создаём карту статусов по ID
        $statusMap = [];
        foreach ($statusList as $status) {
            $id = $status['id'] ?? null;
            if ($id !== null) {
                $statusMap[$id] = $status;
            }
        }

        foreach ($channelList as $channel) {
            $channelId = $channel['id'] ?? null;
            if ($channelId === null) continue;

            $status = $statusMap[$channelId] ?? [];
            $isOnline = ($status['online'] ?? '') === 'true';

            // Получаем потоковую информацию
            $streamInfo = $this->apiGet("/ISAPI/Streaming/channels/{$channelId}01");

            $camData = [
                'channel_no' => (int)$channelId,
                'name' => $channel['name'] ?? ('Channel ' . $channelId),
                'ip_address' => $channel['sourceInputPortDescriptor']['ipAddress'] ?? null,
                'status' => $isOnline ? Camera::STATUS_ONLINE : Camera::STATUS_OFFLINE,
                'stream_main' => sprintf(
                    'rtsp://%s:%s@%s:554/Streaming/Channels/%d01',
                    $this->dvr->username ?? 'admin',
                    $this->dvr->password ?? 'admin',
                    $this->dvr->ip_address,
                    $channelId
                ),
                'stream_sub' => sprintf(
                    'rtsp://%s:%s@%s:554/Streaming/Channels/%d02',
                    $this->dvr->username ?? 'admin',
                    $this->dvr->password ?? 'admin',
                    $this->dvr->ip_address,
                    $channelId
                ),
                'ptz_supported' => ($channel['PTZAbility']['ptzScheme']['PTZNode']['supported'] ?? '') === 'true',
                'extra_data' => [
                    'channel' => $channel,
                    'status' => $status,
                    'stream' => $streamInfo,
                ],
            ];

            // Извлекаем разрешение из потока
            if (isset($streamInfo['Video']['videoResolutionWidth'])
                && isset($streamInfo['Video']['videoResolutionHeight'])) {
                $camData['resolution'] = $streamInfo['Video']['videoResolutionWidth']
                    . 'x'
                    . $streamInfo['Video']['videoResolutionHeight'];
            }

            $cameras[] = $camData;
        }

        $this->syncCameras($cameras);
        $this->log(ScanLog::TYPE_CAMERA, ScanLog::STATUS_SUCCESS,
            sprintf('Hikvision: %d cameras found', count($cameras)),
            ['count' => count($cameras)]
        );

        return ['success' => true, 'count' => count($cameras)];
    }
}
