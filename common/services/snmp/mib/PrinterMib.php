<?php

namespace common\services\snmp\mib;

final class PrinterMib
{
    /*
     * RFC 3805 Printer MIB
     */

    public const TOTAL_PAGES =
        '1.3.6.1.2.1.43.10.2.1.4.1.1';

    public const TONER_BLACK =
        '1.3.6.1.2.1.43.11.1.1.9.1.1';

    public const SERIAL =
        '1.3.6.1.2.1.43.5.1.1.17.1';
}
