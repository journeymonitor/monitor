<?php

use \Phpmig\Adapter;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$container = new \Pimple();

$container['db'] = $container->share(function() use ($environmentName) {
    $dbh = new \PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3');
    $dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    return $dbh;
});

$container['phpmig.adapter'] = $container->share(function() use ($container) {
    return new Adapter\PDO\Sql($container['db'], 'migrations');
});

$container['phpmig.migrations_path'] = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

return $container;