<?php

namespace console\controllers;

use common\models\Dvr;
use common\services\cctv\CctvFactory;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class CctvController extends Controller
{
    public $defaultAction = 'help';

    /**
     * Справка по командам
     */
    public function actionHelp(): int
    {
        $this->stdout("Команды управления системами видеонаблюдения:\n", Console::FG_CYAN);
        $this->stdout("  yii cctv/init-dvrs        - Инициализация DVR из конфигурации\n");
        $this->stdout("  yii cctv/scan-dvrs        - Опрос всех видеорегистраторов\n");
        $this->stdout("  yii cctv/scan-dvr [id]    - Опрос конкретного DVR\n");
        $this->stdout("  yii cctv/scan-cameras     - Опрос камер на всех DVR\n");
        $this->stdout("  yii cctv/scan-camera [id] - Опрос камер на конкретном DVR\n");
        $this->stdout("  yii cctv/full-scan        - Полное сканирование (DVR + камеры)\n");
        $this->stdout("  yii cctv/status           - Статус всех систем\n");
        return ExitCode::OK;
    }

    /**
     * Инициализация DVR из вашей конфигурации
     */
    public function actionInitDvrs(): int
    {
        $this->stdout("Инициализация видеорегистраторов...\n", Console::FG_YELLOW);

        $dvrs = [
            // Trassir
            [
                'name' => 'Блок Адлер',
                'system_type' => Dvr::SYSTEM_TRASSIR,
                'ip_address' => '192.168.124.4',
                'port' => 8080,
                'network' => '192.168.124.0/24',
                'sdk_password' => null, // Укажите реальный SDK-пароль
            ],
            [
                'name' => 'Блок Прадо',
                'system_type' => Dvr::SYSTEM_TRASSIR,
                'ip_address' => '192.168.124.5',
                'port' => 8081,
                'network' => '192.168.124.0/24',
                'sdk_password' => null,
            ],
            // Dahua
            [
                'name' => 'Блок Geely',
                'system_type' => Dvr::SYSTEM_DAHUA,
                'ip_address' => '192.168.40.50',
                'port' => 37777,
                'network' => '192.168.40.0/24',
                'username' => 'admin',
                'password' => null, // Укажите реальный пароль
            ],
            [
                'name' => 'Блок BelGee',
                'system_type' => Dvr::SYSTEM_DAHUA,
                'ip_address' => '192.168.40.200',
                'port' => 36777,
                'network' => '192.168.40.0/24',
                'username' => 'admin',
                'password' => null,
            ],
            [
                'name' => 'Блок MeSochi',
                'system_type' => Dvr::SYSTEM_DAHUA,
                'ip_address' => '192.168.130.150',
                'port' => 37777,
                'network' => '192.168.130.0/24',
                'username' => 'admin',
                'password' => null,
            ],
            // Hikvision
            [
                'name' => 'Блок Geely (Hikvision)',
                'system_type' => Dvr::SYSTEM_HIKVISION,
                'ip_address' => '192.168.40.100',
                'port' => 8000,
                'network' => '192.168.40.0/24',
                'username' => 'admin',
                'password' => null, // Укажите реальный пароль
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($dvrs as $config) {
            $dvr = Dvr::findOne([
                'ip_address' => $config['ip_address'],
                'port' => $config['port'],
            ]);

            if (!$dvr) {
                $dvr = new Dvr();
                $isNew = true;
            } else {
                $isNew = false;
            }

            $dvr->attributes = $config;

            if ($dvr->save()) {
                if ($isNew) {
                    $created++;
                    $this->stdout("  Создан: {$config['name']} ({$config['ip_address']}:{$config['port']})\n", Console::FG_GREEN);
                } else {
                    $updated++;
                    $this->stdout("  Обновлён: {$config['name']}\n", Console::FG_BLUE);
                }
            } else {
                $this->stdout("  Ошибка: " . implode(', ', $dvr->firstErrors) . "\n", Console::FG_RED);
            }
        }

        $this->stdout("\nГотово: создано $created, обновлено $updated\n", Console::FG_CYAN);
        return ExitCode::OK;
    }

    /**
     * Опрос всех видеорегистраторов
     */
    public function actionScanDvrs(): int
    {
        $dvrs = Dvr::find()->where(['status' => Dvr::STATUS_ACTIVE])->all();

        $this->stdout("Опрос " . count($dvrs) . " видеорегистраторов...\n\n", Console::FG_YELLOW);

        $success = 0;
        $failed = 0;

        foreach ($dvrs as $dvr) {
            $this->stdout("[" . $dvr->getSystemLabel() . "] {$dvr->name} ({$dvr->ip_address}:{$dvr->port})... ", Console::FG_CYAN);

            try {
                $service = CctvFactory::create($dvr);
                $result = $service->scanDvr();

                if ($result['success']) {
                    $this->stdout("OK", Console::FG_GREEN);
                    if (isset($result['data']['model'])) {
                        $this->stdout(" [{$result['data']['model']}]", Console::FG_GREY);
                    }
                    $success++;
                } else {
                    $this->stdout("FAIL: " . ($result['error'] ?? 'Unknown error'), Console::FG_RED);
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->stdout("ERROR: " . $e->getMessage(), Console::FG_RED);
                $failed++;
            }

            $this->stdout("\n");
        }

        $this->stdout("\nРезультат: $success успешно, $failed ошибок\n", Console::FG_YELLOW);
        return $failed > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * Опрос конкретного DVR
     * @param int $id ID DVR
     */
    public function actionScanDvr(int $id): int
    {
        $dvr = Dvr::findOne($id);
        if (!$dvr) {
            $this->stderr("DVR с ID $id не найден\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Опрос DVR: {$dvr->name} ({$dvr->ip_address}:{$dvr->port})\n", Console::FG_YELLOW);

        $service = CctvFactory::create($dvr);
        $result = $service->scanDvr();

        if ($result['success']) {
            $this->stdout("Успешно!\n", Console::FG_GREEN);
            print_r($result['data'] ?? []);
            return ExitCode::OK;
        } else {
            $this->stderr("Ошибка: " . ($result['error'] ?? 'Unknown') . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Опрос камер на всех DVR
     */
    public function actionScanCameras(): int
    {
        $dvrs = Dvr::find()->where(['status' => Dvr::STATUS_ACTIVE])->all();

        $this->stdout("Опрос камер на " . count($dvrs) . " видеорегистраторах...\n\n", Console::FG_YELLOW);

        $totalCameras = 0;
        $failed = 0;

        foreach ($dvrs as $dvr) {
            $this->stdout("[" . $dvr->getSystemLabel() . "] {$dvr->name}... ", Console::FG_CYAN);

            try {
                $service = CctvFactory::create($dvr);
                $result = $service->scanCameras();

                if ($result['success']) {
                    $count = $result['count'] ?? 0;
                    $this->stdout("$count камер", Console::FG_GREEN);
                    $totalCameras += $count;
                } else {
                    $this->stdout("FAIL: " . ($result['error'] ?? 'Unknown'), Console::FG_RED);
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->stdout("ERROR: " . $e->getMessage(), Console::FG_RED);
                $failed++;
            }

            $this->stdout("\n");
        }

        $this->stdout("\nВсего камер: $totalCameras\n", Console::FG_YELLOW);
        return $failed > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * Опрос камер на конкретном DVR
     * @param int $id ID DVR
     */
    public function actionScanCamera(int $id): int
    {
        $dvr = Dvr::findOne($id);
        if (!$dvr) {
            $this->stderr("DVR с ID $id не найден\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Опрос камер на DVR: {$dvr->name}\n", Console::FG_YELLOW);

        $service = CctvFactory::create($dvr);
        $result = $service->scanCameras();

        if ($result['success']) {
            $this->stdout("Найдено камер: " . ($result['count'] ?? 0) . "\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stderr("Ошибка: " . ($result['error'] ?? 'Unknown') . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Полное сканирование: DVR + камеры
     */
    public function actionFullScan(): int
    {
        $this->stdout("=== ПОЛНОЕ СКАНИРОВАНИЕ ===\n\n", Console::FG_YELLOW);

        $this->stdout("Шаг 1: Опрос видеорегистраторов\n", Console::FG_CYAN);
        $this->actionScanDvrs();

        $this->stdout("\nШаг 2: Опрос камер\n", Console::FG_CYAN);
        $this->actionScanCameras();

        $this->stdout("\n=== СКАНИРОВАНИЕ ЗАВЕРШЕНО ===\n", Console::FG_GREEN);
        return ExitCode::OK;
    }

    /**
     * Статус всех систем
     */
    public function actionStatus(): int
    {
        $dvrs = Dvr::find()->all();

        $this->stdout("Статус систем видеонаблюдения\n", Console::FG_CYAN);
        $this->stdout(str_repeat("=", 80) . "\n");

        foreach ($dvrs as $dvr) {
            $statusColor = $dvr->status === Dvr::STATUS_ACTIVE ? Console::FG_GREEN : Console::FG_RED;
            $statusText = $dvr->status === Dvr::STATUS_ACTIVE ? 'АКТИВЕН' : 'ВЫКЛ';

            $this->stdout(sprintf(
                "[%s] %-20s %-15s:%-5d %-12s Каналов: %-3d Последний опрос: %s\n",
                strtoupper($dvr->system_type),
                $dvr->name,
                $dvr->ip_address,
                $dvr->port,
                $statusText,
                $dvr->channel_count ?? '?',
                $dvr->last_scan_at ?? 'никогда'
            ), $statusColor);

            // Статус камер
            $online = $dvr->getCameras()->where(['status' => \common\models\Camera::STATUS_ONLINE])->count();
            $total = $dvr->getCameras()->count();
            $this->stdout("         Камер: $online/$total онлайн\n", Console::FG_GREY);
        }

        return ExitCode::OK;
    }
}
