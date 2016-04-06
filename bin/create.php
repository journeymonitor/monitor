<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;
use JourneyMonitor\Monitor\Base\Logger;
use JourneyMonitor\Monitor\JobCreator\Creator;

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$logger = new Logger();

$testcaseRepository = new TestcaseRepository(new \PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3'), $logger);

$creator = new Creator($testcaseRepository, '/etc/cron.d', $environmentName, $logger);
$success = $creator->run();

if ($success === true) {
    exit(0);
} else {
    exit(1);
}
