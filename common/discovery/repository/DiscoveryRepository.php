<?php

declare(strict_types=1);

namespace common\discovery\repository;

use common\discovery\contracts\DiscoveryRepositoryInterface;
use common\discovery\dto\DiscoveredDevice;

/**
 * Репозиторий обнаруженных устройств.
 */
class DiscoveryRepository implements DiscoveryRepositoryInterface
{
    /**
     * @param DiscoveredDevice $device
     * @return void
     */
    public function save(DiscoveredDevice $device): void
    {
        // Реализация будет выполнена после определения
        // алгоритма сопоставления обнаруженного устройства
        // с объектами инвентаризации.
    }
}
