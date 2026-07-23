<?php

declare(strict_types=1);

namespace common\discovery\probe;

use common\discovery\contracts\DeviceProbeInterface;
use common\discovery\contracts\SnmpClientInterface;
use common\discovery\dto\DiscoveredDevice;
use common\discovery\dto\NetworkNode;
use common\discovery\mib\SystemMib;

readonly class SystemProbe implements DeviceProbeInterface
{
    /**
     * @param SnmpClientInterface $snmpClient
     */
    public function __construct(
        private SnmpClientInterface $snmpClient,
    ) {
    }

    /**
     * @param NetworkNode $node
     * @return bool
     */
    public function supports(NetworkNode $node): bool
    {
        return $this->snmpClient->isAvailable($node);
    }

    /**
     * @param NetworkNode $node
     * @return DiscoveredDevice
     */
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
            )
            ->setAttribute(
                'sysContact',
                $this->snmpClient->get(
                    $node,
                    SystemMib::SYS_CONTACT
                )
            )
            ->setAttribute(
                'sysLocation',
                $this->snmpClient->get(
                    $node,
                    SystemMib::SYS_LOCATION
                )
            );

        return $device;
    }

}
