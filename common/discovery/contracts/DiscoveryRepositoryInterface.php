<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\DiscoveredDevice;

interface DiscoveryRepositoryInterface
{
    /**
     * Сохранить сведения об обнаруженном устройстве.
     *
     * @param DiscoveredDevice $device Обнаруженное устройство.
     *
     * @return void
     */
    public function save(DiscoveredDevice $device): void;
}
