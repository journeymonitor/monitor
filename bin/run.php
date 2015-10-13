<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\TestresultRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;
use JourneyMonitor\Monitor\JobRunner\Runner;
use JourneyMonitor\Monitor\JobRunner\Notifier;

if (!array_key_exists(1, $argv)) {
    exit(1);
}
$testcaseId = $argv[1];

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$dbConnection = new PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3');

$testcaseRepository = new TestcaseRepository($dbConnection);

$runner = new Runner($testcaseRepository, '/var/tmp', $testcaseId);
$runner->prepare();
$testresultModel = $runner->run();

$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);
$testresultRepository->add($testresultModel);

$sendMail = function($receiver, $subject, $body) {
    mail($receiver, $subject, $body);
};

$notifier = new Notifier($testresultRepository, $sendMail);
$notifier->handle($testresultModel);
