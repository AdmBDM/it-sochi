<?php

declare(strict_types=1);

namespace common\discovery\infrastructure\network;

use common\discovery\contracts\IpRangeExpanderInterface;
use InvalidArgumentException;

class IpRangeExpander implements IpRangeExpanderInterface
{
    public function expand(string $range): iterable
    {
        $range = trim($range);

        if (str_contains($range, '/')) {
            yield from $this->expandCidr($range);

            return;
        }

        if (str_contains($range, '-')) {
            yield from $this->expandInterval($range);

            return;
        }

        if (filter_var($range, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            yield $range;

            return;
        }

        throw new InvalidArgumentException(
            sprintf('Unsupported IP range format: %s', $range)
        );
    }

    /**
     * @param string $cidr
     * @return iterable
     */
    private function expandCidr(string $cidr): iterable
    {
        [$network, $prefix] = explode('/', $cidr, 2);

        if (
            filter_var($network, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
            || !is_numeric($prefix)
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid CIDR: %s', $cidr)
            );
        }

        $prefix = (int) $prefix;

        if ($prefix < 0 || $prefix > 32) {
            throw new InvalidArgumentException(
                sprintf('Invalid CIDR prefix: %s', $cidr)
            );
        }

        $networkLong = ip2long($network);

        if ($networkLong === false) {
            throw new InvalidArgumentException(
                sprintf('Invalid network address: %s', $cidr)
            );
        }

        $mask = $prefix === 0
            ? 0
            : (-1 << (32 - $prefix));

        $networkAddress = $networkLong & $mask;
        $broadcastAddress = $networkAddress | ~$mask;

        for ($ip = $networkAddress; $ip <= $broadcastAddress; $ip++) {
            yield long2ip($ip);
        }
    }

    /**
     * @param string $interval
     * @return iterable
     */
    private function expandInterval(string $interval): iterable
    {
        [$startIp, $endIp] = array_map('trim', explode('-', $interval, 2));

        if (
            filter_var($startIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false ||
            filter_var($endIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid IP interval: %s', $interval)
            );
        }

        $start = ip2long($startIp);
        $end = ip2long($endIp);

        if ($start === false || $end === false) {
            throw new InvalidArgumentException(
                sprintf('Invalid IP interval: %s', $interval)
            );
        }

        if ($start > $end) {
            throw new InvalidArgumentException(
                sprintf('Invalid IP interval: start address is greater than end address: %s', $interval)
            );
        }

        for ($ip = $start; $ip <= $end; $ip++) {
            yield long2ip($ip);
        }
    }}
