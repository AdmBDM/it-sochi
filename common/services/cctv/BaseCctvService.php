<?php

namespace common\services\cctv;

use common\models\Camera;
use common\models\Dvr;
use common\models\ScanLog;
use Yii;

abstract class BaseCctvService
{
    protected Dvr $dvr;
    protected int $timeout = 10;

    public function __construct(Dvr $dvr)
    {
        $this->dvr = $dvr;
    }

    abstract public function scanDvr(): array;
    abstract public function scanCameras(): array;

    protected function log(string $type, string $status, string $message, array $details = []): void
    {
        $log = new ScanLog();
        $log->dvr_id = $this->dvr->id;
        $log->scan_type = $type;
        $log->status = $status;
        $log->message = $message;
        $log->details = $details;
        $log->save(false);
    }

    /**
     * Универсальный HTTP GET через curl (не требует внешних зависимостей)
     * Используем нативный curl для максимальной совместимости
     */
    protected function httpGet(string $url, array $options = []): ?array
    {
        $ch = curl_init();

        $curlOpts = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $options['timeout'] ?? $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => $options['headers'] ?? [],
        ];

        // Basic Auth
        if (isset($options['auth'])) {
            $curlOpts[CURLOPT_USERPWD] = $options['auth'];
        }

        curl_setopt_array($ch, $curlOpts);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            Yii::error("cURL error: $error");
            return null;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            Yii::error("HTTP $httpCode for $url");
            return null;
        }

        $content = trim($response);

        // Пытаемся распарсить JSON
        $json = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        // Если не JSON — возвращаем как raw
        return ['raw' => $content];
    }

    /**
     * Альтернатива через Guzzle (если установлен)
     */
    protected function httpGetGuzzle(string $url, array $options = []): ?array
    {
        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => $options['timeout'] ?? $this->timeout,
                'verify' => false,
            ]);

            $requestOptions = [];

            if (isset($options['headers'])) {
                $requestOptions['headers'] = $options['headers'];
            }

            if (isset($options['auth'])) {
                [$user, $pass] = explode(':', $options['auth'], 2);
                $requestOptions['auth'] = [$user, $pass];
            }

            $response = $client->request('GET', $url, $requestOptions);
            $body = (string) $response->getBody();

            $json = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }

            return ['raw' => $body];
        } catch (\Exception $e) {
            Yii::error("Guzzle error: " . $e->getMessage());
            return null;
        }
    }

    protected function updateDvr(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($this->dvr->hasAttribute($key) && $value !== null) {
                $this->dvr->$key = $value;
            }
        }
        $this->dvr->last_scan_at = date('Y-m-d H:i:s');

        if (!$this->dvr->save(false)) {
            Yii::error('DVR save failed: ' . print_r($this->dvr->errors, true));
            throw new \RuntimeException('Failed to save DVR: ' . json_encode($this->dvr->errors));
        }
    }

    protected function syncCameras(array $camerasData): void
    {
        $existingGuids = [];
        $existingChannels = [];

        foreach ($camerasData as $camData) {
            $guid = $camData['guid'] ?? null;
            $channelNo = $camData['channel_no'] ?? null;

            $query = Camera::find()->where(['dvr_id' => $this->dvr->id]);

            if ($guid) {
                $query->andWhere(['guid' => $guid]);
                $existingGuids[] = $guid;
            } elseif ($channelNo !== null) {
                $query->andWhere(['channel_no' => $channelNo]);
                $existingChannels[] = $channelNo;
            } else {
                continue;
            }

            $camera = $query->one();

            if (!$camera) {
                $camera = new Camera();
                $camera->dvr_id = $this->dvr->id;
            }

            foreach ($camData as $key => $value) {
                if ($camera->hasAttribute($key) && $value !== null) {
                    $camera->$key = $value;
                }
            }

            $camera->save(false);
        }

        // Помечаем отсутствующие камеры как офлайн
        $offlineQuery = Camera::find()->where(['dvr_id' => $this->dvr->id]);
        if (!empty($existingGuids)) {
            $offlineQuery->andWhere(['not in', 'guid', $existingGuids]);
        }
        if (!empty($existingChannels)) {
            $offlineQuery->andWhere(['not in', 'channel_no', $existingChannels]);
        }

        foreach ($offlineQuery->all() as $offlineCam) {
            $offlineCam->status = Camera::STATUS_OFFLINE;
            $offlineCam->save(false);
        }
    }
}
