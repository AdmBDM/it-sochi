<?php

namespace common\discovery\mib;

final readonly class SystemMib
{
    public const SYS_DESCR = '1.3.6.1.2.1.1.1.0';

    public const SYS_OBJECT_ID = '1.3.6.1.2.1.1.2.0';

    public const SYS_UPTIME = '1.3.6.1.2.1.1.3.0';

    public const SYS_CONTACT = '1.3.6.1.2.1.1.4.0';

    public const SYS_NAME = '1.3.6.1.2.1.1.5.0';

    public const SYS_LOCATION = '1.3.6.1.2.1.1.6.0';

    public const SYS_SERVICES = '1.3.6.1.2.1.1.7.0';

    private function __construct()
    {
    }
}
