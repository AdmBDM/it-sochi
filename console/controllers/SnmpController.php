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
        $this->stdout("SNMP discovery started for 192.168.88.0/21\n");
        $this->stdout("Threads: $threads\n\n");

        // Генерируем все IP /21
        $ips = [];
        for ($third = 88; $third <= 95; $third++) {
            for ($fourth = 1; $fourth <= 254; $fourth++) {
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

            if ($descr && preg_match('/(printer|kyocera|hp|brother|canon|xerox|ricoh|epson)/i', $descr)) {
                $name = @snmpget($ip, 'public', '1.3.6.1.2.1.1.5.0', 300000, 1);

                $printer = [
                    'ip' => $ip,
                    'name' => $name ? trim($name, '"') : 'Unknown',
                    'descr' => trim($descr, '"'),
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
        foreach ($found as $printer) {
            $model = DiscoveredPrinter::findOne(['ip' => $printer['ip']]) ?? new DiscoveredPrinter();

            $model->attributes = [
                'ip' => $printer['ip'],
                'snmp_name' => $printer['name'] ?? null,
                'snmp_descr' => $printer['descr'] ?? null,
                'guessed_model' => $this->guessModel($printer['descr'] ?? ''),
                'discovered_at' => date('Y-m-d H:i:s'),
                'source' => $source,
                'is_local' => false,
                'matched_device_id' => null, // сопоставляется вручную или автоматически
            ];

            if (!$model->save()) {
                Yii::error("Failed to save discovered printer {$printer['ip']}: " . json_encode($model->errors));
            }
        }

        $this->stdout("Saved to: $reportFile\n");
        $this->stdout("Database: " . count($found) . " records\n");
    }
}
