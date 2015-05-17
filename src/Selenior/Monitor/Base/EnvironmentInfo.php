<?php

namespace Selenior\Monitor\Base;

class EnvironmentInfo
{
    public function getName()
    {
        $env = getenv("PHP_ENV");
        if (!$env) {
            $env = "dev";
        }
        return $env;
    }
}
