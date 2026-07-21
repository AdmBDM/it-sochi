<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\DiscoveredDevice;

interface InventoryManagerInterface
{
    /**
     * Выполнить обработку обнаруженного устройства.
     *
     * @param DiscoveredDevice $device Обнаруженное устройство.
     *
     * @return void
     */
    public function process(DiscoveredDevice $device): void;
}
