<?php

namespace console\controllers;

use common\models\Device;
use common\models\DiscoveredPrinter;
use Yii;
use yii\console\Controller;
use yii\db\Exception;
use yii\helpers\Console;

/**
 * Контроллер сопоставления найденных принтеров с устройствами в базе.
 *
 * Приоритет сопоставления:
 *   1. MAC-адрес (самый надёжный)
 *   2. Серийный номер
 *   3. IP-адрес
 *   4. Имя + модель (fuzzy match)
 */
class PrinterMatchController extends Controller
{
    /**
     * Запуск автоматического сопоставления.
     *
     * Usage:
     *   php yii printer-match/run [--dry-run]
     *
     * @param bool $dryRun Пробный прогон без сохранения изменений
     *
     * @return int
     * @throws Exception
     */
    public function actionRun(bool $dryRun = false): int
    {
        Console::output(Console::ansiFormat("=== Printer Matching ===", [Console::FG_CYAN, Console::BOLD]));

        $discovered = DiscoveredPrinter::find()
            ->where([
                'OR',
                ['matched_device_id' => null],
                ['matched_device_id' => 0],
            ])
            ->all();

        $matched   = 0;
        $unmatched = 0;
        $updated   = 0;

        foreach ($discovered as $dp) {
            $device = $this->findMatch($dp);

            if ($device) {
                if (!$dryRun) {
                    $dp->matched_device_id = $device->id;
                    $dp->save(false);

                    // Обновляем mac_address в device, если пустой
                    if (empty($device->mac_address) && !empty($dp->mac_address)) {
                        $device->mac_address = $dp->mac_address;
                        $device->save(false);
                        $updated++;
                    }
                }

                Console::output(Console::ansiFormat(
                    "✓ MATCH: {$dp->ip} → {$device->name} (ID: {$device->id})",
                    [Console::FG_GREEN]
                ));
                $matched++;
            } else {
                Console::output(Console::ansiFormat(
                    "? UNMATCHED: {$dp->ip} {$dp->snmp_name} {$dp->guessed_model}",
                    [Console::FG_YELLOW]
                ));
                $unmatched++;
            }
        }

        Console::output("");
        Console::output(Console::ansiFormat("=== Results ===", [Console::FG_CYAN, Console::BOLD]));
        Console::output("Total discovered: " . count($discovered));
        Console::output(Console::ansiFormat("Matched: {$matched}", [Console::FG_GREEN]));
        Console::output(Console::ansiFormat("Unmatched: {$unmatched}", [Console::FG_YELLOW]));
        Console::output("Devices updated: {$updated}");

        if ($dryRun) {
            Console::output(Console::ansiFormat("[DRY RUN — no changes saved]", [Console::FG_PURPLE, Console::BOLD]));
        }

        return 0;
    }

    /**
     * Показать несопоставленные принтеры.
     *
     * Usage:
     *   php yii printer-match/unmatched
     */
    public function actionUnmatched(): int
    {
        $unmatched = DiscoveredPrinter::find()
            ->where([
                'OR',
                ['matched_device_id' => null],
                ['matched_device_id' => 0],
            ])
            ->orderBy('ip')
            ->all();

        Console::output(Console::ansiFormat("=== Unmatched Printers ===", [Console::FG_CYAN, Console::BOLD]));
        Console::output(sprintf(
            "%-15s %-25s %-30s %-18s %-15s",
            'IP', 'Name', 'Model', 'MAC', 'Serial'
        ));
        Console::output(str_repeat('-', 110));

        foreach ($unmatched as $dp) {
            Console::output(sprintf(
                "%-15s %-25s %-30s %-18s %-15s",
                $dp->ip,
                $dp->snmp_name ?: '—',
                $dp->guessed_model ?: '—',
                $dp->mac_address ?: '—',
                $dp->serial_snmp ?: '—'
            ));
        }

        Console::output(str_repeat('-', 110));
        Console::output("Total: " . count($unmatched));

        return 0;
    }

    /**
     * Ручное сопоставление принтера с устройством.
     *
     * Usage:
     *   php yii printer-match/manual <discovered_id> <device_id>
     *
     * @param int $discoveredId
     * @param int $deviceId
     *
     * @return int
     * @throws Exception
     */
    public function actionManual(int $discoveredId, int $deviceId): int
    {
        $dp = DiscoveredPrinter::findOne($discoveredId);
        $device = Device::findOne($deviceId);

        if (!$dp || !$device) {
            Console::stderr(Console::ansiFormat("Invalid IDs\n", [Console::FG_RED]));
            return 1;
        }

        $dp->matched_device_id = $device->id;
        $dp->save(false);

        if (empty($device->mac_address) && !empty($dp->mac_address)) {
            $device->mac_address = $dp->mac_address;
            $device->save(false);
        }

        Console::output(Console::ansiFormat(
            "Linked: {$dp->ip} → {$device->name} (ID: {$device->id})",
            [Console::FG_GREEN]
        ));

        return 0;
    }

    /**
     * Сбросить сопоставление для принтера.
     *
     * Usage:
     *   php yii printer-match/reset <discovered_id>
     *
     * @param int $discoveredId
     *
     * @return int
     * @throws Exception
     */
    public function actionReset(int $discoveredId): int
    {
        $dp = DiscoveredPrinter::findOne($discoveredId);

        if (!$dp) {
            Console::stderr(Console::ansiFormat("Discovered printer not found\n", [Console::FG_RED]));
            return 1;
        }

        $dp->matched_device_id = null;
        $dp->save(false);

        Console::output(Console::ansiFormat(
            "Reset match for: {$dp->ip}",
            [Console::FG_GREEN]
        ));

        return 0;
    }

    /**
     * Поиск соответствия по приоритетам.
     *
     * @param DiscoveredPrinter $dp
     *
     * @return Device|null
     */
    private function findMatch(DiscoveredPrinter $dp): ?Device
    {
        // 1. По MAC-адресу (самый точный)
        if (!empty($dp->mac_address)) {
            $byMac = Device::find()
                ->where(['mac_address' => $dp->mac_address])
                ->one();
            if ($byMac) {
                return $byMac;
            }
        }

        // 2. По серийному номеру
        if (!empty($dp->serial_snmp) && !preg_match('/^0+$/', $dp->serial_snmp)) {
            $bySerial = Device::find()
                ->where(['serial_number' => $dp->serial_snmp])
                ->one();
            if ($bySerial) {
                return $bySerial;
            }
        }

        // 3. По IP-адресу
        if (!empty($dp->ip)) {
            $byIp = Device::find()
                ->where(['LIKE', 'comment', $dp->ip])
                ->one();
            if ($byIp) {
                return $byIp;
            }
        }

        // 4. По имени (fuzzy) — если модель совпадает
        if (!empty($dp->snmp_name) && !empty($dp->guessed_model)) {
            $byName = Device::find()
                ->where(['ilike', 'name', $dp->snmp_name])
                ->orWhere(['ilike', 'name', str_replace('-', ' ', $dp->snmp_name)])
                ->all();

            if (count($byName) === 1) {
                $dev = $byName[0];
                $devModel = $dev->model ? $dev->model->name : '';
                if (
                    stripos($dp->guessed_model, $devModel) !== false
                    || stripos($devModel, $dp->guessed_model) !== false
                ) {
                    return $dev;
                }
            }
        }

        return null;
    }
}
