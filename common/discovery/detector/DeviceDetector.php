<?php

declare(strict_types=1);

namespace common\discovery\detector;

use common\discovery\dto\DiscoveredDevice;
use common\discovery\dto\NetworkNode;
use common\discovery\registry\DeviceProbeRegistry;

/**
 * Определяет подходящий DeviceProbe
 * * для обработки обнаруженного сетевого узла.
 */
readonly class DeviceDetector
{
    /**
     * @param DeviceProbeRegistry $probeRegistry
     */
    public function __construct(
        private DeviceProbeRegistry $probeRegistry,
    ) {
    }

    /**
     * Выполняет идентификацию обнаруженного сетевого узла.
     *
     * @param NetworkNode $node
     *
     * @return DiscoveredDevice|null
     */
    public function detect(NetworkNode $node): ?DiscoveredDevice
    {
        foreach ($this->probeRegistry->all() as $probe) {
            if (!$probe->supports($node)) {
                continue;
            }

            return $probe->probe($node);
        }

        return null;
    }
}
