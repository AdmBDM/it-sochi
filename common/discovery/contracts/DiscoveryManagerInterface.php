<?php

declare(strict_types=1);

namespace common\discovery\contracts;

interface DiscoveryManagerInterface
{
    public function discover(): void;
}
