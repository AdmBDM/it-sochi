<?php

namespace common\services\output;

interface OutputInterface
{
    public function write(
        string $message,
        OutputLevel $level = OutputLevel::INFO
    ): void;

    public function debug(string $message): void;

    public function info(string $message): void;

    public function success(string $message): void;

    public function warning(string $message): void;

    public function error(string $message): void;
}
