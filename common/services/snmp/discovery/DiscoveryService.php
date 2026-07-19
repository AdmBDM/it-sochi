<?php

namespace common\services\snmp\discovery;

use common\models\DiscoveredPrinter;
use common\services\output\NullOutput;
use common\services\output\OutputInterface;
use common\services\snmp\SnmpClient;
use Yii;
use yii\db\Exception;

class DiscoveryService
{
    /**
     * @param OutputInterface $output
     */
    public function __construct(
        private readonly OutputInterface $output = new NullOutput()
    ) {
    }

    /**
     * Определение предполагаемой модели устройства по sysDescr.
     *
     * @param string $descr
     * @return string|null
     */
    public function guessModel(string $descr): ?string
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
            if (preg_match($regex, $descr)) {
                return preg_replace($regex, $replacement, $descr);
            }
        }

        return null;
    }

    /**
     * Получает имя хоста из MikroTik DHCP lease по IP
     * @param string $ip
     * @return string|null
     */
    public function getHostnameFromMikrotik(string $ip): ?string
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
     * @param array $found
     * @param string $source
     *
     * @return void
     * @throws Exception
     */
    public function saveDiscovered(array $found, string $source): void
    {
        $snmp = new SnmpClient();

        $reportFile = Yii::getAlias("@runtime/printers_discovered_{$source}_" . date('Ymd_His') . '.json');
        file_put_contents($reportFile, json_encode($found, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Сохранение в БД для сопоставления
        $newCount = 0;
        $updatedCount = 0;

        foreach ($found as $printer) {
            $ids = $snmp->getPrinterIdentifiers($printer['ip']);

            // Ищем по приоритету: MAC → Serial → IP
            $model = null;

            if ($ids['mac']) {
                $model = DiscoveredPrinter::findOne(['mac_address' => $ids['mac']]);
            }

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
            if (!empty($name)) {
                $model->snmp_name = $name;
            } elseif (empty($model->snmp_name)) {
                // Только если и старое пустое — оставляем пустым
                $model->snmp_name = null;
            }
            $model->snmp_descr = $printer['descr'];
            $model->guessed_model = $this->guessModel($printer['descr']);
            $model->last_seen_at = date('Y-m-d H:i:s');
            $model->source = $source ?? '---';
            $model->is_local = false;

            if (!$model->save()) {
//                Yii::error("Failed to save {$printer['ip']}: " . json_encode($model->errors));
//                $this->output->error("FAIL {$printer['ip']}: " . json_encode($model->errors) . "\n");
                $this->output->error(sprintf(
                    "Failed to save %s: %s",
                    $printer['ip'],
                    json_encode($model->errors)
                ));
            }
        }

        $this->output->success("Saved to: $reportFile");
        $this->output->success("Database: " . count($found) . " records");
        $this->output->success("New: $newCount, Updated: $updatedCount");
    }

}
