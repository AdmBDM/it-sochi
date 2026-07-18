<?php
// console/controllers/SnmpController.php
namespace console\controllers;

use common\models\Device;
use common\models\DiscoveredPrinter;
use common\models\PrinterPageCounter;
use common\services\snmp\SnmpClient;
use Yii;
use yii\console\Controller;
use yii\db\Exception;
use yii\db\Expression;

class SnmpController extends Controller
{
//    /**
//     * yii snmp/poll [--device-id=123] [--verbose]
//     * @param $deviceId
//     * @param $verbose
//     *
//     * @return void
//     * @throws Exception
//     */
//    public function actionPoll($deviceId = null, $verbose = false): void
//    {
//        $query = Device::find()
//            ->where(['IS NOT', 'snmp_ip', null])
//            ->andWhere(['IS NOT', 'printer_metrics', null]) // только принтеры
//            ->andWhere(['is_active' => true]);
//
//        if ($deviceId) $query->andWhere(['id' => $deviceId]);
//
//        $snmp = new SnmpClient();
//
//        foreach ($query->each() as $device) {
//            try {
////                $data = $this->snmpWalk($device->snmp_ip, $device->snmp_community);
//                $data = $snmp->getPrinterCounters(
//                    $device->snmp_ip,
//                    $device->snmp_community ?: 'public'
//                );
//
//                if ($data['total_pages'] === null) {
//                    $verbose && $this->stderr("{$device->name}: SNMP недоступен\n");
//                    continue;
//                }
//
//                // Сохраняем в историю
//                $counter = new PrinterPageCounter([
//                    'device_id' => $device->id,
//                    'total_pages' => $data['total_pages'],
//                    'color_pages' => $data['color_pages'] ?? 0,
//                    'bw_pages' => ($data['total_pages'] - ($data['color_pages'] ?? 0)),
//                    'source' => 'snmp',
//                ]);
//                $counter->save();
//
//                // Обновляем текущие метрики в devices
//                $device->updateAttributes([
//                    'printer_metrics' => json_encode([
//                        'total_pages' => $data['total_pages'],
//                        'color_pages' => $data['color_pages'] ?? 0,
//                        'last_reading_at' => date('c'),
//                        'toner_level' => $data['toner_black'] ?? null, // % остатка
//                    ])
//                ]);
//
//                $verbose && $this->stdout("{$device->name}: {$data['total_pages']} стр.\n");
//
//            } catch (Exception $e) {
//                $verbose && $this->stderr("{$device->name}: {$e->getMessage()}\n");
//            }
//        }
//    }

    /**
     * @param null $deviceId
     * @return int
     */
    public function actionPoll(null $deviceId = null): int
    {
        return $this->actionCollectCounters(
            $deviceId,
            false
        );
    }

    /**
     * @param string $ip
     * @param string $community
     *
     * @return array
     */
//    private function snmpWalk(string $ip, string $community): array
//    {
//        // OID'ы для большинства принтеров (HP, Kyocera, Brother)
//        $oids = [
//            'total_pages' => '1.3.6.1.2.1.43.10.2.1.4.1.1', // prtMarkerLifeCount
//            'color_pages' => '1.3.6.1.4.1.11.2.3.9.4.2.1.4.1.2.6.0', // HP specific, fallback needed
//            'toner_black' => '1.3.6.1.2.1.43.11.1.1.9.1.1', // % остатка тонера
//        ];
//
//        $result = [];
//        foreach ($oids as $key => $oid) {
//            $value = @snmpget($ip, $community, $oid, 1000000, 1);
//            $result[$key] = $value !== false ? (int)$value : null;
//        }
//
//        return $result;
//    }

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
     * @param int $threads - период обновления
     *
     * @return int
     * @throws Exception
     */
    public function actionDiscoverNetwork(int $threads = 64): int
    {
        snmp_set_quick_print(true);
        snmp_set_enum_print(true);
        snmp_set_oid_numeric_print(true);

        $this->stdout("SNMP discovery started for 192.168.88.0/21\n");
        $this->stdout("Threads: $threads\n\n");

        // Генерируем все IP /21
        $ips = [];
//        $initOct = 88;
//        $endOct = 95;
//        $initOct4 = 1;
//        $endOct4 = 254;
        $initOct = 90;
        $endOct = 90;
        $initOct4 = 200;
        $endOct4 = 254;
        for ($third = $initOct; $third <= $endOct; $third++) {
            for ($fourth = $initOct4; $fourth <= $endOct4; $fourth++) {
                $ips[] = "192.168.$third.$fourth";
            }
        }

        $total = count($ips);
        $found = [];
        $processed = 0;
        $snmp = new SnmpClient();

        foreach ($ips as $ip) {
            $processed++;

            // Прогресс каждые $threads адреса
            if ($processed % $threads === 0) {
                $percent = round($processed / $total * 100);
                $this->stdout("\rProgress: $processed/$total ($percent%) | Found: " . count($found));
            }

            // SNMP-запрос с таймаутом 300ms
            $descr = @snmpget($ip, 'public', '1.3.6.1.2.1.1.1.0', 300000, 1);
            if (!$descr) {
                $descr = @snmp2_get($ip, 'public', '1.3.6.1.2.1.1.1.0', 300000, 1);
            }

            // Исключаем роутеры и сетевое оборудование по описанию
            if ($descr && preg_match('/(routeros|mikrotik|cisco|ubiquiti|unifi|tp-link|d-link|netgear|juniper|fortinet|pfsense|opnsense|synology|qnap|asus|huawei|zyxel)/i', $descr)) {
                continue;
            }

            if ($descr && preg_match('/(printer|print|mfp|copier|fax|scanner|kyocera|hp|hewlett|brother|canon|xerox|ricoh|epson|tsc|zebra|godex|argox|dymo|sato|datamax|intermec|honeywell|toshiba|samsung|lexmark|dell|konica|minolta|sharp|panasonic|oki|fuji|phaser|workcentre|ecosys|laserjet|deskjet|officejet|pixma|imageclass|label|barcode|receipt|thermal|pos|ttp|te|tdp)/i', $descr)) {
                $name = @snmpget($ip, 'public', '1.3.6.1.2.1.1.5.0', 300000, 1);
                $name = $name ? trim($name, '"') : 'Unknown';
                if (!$name) {
                    $name = @snmp2_get($ip, 'public', '1.3.6.1.2.1.1.5.0', 300000, 1);
                }
                if (empty($name)) {
                    $name = gethostbyaddr($ip);
                    if ($name === $ip) $name = '';
                }

                // ← ДОБАВЛЯЕМ идентификаторы
//                $ids = $this->getPrinterIdentifiers($ip);
                $ids = $snmp->getPrinterIdentifiers($ip);

                $printer = [
                    'ip' => $ip,
//                    'name' => $name ? trim($name, '"') : 'Unknown',
                    'name' => $name,
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
        $snmp = new SnmpClient();

        $reportFile = Yii::getAlias("@runtime/printers_discovered_{$source}_" . date('Ymd_His') . '.json');
        file_put_contents($reportFile, json_encode($found, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Сохранение в БД для сопоставления
        $newCount = 0;
        $updatedCount = 0;

        foreach ($found as $printer) {
//            $ids = $this->getPrinterIdentifiers($printer['ip']);
            $ids = $snmp->getPrinterIdentifiers($printer['ip']);

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

            // Полоритет имени: SNMP → MikroTik DHCP → DNS
            $ip = $printer['ip'];
            $name = @snmpget($ip, 'public', '1.3.6.1.2.1.1.5.0', 300000, 1);
            $name = $name ? trim($name, '"') : '';

            // Fallback на MikroTik DHCP, если SNMP-имя пустое
            if (empty($name)) {
                $mikrotikName = $this->getHostnameFromMikrotik($ip);
                if ($mikrotikName) {
                    $name = $mikrotikName;
                }
            }

            // Fallback на DNS, если и MikroTik не дал имя
            if (empty($name)) {
                $dnsName = gethostbyaddr($ip);
                if ($dnsName !== $ip) {
                    $name = $dnsName;
                }
            }

            // Прямое присвоение вместо attributes()
            $model->ip = $printer['ip'];
            $model->mac_address = $printer['mac'] ?? $ids['mac'];
            $model->serial_snmp = $printer['serial'] ?? $ids['serial'];
//            $model->snmp_name = $printer['name'];
            if (!empty($name)) {
                $model->snmp_name = $name;
            } elseif (empty($model->snmp_name)) {
                // Только если и старое пустое — оставляем пустым
                $model->snmp_name = null;
            }
            $model->snmp_descr = $printer['descr'];
            $model->guessed_model = $this->guessModel($printer['descr']);
//            $model->discovered_at = date('Y-m-d H:i:s');
            $model->last_seen_at = date('Y-m-d H:i:s');
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
     * Получает имя хоста из MikroTik DHCP lease по IP
     * @param string $ip
     * @return string|null
     */
    private function getHostnameFromMikrotik(string $ip): ?string
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $cmd = 'ssh admbdm@192.168.88.1 "/ip dhcp-server lease print detail where address=' . $ip . '" 2>/dev/null';

        $output = shell_exec($cmd);
        if (empty($output) || str_contains($output, 'syntax error') || str_contains($output, 'bad command')) {
            return null;
        }

        // Парсим host-name="..." из вывода
        if (preg_match('/host-name="([^"]+)"/i', $output, $m)) {
            return trim($m[1]);
        }

        return null;
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
            '/(TE\d+|TTP\d+|TDP\d+)\s+Version/i' => 'TSC $1',
            '/Zebra\s+(ZT\d+|GX\d+|ZD\d+|LP\d+|TLP\d+)/i' => 'Zebra $1',
            '/Zebra/i' => 'Zebra',
            '/Godex\s+(EZ\d+|G\d+|RT\d+)/i' => 'Godex $1',
            '/Godex/i' => 'Godex',
            '/Argox\s+(OS-\d+|CP-\d+|F1|X\d+)/i' => 'Argox $1',
            '/Argox/i' => 'Argox',
            '/Dymo\s+(\w+)/i' => 'Dymo $1',
            '/Dymo/i' => 'Dymo',
            '/Sato\s+(CL\d+|GL\d+|GT\d+|LM\d+)/i' => 'Sato $1',
            '/Sato/i' => 'Sato',
            '/Datamax\s+([A-Z0-9-]+)/i' => 'Datamax $1',
            '/Intermec\s+([A-Z0-9-]+)/i' => 'Intermec $1',
            '/Honeywell\s+([A-Z0-9-]+)/i' => 'Honeywell $1',
            '/Barcode\s+Printer/i' => 'Barcode Printer',
            '/Thermal\s+Printer/i' => 'Thermal Printer',
            '/Label\s+Printer/i' => 'Label Printer',
            '/Receipt\s+Printer/i' => 'Receipt Printer',
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
//    private function getPrinterIdentifiers(string $ip): array
//    {
//        $result = [
//            'mac' => null,
//            'serial' => null,
//        ];
//
//        // 1. MAC через ARP (надёжнее SNMP для MAC)
//        $arp = shell_exec("ip neigh show $ip 2>/dev/null");
//        if (preg_match('/([0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2})/i', $arp, $m)) {
//            $result['mac'] = strtolower($m[1]);
//        }
//
//        // 2. Серийный номер через SNMP
//        // prtGeneralSerialNumber (стандарт Printer MIB)
//        $oids = [
//            '1.3.6.1.2.1.43.5.1.1.17.1',      // стандартный
//            '1.3.6.1.4.1.1347.43.5.1.1.28.1',  // Kyocera
//            '1.3.6.1.4.1.11.2.3.9.4.2.1.1.3.0', // HP
//            '1.3.6.1.4.1.2435.2.3.9.4.2.1.5.5.1.0', // Brother
//        ];
//
//        foreach ($oids as $oid) {
//            $raw = @snmpget($ip, 'public', $oid, 200000, 1);
//            if ($raw) {
//                $serial = trim($raw, '" ');
//                if (!empty($serial) && $serial !== 'NULL') {
//                    $result['serial'] = $serial;
//                    break;
//                }
//            }
//        }
//
//        return $result;
//    }

    /**
     * Опрос счётчиков страниц сетевых принтеров.
     *
     * Usage:
     *   php yii snmp/collect-counters
     *   php yii snmp/collect-counters --verbose=1
     *   php yii snmp/collect-counters --device-id=123
     *
     * @param int|null $deviceId
     * @param bool $verbose
     *
     * @return int
     */
    public function actionCollectCounters(
        ?int $deviceId = null,
//        bool $verbose = false
        bool $verbose = true
    ): int
    {
        $query = Device::find()
            ->alias('d')
            ->select([
                'd.*',
//                'discovered_ip' => 'dp.ip',
                'dp.ip',
            ])
            ->leftJoin(
                DiscoveredPrinter::tableName() . ' dp',
                'dp.matched_device_id = d.id'
            )
            ->where(['d.is_active' => true])
//            ->andWhere([
//                'or',
//                ['IS NOT', 'dp.id', null],
//                ['IS NOT', 'd.snmp_ip', null],   // резервный вариант
//            ]);
            ->andWhere(['IS NOT', 'dp.id', null]);

        if ($deviceId !== null) {
            $query->andWhere(['d.id' => $deviceId]);
        }

        $snmp = new SnmpClient();

        $processed = 0;
        $saved = 0;
        $failed = 0;

        foreach ($query->each() as $device) {

            /** @var Device $device */

            ++$processed;

            // Используем самый актуальный IP
            $ip = $device->getAttribute('discovered_ip') ?: $device->snmp_ip;

            if (empty($ip)) {

                ++$failed;

                if ($verbose) {
                    $this->stderr(sprintf(
                        "[%d] %s: SNMP IP not found\n",
                        $device->id,
                        $device->name
                    ));
                }

                continue;
            }

            $community = $device->snmp_community ?: 'public';

            try {

                $counters = $snmp->getPrinterCounters(
                    $ip,
                    $community
                );

                $totalPages = $counters['total_pages'] ?? null;

                if ($totalPages === null || $totalPages < 0) {

                    ++$failed;

                    if ($verbose) {
                        $this->stderr(sprintf(
                            "[%d] %s (%s): SNMP unavailable\n",
                            $device->id,
                            $device->name,
                            $ip
                        ));
                    }

                    continue;
                }

                if ($this->saveCounters($device, $counters)) {

                    ++$saved;

                    if ($verbose) {
                        $this->stdout(sprintf(
                            "[%d] %-35s %10d pages\n",
                            $device->id,
                            $device->name,
                            $totalPages
                        ));
                    }

                } else {

                    ++$failed;

                    if ($verbose) {
                        $this->stderr(sprintf(
                            "[%d] %s: database error\n",
                            $device->id,
                            $device->name
                        ));
                    }
                }

            } catch (\Throwable $e) {

                ++$failed;

                Yii::error($e);

                if ($verbose) {
                    $this->stderr(sprintf(
                        "[%d] %s: %s\n",
                        $device->id,
                        $device->name,
                        $e->getMessage()
                    ));
                }
            }
        }

        $this->stdout(PHP_EOL);
        $this->stdout("Processed : {$processed}\n");
        $this->stdout("Saved     : {$saved}\n");
        $this->stdout("Failed    : {$failed}\n");

        return self::EXIT_CODE_NORMAL;

    }

    /**
     * Сохранение счётчиков страниц.
     *
     * @param Device $device
     * @param array $counters
     *
     * @return bool
     * @throws Exception
     */
    private function saveCounters(
        Device $device,
        array $counters
    ): bool
    {
        $model = new PrinterPageCounter();

        $model->device_id = $device->id;
        $model->total_pages = $counters['total_pages'];
        $model->color_pages = $counters['color_pages'];
        $model->bw_pages = $counters['bw_pages'];
        $model->source = 'snmp';
        $model->captured_at = new Expression('NOW()');

        if ($model->save(false)) {
            return true;
        }

        Yii::error([
            'device_id' => $device->id,
            'errors'    => $model->errors,
        ]);

        return false;
    }

}
