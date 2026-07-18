<?php
namespace common\services\snmp;

use common\services\snmp\mib\BrotherMib;
use common\services\snmp\mib\HpMib;
use common\services\snmp\mib\KyoceraMib;
use common\services\snmp\mib\PrinterMib;
use common\services\snmp\mib\SystemMib;

class SnmpClient
{
    private int $timeout = 300000;

    private int $retries = 1;

    public function __construct()
    {
        snmp_set_quick_print(true);
        snmp_set_enum_print(true);
        snmp_set_oid_numeric_print(true);
    }

    public function get(
        string $ip,
        string $oid,
        string $community = 'public'
    ): mixed
    {
        $value = @snmpget(
            $ip,
            $community,
            $oid,
            $this->timeout,
            $this->retries
        );

        if ($value === false) {
            $value = @snmp2_get(
                $ip,
                $community,
                $oid,
                $this->timeout,
                $this->retries
            );
        }

        return $value === false
            ? null
            : $this->normalizeValue($value);
    }

    public function getMany(
        string $ip,
        array $oids,
        string $community = 'public'
    ): array {

        $result = [];

        foreach ($oids as $key => $oid) {
            $result[$key] = $this->get(
                $ip,
                $oid,
                $community
            );
        }

        return $result;
    }

    public function getSystemInfo(
        string $ip,
        string $community = 'public'
    ): array {

        return $this->getMany($ip, [

            'name' => SystemMib::SYS_NAME,

            'description' => SystemMib::SYS_DESCR,

        ], $community);
    }

    public function getPrinterIdentifiers(
        string $ip,
        string $community = 'public'
    ): array {

        $serial = null;

        foreach ([
                     PrinterMib::SERIAL,
                     KyoceraMib::SERIAL,
                     HpMib::SERIAL,
                     BrotherMib::SERIAL,
                 ] as $oid) {

            $serial = $this->get(
                $ip,
                $oid,
                $community
            );

            if (
                !empty($serial)
                &&
                !preg_match('/^0+$/', (string)$serial)
            ) {
                break;
            }

            $serial = null;
        }

        $mac = null;

        $arp = shell_exec("ip neigh show {$ip} 2>/dev/null");

        if (
            preg_match(
                '/([0-9a-f]{2}(:[0-9a-f]{2}){5})/i',
                $arp,
                $m
            )
        ) {
            $mac = strtolower($m[1]);
        }

        return [
            'mac' => $mac,
            'serial' => $serial,
        ];
    }

    public function getPrinterCounters(
        string $ip,
        string $community = 'public'
    ): array {

        $total = $this->get(
            $ip,
            PrinterMib::TOTAL_PAGES,
            $community
        );

        if ($total === null || empty($total)) {
            $total = $this->getLedmTotalPages($ip);
        }

        $color = $this->get(
            $ip,
            HpMib::COLOR_PAGES,
            $community
        );

        return [

            'total_pages' => $total,

            'color_pages' => $color,

            'bw_pages' => (
                $total !== null && $color !== null
            )
                ? max(0, $total - $color)
                : null,

            'toner_black' => $this->get(
                $ip,
                PrinterMib::TONER_BLACK,
                $community
            ),

            'toner_cyan' => null,

            'toner_magenta' => null,

            'toner_yellow' => null,
        ];
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_numeric($value)) {
            return (int)$value;
        }

        $value = trim((string)$value);

        if (preg_match('/^STRING:\s*"?(.*?)"?$/i', $value, $m)) {
            return trim($m[1]);
        }

        if (preg_match('/^INTEGER:\s*(-?\d+)/i', $value, $m)) {
            return (int)$m[1];
        }

        if (preg_match('/^Counter32:\s*(\d+)/i', $value, $m)) {
            return (int)$m[1];
        }

        if (preg_match('/^Gauge32:\s*(\d+)/i', $value, $m)) {
            return (int)$m[1];
        }

        if (preg_match('/^\w+:\s*(\d+)$/', $value, $m)) {
            return (int)$m[1];
        }

        return trim($value, "\" ");
    }

    private function getLedmTotalPages(string $ip): ?int
    {
        $url = "http://{$ip}/DevMgmt/ProductUsageDyn.xml";

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
            ],
        ]);

        $xml = @file_get_contents($url, false, $context);

        if ($xml === false) {
            return null;
        }

        libxml_use_internal_errors(true);

        $doc = simplexml_load_string($xml);

        if ($doc === false) {
            return null;
        }

        $namespaces = $doc->getNamespaces(true);

        if (isset($namespaces['pudyn'])) {
            $doc->registerXPathNamespace('pudyn', $namespaces['pudyn']);
        }

        if (isset($namespaces['dd'])) {
            $doc->registerXPathNamespace('dd', $namespaces['dd']);
        }

        $result = $doc->xpath('//pudyn:PrinterSubunit/dd:TotalImpressions');

        if (!$result || !isset($result[0])) {
            return null;
        }

        return (int)$result[0];
    }

}
