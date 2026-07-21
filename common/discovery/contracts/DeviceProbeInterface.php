<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\DiscoveredDevice;
use common\discovery\dto\NetworkNode;

interface DeviceProbeInterface
{
    /**
     * Проверить, способен ли Probe обработать указанный сетевой узел.
     *
     * @param NetworkNode $node Обнаруженный сетевой узел.
     *
     * @return bool
     */
    public function supports(NetworkNode $node): bool;

    /**
     * Выполнить идентификацию устройства.
     *
     * @param NetworkNode $node Обнаруженный сетевой узел.
     *
     * @return DiscoveredDevice
     */
    public function probe(NetworkNode $node): DiscoveredDevice;
}
