<?php
// console/controllers/SnmpController.php
namespace console\controllers;

use common\models\Device;
use common\models\DiscoveredPrinter;
use common\models\PrinterPageCounter;
use Yii;
use yii\console\Controller;
use yii\db\Exception;

class SnmpController extends Controller
{
    /**
     * yii snmp/poll [--device-id=123] [--verbose]
     * @param $deviceId
     * @param $verbose
     *
     * @return void
     * @throws Exception
     */
    public function actionPoll($deviceId = null, $verbose = false): void
    {
        $query = Device::find()
            ->where(['IS NOT', 'snmp_ip', null])
            ->andWhere(['IS NOT', 'printer_metrics', null]) // только принтеры
            ->andWhere(['is_active' => true]);

        if ($deviceId) $query->andWhere(['id' => $deviceId]);

        foreach ($query->each() as $device) {
            try {
                $data = $this->snmpWalk($device->snmp_ip, $device->snmp_community);

                if ($data['total_pages'] === null) {
                    $verbose && $this->stderr("{$device->name}: SNMP недоступен\n");
                    continue;
                }

                // Сохраняем в историю
                $counter = new PrinterPageCounter([
                    'device_id' => $device->id,
                    'total_pages' => $data['total_pages'],
                    'color_pages' => $data['color_pages'] ?? 0,
                    'bw_pages' => ($data['total_pages'] - ($data['color_pages'] ?? 0)),
                    'source' => 'snmp',
                ]);
                $counter->save();

                // Обновляем текущие метрики в devices
                $device->updateAttributes([
                    'printer_metrics' => json_encode([
                        'total_pages' => $data['total_pages'],
                        'color_pages' => $data['color_pages'] ?? 0,
                        'last_reading_at' => date('c'),
                        'toner_level' => $data['toner_black'] ?? null, // % остатка
                    ])
                ]);

                $verbose && $this->stdout("{$device->name}: {$data['total_pages']} стр.\n");

            } catch (Exception $e) {
                $verbose && $this->stderr("{$device->name}: {$e->getMessage()}\n");
            }
        }
    }

    /**
     * @param string $ip
     * @param string $community
     *
     * @return array
     */
    private function snmpWalk(string $ip, string $community): array
    {
        // OID'ы для большинства принтеров (HP, Kyocera, Brother)
        $oids = [
            'total_pages' => '1.3.6.1.2.1.43.10.2.1.4.1.1', // prtMarkerLifeCount
            'color_pages' => '1.3.6.1.4.1.11.2.3.9.4.2.1.4.1.2.6.0', // HP specific, fallback needed
            'toner_black' => '1.3.6.1.2.1.43.11.1.1.9.1.1', // % остатка тонера
        ];

        $result = [];
        foreach ($oids as $key => $oid) {
            $value = @snmpget($ip, $community, $oid, 1000000, 1);
            $result[$key] = $value !== false ? (int)$value : null;
        }

        return $result;
    }

    /**
     * @return void
     */
    public function actionDiscover(): void
    {
        // /21 = 2046 адресов: 192.168.88.0 - 192.168.95.255
        // Пропускаем .0 и .255 в каждой /24
        $found = [];
        $total = 0;

        for ($third = 88; $third <= 95; $third++) {
            for ($fourth = 1; $fourth <= 254; $fourth++) {
                $ip = "192.168.$third.$fourth";
                $total++;

                // Быстрый check с таймаутом 200ms
                $descr = @snmpget($ip, 'public', '1.3.6.1.2.1.1.1.0', 200000, 1);

                if ($descr && preg_match('/(printer|kyocera|hp|brother|canon|xerox)/i', $descr)) {
                    $name = @snmpget($ip, 'public', '1.3.6.1.2.1.1.5.0', 200000, 1);
                    $found[] = [
                        'ip' => $ip,
                        'name' => $name ? trim($name, '"') : 'Unknown',
                        'descr' => trim($descr, '"'),
                    ];
                    $this->stdout("✓ $ip\n");
                }
            }
        }

        $this->stdout("\nScanned: $total, Found: " . count($found) . "\n");
        file_put_contents(Yii::getAlias('@runtime/printers_discovered.json'), json_encode($found, JSON_PRETTY_PRINT));
    }

    /**
     * @param int $threads
     *
     * @return int
     */
    public function actionDiscoverNetwork(int $threads = 50): int
    {
        snmp_set_quick_print(true);
        snmp_set_enum_print(true);
        snmp_set_oid_numeric_print(true);

        $this->stdout("SNMP discovery started for 192.168.88.0/21\n");
        $this->stdout("Threads: $threads\n\n");

        // Генерируем все IP /21
        $ips = [];
        $initOct = 88;
        $endOct = 95;
        $initOct4 = 1;
        $endOct4 = 254;
//        $initOct = 90;
//        $endOct = 90;
//        $initOct4 = 200;
//        $endOct4 = 254;
        for ($third = $initOct; $third <= $endOct; $third++) {
            for ($fourth = $initOct4; $fourth <= $endOct4; $fourth++) {
                $ips[] = "192.168.$third.$fourth";
            }
        }

        $total = count($ips);
        $found = [];
        $processed = 0;

        foreach ($ips as $ip) {
            $processed++;

            // Прогресс каждые 50 адресов
            if ($processed % 50 === 0) {
                $percent = round($processed / $total * 100);
                $this->stdout("\rProgress: $processed/$total ($percent%) | Found: " . count($found));
            }

            // SNMP-запрос с таймаутом 300ms
            $descr = @snmpget($ip, 'public', '1.3.6.1.2.1.1.1.0', 300000, 1);

            if ($descr && preg_match('/(printer|kyocera|hp|brother|canon|xerox|ricoh|epson|tsc|barcode|label|zebra|godex|argox|te210|te310|ttp|tdp)/i', $descr)) {
                $name = @snmpget($ip, 'public', '1.3.6.1.2.1.1.5.0', 300000, 1);

                // ← ДОБАВЛЯЕМ идентификаторы
                $ids = $this->getPrinterIdentifiers($ip);

                $printer = [
                    'ip' => $ip,
                    'name' => $name ? trim($name, '"') : 'Unknown',
                    'descr' => trim($descr, '"'),
                    'mac' => $ids['mac'],        // ←
                    'serial' => $ids['serial'],  // ←
                ];
                $found[] = $printer;

                $this->stdout("\n✓ Found: $ip - {$printer['name']}\n");
            }
        }

        $this->stdout("\r\n\n=== Results ===\n");
        $this->stdout("Scanned: $total\n");
        $this->stdout("Found: " . count($found) . "\n");

        if (!empty($found)) {
            $this->saveDiscovered($found, 'snmp');
        }

        return 0;
    }

    /**
     * @param array $found
     * @param string $source
     *
     * @return void
     * @throws Exception
     */
    private function saveDiscovered(array $found, string $source): void
    {
        $reportFile = Yii::getAlias("@runtime/printers_discovered_{$source}_" . date('Ymd_His') . '.json');
        file_put_contents($reportFile, json_encode($found, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Сохранение в БД для сопоставления
        $newCount = 0;
        $updatedCount = 0;

        foreach ($found as $printer) {
            $ids = $this->getPrinterIdentifiers($printer['ip']);

            // Ищем по приоритету: MAC → Serial → IP
            $model = null;

            if ($ids['mac']) {
                $model = DiscoveredPrinter::findOne(['mac_address' => $ids['mac']]);
            }

//            if (!$model && $ids['serial']) {
//                $model = DiscoveredPrinter::findOne(['serial_snmp' => $ids['serial']]);
//            }
            if (!$model && $ids['serial'] && !preg_match('/^0+$/', $ids['serial'])) {
                $model = DiscoveredPrinter::findOne(['serial_snmp' => $ids['serial']]);
            }

            if (!$model) {
                $model = DiscoveredPrinter::findOne(['ip' => $printer['ip']]);
            }

            $isNew = $model === null;
            if ($isNew) {
                $model = new DiscoveredPrinter();
                $newCount++;
            } else {
                $updatedCount++;
                // Сохраняем предыдущий IP перед обновлением
                if ($model->ip !== $printer['ip']) {
                    $model->last_ip = $model->ip;
                }
            }

            // Прямое присвоение вместо attributes()
            $model->ip = $printer['ip'];
            $model->mac_address = $printer['mac'] ?? $ids['mac'];
            $model->serial_snmp = $printer['serial'] ?? $ids['serial'];
            $model->snmp_name = $printer['name'];
            $model->snmp_descr = $printer['descr'];
            $model->guessed_model = $this->guessModel($printer['descr']);
            $model->discovered_at = date('Y-m-d H:i:s');
            $model->source = $source ?? '---';
            $model->is_local = false;

            if (!$model->save()) {
                Yii::error("Failed to save {$printer['ip']}: " . json_encode($model->errors));
                $this->stderr("FAIL {$printer['ip']}: " . json_encode($model->errors) . "\n");
//            } else {
//                $this->stdout("OK {$printer['ip']} id={$model->id}\n");
            }
        }

        $this->stdout("Saved to: $reportFile\n");
        $this->stdout("Database: " . count($found) . " records\n");
        $this->stdout("\nNew: $newCount, Updated: $updatedCount\n");
    }

    /**
     * @param string $descr
     * @return string|null
     */
    private function guessModel(string $descr): ?string
    {
        $patterns = [
            '/Kyocera\s+(ECOSYS\s+[A-Z0-9]+)/i' => '$1',
            '/Kyocera\s+([A-Z0-9]+)/i' => 'Kyocera $1',
            '/HP\s+(LaserJet\s+\w+|M\d+[a-z]*)/i' => 'HP $1',
            '/Hewlett-Packard.*(LaserJet|MFP|M\d+)/i' => 'HP $1',
            '/Brother\s+(HL-[A-Z0-9]+|DCP-[A-Z0-9]+|MFC-[A-Z0-9]+)/i' => 'Brother $1',
            '/Brother\s+([A-Z0-9]+)/i' => 'Brother $1',
            '/Canon\s+(i-SENSYS\s+[A-Z0-9]+|LBP\d+|MF\d+)/i' => 'Canon $1',
            '/Xerox\s+(Phaser\s+\d+|WorkCentre\s+\d+)/i' => 'Xerox $1',
            '/Ricoh\s+(SP\s+\d+|MP\s+\w+)/i' => 'Ricoh $1',
            '/Epson\s+(WorkForce|AcuLaser\s+\w+)/i' => 'Epson $1',
            '/TSC\s+(TE\d+|TTP\d+|TDP\d+)/i' => 'TSC $1',
            '/Barcode\s+Printer/i' => 'Barcode Printer',
        ];

        foreach ($patterns as $regex => $replacement) {
            if (preg_match($regex, $descr, $m)) {
                return preg_replace($regex, $replacement, $descr);
            }
        }

        return null;
    }

    /**
     * @param string $ip
     *
     * @return null[]
     */
    private function getPrinterIdentifiers(string $ip): array
    {
        $result = [
            'mac' => null,
            'serial' => null,
        ];

        // 1. MAC через ARP (надёжнее SNMP для MAC)
        $arp = shell_exec("ip neigh show $ip 2>/dev/null");
        if (preg_match('/([0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2})/i', $arp, $m)) {
            $result['mac'] = strtolower($m[1]);
        }

        // 2. Серийный номер через SNMP
        // prtGeneralSerialNumber (стандарт Printer MIB)
        $oids = [
            '1.3.6.1.2.1.43.5.1.1.17.1',      // стандартный
            '1.3.6.1.4.1.1347.43.5.1.1.28.1',  // Kyocera
            '1.3.6.1.4.1.11.2.3.9.4.2.1.1.3.0', // HP
            '1.3.6.1.4.1.2435.2.3.9.4.2.1.5.5.1.0', // Brother
        ];

        foreach ($oids as $oid) {
            $raw = @snmpget($ip, 'public', $oid, 200000, 1);
            if ($raw) {
                $serial = trim($raw, '" ');
                if (!empty($serial) && $serial !== 'NULL') {
                    $result['serial'] = $serial;
                    break;
                }
            }
        }

        return $result;
    }

}
