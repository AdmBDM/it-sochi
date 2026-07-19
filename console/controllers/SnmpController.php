<?php
// console/controllers/SnmpController.php
namespace console\controllers;

use common\models\Device;
use common\models\DiscoveredPrinter;
use common\models\PrinterPageCounter;
use common\services\output\ConsoleOutput;
use common\services\output\OutputInterface;
use common\services\snmp\SnmpClient;
use common\services\snmp\discovery\DiscoveryService;
use Yii;
use yii\console\Controller;
use yii\db\Exception;
use yii\db\Expression;

class SnmpController extends Controller
{
    /**
     * @return OutputInterface
     */
    private function output(): OutputInterface
    {
        return new ConsoleOutput($this);
    }

    /**
     * @param null $deviceId
     *
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
                    $this->output()->info("✓ $ip");
                }
            }
        }

        $this->output()->info("\nScanned: $total, Found: " . count($found));
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

        $this->output()->info("SNMP discovery started for 192.168.88.0/21");
        $this->output()->info("Threads: $threads\n");

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
                $this->output()->info("\rProgress: $processed/$total ($percent%) | Found: " . count($found));
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
                $ids = $snmp->getPrinterIdentifiers($ip);

                $printer = [
                    'ip' => $ip,
                    'name' => $name,
                    'descr' => trim($descr, '"'),
                    'mac' => $ids['mac'],        // ←
                    'serial' => $ids['serial'],  // ←
                ];
                $found[] = $printer;

                $this->output()->info("✓ Found: $ip - {$printer['name']}");
            }
        }

        $this->output()->info("\r=== Results ===");
        $this->output()->info("Scanned: $total");
        $this->output()->info("Found: " . count($found));

        if (!empty($found)) {
            $discovery = new DiscoveryService(
                $this->output()
            );
            $discovery->saveDiscovered($found, 'snmp');
        }

        return 0;
    }

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
                'dp.ip',
            ])
            ->leftJoin(
                DiscoveredPrinter::tableName() . ' dp',
                'dp.matched_device_id = d.id'
            )
            ->where(['d.is_active' => true])
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
                    $this->output()->error(sprintf(
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
                        $this->output()->error(sprintf(
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
                        $this->output()->info(sprintf(
                            "[%d] %-35s %10d pages\n",
                            $device->id,
                            $device->name,
                            $totalPages
                        ));
                    }

                } else {

                    ++$failed;

                    if ($verbose) {
                        $this->output()->error(sprintf(
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
                    $this->output()->error(sprintf(
                        "[%d] %s: %s\n",
                        $device->id,
                        $device->name,
                        $e->getMessage()
                    ));
                }
            }
        }

        $this->output()->success(PHP_EOL);
        $this->output()->success("Processed : {$processed}");
        $this->output()->success("Saved     : {$saved}");
        $this->output()->success("Failed    : {$failed}");

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
