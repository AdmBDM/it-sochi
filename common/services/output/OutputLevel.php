<?php

namespace common\services\output;

enum OutputLevel: int
{
    case DEBUG   = 10;
    case INFO    = 20;
    case SUCCESS = 30;
    case WARNING = 40;
    case ERROR   = 50;
}