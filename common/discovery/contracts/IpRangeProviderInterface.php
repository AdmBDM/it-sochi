<?php

declare(strict_types=1);

namespace common\discovery\contracts;

/**
 * Интерфейс поставщика диапазонов IP-адресов для сканирования.
 */
interface IpRangeProviderInterface
{
    /**
     * Возвращает список диапазонов IP-адресов.
     *
     * Каждый диапазон представляет собой строку,
     * понятную реализации сканера
     * (например: "192.168.88.0/24",
     * "192.168.88.10-192.168.88.50" и т.п.).
     *
     * @return string[]
     */
    public function getRanges(): array;
}
