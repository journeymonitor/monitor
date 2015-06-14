<?php

namespace JourneyMonitor\Monitor\Base;

class EnvironmentInfo
{
    public function getName()
    {
        if (is_array($_SERVER) && array_key_exists('PHP_ENV', $_SERVER)) {
            $env = $_SERVER['PHP_ENV'];
        } else {
            $env = getenv('PHP_ENV');
        }
        if (!$env) {
            $env = 'dev';
        }
        return $env;
    }
}
