<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\DiscoveredDevice;

/**
 * Определяет тип обнаруженного устройства.
 */
interface DeviceClassifierInterface
{
    /**
     * Определяет тип устройства.
     *
     * Возвращает идентификатор типа либо null,
     * если определить тип невозможно.
     *
     * @param DiscoveredDevice $device
     *
     * @return string|null
     */
    public function classify(DiscoveredDevice $device): ?string;
}
