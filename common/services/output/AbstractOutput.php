<?php

namespace common\services\output;

abstract class AbstractOutput implements OutputInterface
{
    abstract public function write(
        string $message,
        OutputLevel $level = OutputLevel::INFO
    ): void;

    public function debug(string $message): void
    {
        $this->write($message, OutputLevel::DEBUG);
    }

    public function info(string $message): void
    {
        $this->write($message, OutputLevel::INFO);
    }

    public function success(string $message): void
    {
        $this->write($message, OutputLevel::SUCCESS);
    }

    public function warning(string $message): void
    {
        $this->write($message, OutputLevel::WARNING);
    }

    public function error(string $message): void
    {
        $this->write($message, OutputLevel::ERROR);
    }
}
