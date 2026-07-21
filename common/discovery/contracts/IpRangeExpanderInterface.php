<?php

declare(strict_types=1);

namespace common\discovery\contracts;

/**
 * Интерфейс развёртывания диапазонов IP-адресов.
 */
interface IpRangeExpanderInterface
{
    /**
     * Разворачивает диапазон IP-адресов в последовательность отдельных адресов.
     *
     * @param string $range
     *
     * @return iterable
     */
    public function expand(string $range): iterable;
}
