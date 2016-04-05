<?php

namespace JourneyMonitor\Monitor\Base;

class Logger
{
    /**
     * @param string $text
     */
    public function info($text)
    {
        if (defined('PHPUNIT') && PHPUNIT === "yes") {
            return;
        }
        print($text . "\n");
    }
}
