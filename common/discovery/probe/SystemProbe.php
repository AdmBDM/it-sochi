<?php

declare(strict_types=1);

namespace common\discovery\probe;

use common\discovery\contracts\DeviceProbeInterface;
use common\discovery\contracts\SnmpClientInterface;
use common\discovery\dto\DiscoveredDevice;
use common\discovery\dto\NetworkNode;
use common\discovery\mib\SystemMib;
use LogicException;

readonly class SystemProbe implements DeviceProbeInterface
{
    public function __construct(
        private SnmpClientInterface $snmpClient,
    ) {
    }

    public function supports(NetworkNode $node): bool
    {
//        throw new LogicException('Not implemented yet.');
        return $this->snmpClient->isAvailable($node);
    }

    public function probe(NetworkNode $node): DiscoveredDevice
    {
        $device = new DiscoveredDevice();

        $device
            ->setIp($node->getIp())
            ->setHostname(
                $this->snmpClient->get(
                    $node,
                    SystemMib::SYS_NAME
                )
            )
            ->setAttribute(
                'sysDescr',
                $this->snmpClient->get(
                    $node,
                    SystemMib::SYS_DESCR
                )
            )
            ->setAttribute(
                'sysObjectId',
                $this->snmpClient->get(
                    $node,
                    SystemMib::SYS_OBJECT_ID
                )
            );

        return $device;
    }

}
