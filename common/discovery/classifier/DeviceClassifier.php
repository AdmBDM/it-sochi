<?php

declare(strict_types=1);

namespace common\discovery\classifier;

use common\discovery\contracts\DeviceClassifierInterface;
use common\discovery\dto\DiscoveredDevice;

/**
 * Базовая реализация классификатора устройств.
 */
readonly class DeviceClassifier implements DeviceClassifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function classify(DiscoveredDevice $device): ?string
    {
        return null;
    }
}
