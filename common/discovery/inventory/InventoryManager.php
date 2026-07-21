<?php

declare(strict_types=1);

namespace common\discovery\inventory;

use common\discovery\contracts\DiscoveryRepositoryInterface;
use common\discovery\contracts\InventoryManagerInterface;
use common\discovery\dto\DiscoveredDevice;

/**
 * Менеджер обработки обнаруженных устройств.
 */
readonly class InventoryManager implements InventoryManagerInterface
{
    /**
     * @param DiscoveryRepositoryInterface $repository
     */
    public function __construct(
        private DiscoveryRepositoryInterface $repository,
    ) {
    }

    /**
     * @param DiscoveredDevice $device
     * @return void
     */
    public function process(DiscoveredDevice $device): void
    {
        // TODO: Реализовать сохранение устройства после завершения разработки Discovery.
    }
}
