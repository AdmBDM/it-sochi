<?php

declare(strict_types=1);

namespace common\discovery\manager;

use common\discovery\contracts\DiscoveryManagerInterface;
use common\discovery\contracts\InventoryManagerInterface;
use common\discovery\detector\DeviceDetector;
use common\discovery\registry\ScannerRegistry;

/**
 * Менеджер процесса обнаружения устройств.
 */
readonly class DiscoveryManager implements DiscoveryManagerInterface
{
    /**
     * @param ScannerRegistry $scannerRegistry
     * @param DeviceDetector $deviceDetector
     * @param InventoryManagerInterface $inventoryManager
     */
    public function __construct(
        private ScannerRegistry $scannerRegistry,
        private DeviceDetector $deviceDetector,
        private InventoryManagerInterface $inventoryManager,
    ) {
    }

    /**
     * @return void
     */
    public function discover(): void
    {
        foreach ($this->scannerRegistry->all() as $scanner) {
            foreach ($scanner->scan() as $node) {
                $device = $this->deviceDetector->detect($node);

                if ($device === null) {
                    continue;
                }

                $this->inventoryManager->process($device);
            }
        }
    }
}
