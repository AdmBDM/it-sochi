<?php

namespace common\services\cctv;

use common\models\Dvr;

class CctvFactory
{
    public static function create(Dvr $dvr): BaseCctvService
    {
        return match ($dvr->system_type) {
            Dvr::SYSTEM_TRASSIR => new TrassirService($dvr),
            Dvr::SYSTEM_DAHUA => new DahuaService($dvr),
            Dvr::SYSTEM_HIKVISION => new HikvisionService($dvr),
            default => throw new \InvalidArgumentException("Unknown system type: {$dvr->system_type}"),
        };
    }
}
