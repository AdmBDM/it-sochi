<?php

declare(strict_types=1);

namespace common\discovery\infrastructure\snmp;

use common\discovery\contracts\SnmpClientInterface;
use common\discovery\dto\NetworkNode;

readonly class SnmpClient implements SnmpClientInterface
{
    private const string DEFAULT_COMMUNITY = 'public';

    private const int TIMEOUT = 300000;

    private const int RETRIES = 1;

    public function __construct()
    {
        snmp_set_quick_print(true);
        snmp_set_enum_print(true);
        snmp_set_oid_numeric_print(true);
    }

    public function isAvailable(NetworkNode $node): bool
    {
        return $this->query(
                $node,
                '1.3.6.1.2.1.1.2.0'
            ) !== false;
    }

    public function get(NetworkNode $node, string $oid): ?string
    {
        return $this->normalizeValue(
            $this->query($node, $oid)
        );
    }

    public function walk(NetworkNode $node, string $oid): array
    {
        $values = $this->walkQuery($node, $oid);

        if ($values === false) {
            return [];
        }

        $result = [];

        foreach ($values as $key => $value) {
            $result[$key] = $this->normalizeValue($value);
        }

        return $result;
    }

    private function normalizeValue(string|false $value): ?string
    {
        if ($value === false) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^[A-Z0-9\-]+:\s*"?(.*?)"?$/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return trim($value, "\" ");
    }

    private function getCommunity(NetworkNode $node): string
    {
        return self::DEFAULT_COMMUNITY;
    }

    private function query(NetworkNode $node, string $oid): string|false
    {
        $ip = $node->getIp();

        if ($ip === null || $ip === '') {
            return false;
        }

        $community = $this->getCommunity($node);

        $value = @snmpget(
            $ip,
            $community,
            $oid,
            self::TIMEOUT,
            self::RETRIES
        );

        if ($value !== false) {
            return $value;
        }

        return @snmp2_get(
            $ip,
            $community,
            $oid,
            self::TIMEOUT,
            self::RETRIES
        );
    }

    private function walkQuery(NetworkNode $node, string $oid): array|false
    {
        $ip = $node->getIp();

        if ($ip === null || $ip === '') {
            return false;
        }

        $community = $this->getCommunity($node);

        $result = @snmpwalk(
            $ip,
            $community,
            $oid,
            self::TIMEOUT,
            self::RETRIES
        );

        if ($result !== false) {
            return $result;
        }

        return @snmp2_walk(
            $ip,
            $community,
            $oid,
            self::TIMEOUT,
            self::RETRIES
        );
    }

}
