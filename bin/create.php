<?php

error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

use Selenior\Monitor\Base\TestcaseRepository;
use Selenior\Monitor\Base\EnvironmentInfo;
use Selenior\Monitor\JobCreator\Creator;

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$testcaseRepository = new TestcaseRepository(new \PDO('sqlite:/var/tmp/selenior-monitor.sqlite-' . $environmentName));

$creator = new Creator($testcaseRepository, '/etc/cron.d', $environmentName);
$creator->run();
