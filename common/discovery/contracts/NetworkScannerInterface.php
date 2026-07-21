<?php

declare(strict_types=1);

namespace common\discovery\contracts;

use common\discovery\dto\NetworkNode;

interface NetworkScannerInterface
{
    /**
     * Выполнить обнаружение сетевых узлов.
     *
     * @return iterable<NetworkNode>
     */
    public function scan(): iterable;
}
