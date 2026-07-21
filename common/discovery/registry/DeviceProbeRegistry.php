<?php

declare(strict_types=1);

namespace common\discovery\registry;

use common\discovery\contracts\DeviceProbeInterface;

/**
 * Реестр зарегистрированных идентификаторов устройств.
 *
 * Предназначен для хранения набора Probe,
 * участвующих в идентификации обнаруженных устройств.
 */
class DeviceProbeRegistry
{
    /**
     * Зарегистрированные Probe.
     *
     * @var DeviceProbeInterface[]
     */
    private array $probes = [];

    /**
     * Регистрирует Probe.
     *
     * @param DeviceProbeInterface $probe
     *
     * @return static
     */
    public function add(DeviceProbeInterface $probe): static
    {
        if (in_array($probe, $this->probes, true)) {
            return $this;
        }

        $this->probes[] = $probe;

        return $this;
    }

    /**
     * Возвращает список зарегистрированных Probe.
     *
     * @return DeviceProbeInterface[]
     */
    public function all(): array
    {
        return $this->probes;
    }

    /**
     * Очищает реестр.
     *
     * @return static
     */
    public function clear(): static
    {
        $this->probes = [];

        return $this;
    }

    /**
     * Возвращает количество зарегистрированных Probe.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->probes);
    }
}
