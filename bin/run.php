<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\TestresultRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;
use JourneyMonitor\Monitor\Base\Logger;
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

print('About to start Selenium run...' . "\n");
$testresultModel = $runner->run();
print('Finished Selenium run.' . "\n");

print('About to persist testresult ' . $testresultModel->getId() . '...' . "\n");
$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);
$testresultRepository->add($testresultModel);
print('Finished persisting testresult ' . $testresultModel->getId() . '.' . "\n");

print('About to handle notifications...' . "\n");
$sendMail = function($receiver, $subject, $body) {
    print('Sending mail to ' . $receiver . '...' . "\n");
    mail($receiver, $subject, $body);
    print('Finished sending mail to ' . $receiver . '.' . "\n");
};

$notifier = new Notifier($testresultRepository, $sendMail, new Logger());
$notifier->handle($testresultModel);
print('Finished handling notifications.' . "\n");
