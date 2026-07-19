<?php

namespace common\services\output;

class OutputFormatter
{
    /**
     * Форматирует сообщение для вывода.
     *
     * @param string $message
     * @param OutputLevel $level
     *
     * @return string
     */
    public function format(
        string $message,
        OutputLevel $level
    ): string {

        $prefix = match ($level) {

            OutputLevel::DEBUG   => '[DEBUG]',

            OutputLevel::INFO    => '[INFO ]',

            OutputLevel::SUCCESS => '[ OK  ]',

            OutputLevel::WARNING => '[WARN ]',

            OutputLevel::ERROR   => '[ERROR]',

        };

        return sprintf(
            "%s %s%s",
            $prefix,
            $message,
            PHP_EOL
        );
    }
}
