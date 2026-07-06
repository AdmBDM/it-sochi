<?php

namespace common\services\cctv;

use common\models\Camera;
use common\models\Dvr;
use common\models\ScanLog;
use Yii;

/**
 * Сервис для интеграции с Trassir SDK
 * API возвращает плоский массив объектов с полями: name, guid, class, parent
 */
class TrassirService extends BaseCctvService
{
    private ?string $sid = null;
    private string $baseUrl;

    public function __construct(Dvr $dvr)
    {
        parent::__construct($dvr);
        $this->baseUrl = sprintf('https://%s:%d', $dvr->ip_address, $dvr->port);
    }

    private function login(): bool
    {
        $password = $this->dvr->sdk_password ?? $this->dvr->password;
        if (!$password) {
            Yii::error('Trassir: no password configured');
            return false;
        }

        $url = sprintf('%s/login?password=%s', $this->baseUrl, urlencode($password));
        $response = $this->httpGet($url);

        if ($response && isset($response['sid'])) {
            $this->sid = $response['sid'];
            return true;
        }

        return false;
    }

    private function apiGet(string $endpoint): ?array
    {
        if (!$this->sid && !$this->login()) {
            return null;
        }

        $url = sprintf('%s%s?sid=%s', $this->baseUrl, $endpoint, $this->sid);
        return $this->httpGet($url);
    }

    public function scanDvr(): array
    {
        if (!$this->login()) {
            $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_ERROR, 'Trassir login failed');
            return ['success' => false, 'error' => 'Login failed'];
        }

        // Получаем информацию о сервере
        $health = $this->apiGet('/health');

        // Получаем список объектов для подсчёта каналов
        $objects = $this->apiGet('/objects/');

        $dvrData = [
            'model' => $health['name'] ?? ($health['server'] ?? null),
            'firmware' => $health['version'] ?? null,
            'serial_number' => $health['guid'] ?? null,
            'channel_count' => 0,
            'extra_data' => [
                'health' => $health,
            ],
        ];

        // Подсчитываем каналы — ищем объекты с class = "Channel"
        if (is_array($objects)) {
            foreach ($objects as $obj) {
                if (isset($obj['class']) && $obj['class'] === 'Channel') {
                    $dvrData['channel_count']++;
                }
            }
        }

        $this->updateDvr($dvrData);
        $this->log(ScanLog::TYPE_DVR, ScanLog::STATUS_SUCCESS, 'Trassir DVR scanned', $dvrData);

        return ['success' => true, 'data' => $dvrData];
    }

    public function scanCameras(): array
    {
        if (!$this->sid && !$this->login()) {
            $this->log(ScanLog::TYPE_CAMERA, ScanLog::STATUS_ERROR, 'Trassir login failed');
            return ['success' => false, 'error' => 'Login failed'];
        }

        // Получаем плоский массив объектов
        $objects = $this->apiGet('/objects/');

        $cameras = [];

        if (!is_array($objects) || empty($objects)) {
            $this->log(ScanLog::TYPE_CAMERA, ScanLog::STATUS_ERROR, 'No objects found', ['response' => $objects]);
            return ['success' => false, 'error' => 'No objects'];
        }

        foreach ($objects as $obj) {
            // Фильтруем только каналы
            if (!isset($obj['class']) || $obj['class'] !== 'Channel') {
                continue;
            }

            $guid = $obj['guid'] ?? null;
            if (!$guid) continue;

            $name = $obj['name'] ?? ('Camera ' . $guid);

            // Получаем детали канала
            $channelSettings = $this->apiGet('/settings/channels/' . $guid . '/');

            // Получаем состояние объекта
            $objectState = $this->apiGet('/objects/' . $guid . '/');

//            $camData = [
//                'guid' => $guid,
//                'name' => $name,
//                'status' => ($objectState['online'] ?? false) ? Camera::STATUS_ONLINE : Camera::STATUS_OFFLINE,
//                'stream_main' => sprintf(
//                    '%s/get_video?channel=%s&container=rtsp&stream=main&sid=%s',
//                    $this->baseUrl, $guid, $this->sid
//                ),
//                'stream_sub' => sprintf(
//                    '%s/get_video?channel=%s&container=rtsp&stream=sub&sid=%s',
//                    $this->baseUrl, $guid, $this->sid
//                ),
//                'extra_data' => [
//                    'settings' => $channelSettings,
//                    'state' => $objectState,
//                    'raw_object' => $obj,
//                ],
//            ];
            $password = urlencode($this->dvr->sdk_password ?? $this->dvr->password ?? '');

            $camData = [
                'guid' => $guid,
                'name' => $name,
                'status' => ($objectState['online'] ?? false) ? Camera::STATUS_ONLINE : Camera::STATUS_OFFLINE,
                'stream_main' => sprintf(
                    '%s/get_video?channel=%s&container=rtsp&stream=main&password=%s',
                    $this->baseUrl, $guid, $password
                ),
                'stream_sub' => sprintf(
                    '%s/get_video?channel=%s&container=rtsp&stream=sub&password=%s',
                    $this->baseUrl, $guid, $password
                ),
                'extra_data' => [
                    'settings' => $channelSettings,
                    'state' => $objectState,
                    'raw_object' => $obj,
                ],
            ];

            // Извлекаем IP из настроек если есть
            if (isset($channelSettings['ip_address'])) {
                $camData['ip_address'] = $channelSettings['ip_address'];
            }

            $cameras[] = $camData;
        }

        $this->syncCameras($cameras);
        $this->log(ScanLog::TYPE_CAMERA, ScanLog::STATUS_SUCCESS,
            sprintf('Trassir: %d cameras found', count($cameras)),
            ['count' => count($cameras)]
        );

        return ['success' => true, 'count' => count($cameras)];
    }
}
