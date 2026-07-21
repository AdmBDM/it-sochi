<?php

declare(strict_types=1);

namespace common\discovery\scanner;

use common\discovery\contracts\IpRangeExpanderInterface;
use common\discovery\contracts\IpRangeProviderInterface;
use common\discovery\contracts\NetworkScannerInterface;
use common\discovery\contracts\SnmpClientInterface;
use common\discovery\dto\NetworkNode;

readonly class SnmpScanner implements NetworkScannerInterface
{
    public function __construct(
        private SnmpClientInterface      $snmpClient,
        private IpRangeProviderInterface $rangeProvider,
        private IpRangeExpanderInterface $rangeExpander,
    ) {
    }

    public function scan(): iterable
    {
        foreach ($this->rangeProvider->getRanges() as $range) {
            yield from $this->scanRange($range);
        }
    }

    private function scanRange(string $range): iterable
    {
        foreach ($this->rangeExpander->expand($range) as $ip) {
            $node = $this->scanAddress($ip);

            if ($node !== null) {
                yield $node;
            }
        }
    }

    /**
     * @param string $ip
     * @return NetworkNode|null
     */
    private function scanAddress(string $ip): ?NetworkNode
    {
        $node = new NetworkNode();

        $node
            ->setIp($ip)
            ->setSource('snmp');

        if (!$this->snmpClient->isAvailable($node)) {
            return null;
        }

        $hostname = $this->snmpClient->get($node, '1.3.6.1.2.1.1.5.0');

        if ($hostname !== null && $hostname !== '') {
            $node->setHostname($hostname);
        }

        return $node;
    }
}
