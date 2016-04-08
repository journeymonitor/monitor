<?php

namespace JourneyMonitor\Monitor\Base;

class Logger
{
    const TARGET_STDOUT = 0;
    const TARGET_NONE = 1;
    const TARGET_ERROR_LOG = 2;

    private $mode = self::TARGET_STDOUT;

    public function __construct($mode = self::TARGET_STDOUT) {
        $this->mode = $mode;
    }

    /**
     * @param string $text
     */
    public function info($text)
    {
        if (defined('PHPUNIT') && PHPUNIT === "yes") {
            return;
        }

        if ($this->mode === self::TARGET_STDOUT) {
            print($text . "\n");
            return;
        }

        if ($this->mode === self::TARGET_NONE) {
            return;
        }

        if ($this->mode === self::TARGET_ERROR_LOG) {
            error_log($text . "\n");
            return;
        }
    }

    public function error($text)
    {
        $this->info($text);
    }

    public function critical($text)
    {
        $this->info($text);
    }
}
