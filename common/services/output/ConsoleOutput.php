<?php

namespace common\services\output;

use yii\console\Controller;

class ConsoleOutput extends AbstractOutput
{
    private OutputFormatter $formatter;

    /**
     * @param Controller $controller
     * @param OutputFormatter|null $formatter
     */
    public function __construct(
        private readonly Controller $controller,
        ?OutputFormatter $formatter = null
    ) {

        $this->formatter = $formatter
            ?? new OutputFormatter();
    }

    /**
     * @param string $message
     * @param OutputLevel $level
     * @return void
     */
    public function write(
        string $message,
        OutputLevel $level = OutputLevel::INFO
    ): void {

        $text = $this->formatter->format(
            $message,
            $level
        );

        switch ($level) {

            case OutputLevel::ERROR:

                $this->controller->stderr($text);

                break;

            case OutputLevel::WARNING:

                $this->controller->stderr($text);

                break;

            default:

                $this->controller->stdout($text);

                break;
        }
    }
}
