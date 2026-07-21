<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\NetworkNode;

/**
 * Интерфейс SNMP-клиента.
 */
interface SnmpClientInterface
{
    /**
     * Проверяет доступность SNMP на указанном узле.
     *
     * @param NetworkNode $node
     *
     * @return bool
     */
    public function isAvailable(NetworkNode $node): bool;

    /**
     * Выполняет чтение значения SNMP OID.
     *
     * @param NetworkNode $node
     * @param string $oid
     *
     * @return string|null
     */
    public function get(NetworkNode $node, string $oid): ?string;

    /**
     * Выполняет SNMP Walk.
     *
     * @param NetworkNode $node
     * @param string $oid
     *
     * @return array<string, string>
     */
    public function walk(NetworkNode $node, string $oid): array;
}
