<?php

declare(strict_types=1);

namespace common\discovery\registry;

use common\discovery\contracts\NetworkScannerInterface;

/**
 * Реестр зарегистрированных сканеров сети.
 *
 * Предназначен для хранения набора сканеров,
 * участвующих в процессе обнаружения устройств.
 */
class ScannerRegistry
{
    /**
     * Зарегистрированные сканеры.
     *
     * @var NetworkScannerInterface[]
     */
    private array $scanners = [];

    /**
     * Регистрирует сканер.
     *
     * @param NetworkScannerInterface $scanner
     *
     * @return static
     */
    public function add(NetworkScannerInterface $scanner): static
    {
        if (in_array($scanner, $this->scanners, true)) {
            return $this;
        }

        $this->scanners[] = $scanner;

        return $this;
    }

    /**
     * Возвращает список зарегистрированных сканеров.
     *
     * @return NetworkScannerInterface[]
     */
    public function all(): array
    {
        return $this->scanners;
    }

    /**
     * Очищает реестр.
     *
     * @return static
     */
    public function clear(): static
    {
        $this->scanners = [];

        return $this;
    }

    /**
     * Возвращает количество зарегистрированных сканеров.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->scanners);
    }
}
