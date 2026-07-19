<?php

namespace common\services\output;

class NullOutput extends AbstractOutput
{
    public function write(
        string $message,
        OutputLevel $level = OutputLevel::INFO
    ): void
    {
    }
}
