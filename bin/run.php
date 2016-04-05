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

$logger = new Logger();

$dbConnection = new PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3');

$testcaseRepository = new TestcaseRepository($dbConnection);

$runner = new Runner($testcaseRepository, '/var/tmp', $testcaseId);
$runner->prepare();

$logger->info('About to start Selenium run...' . "\n");
$testresultModel = $runner->run();
$logger->info('Finished Selenium run, exit code was ' . $testresultModel->getExitCode() . "\n");

$logger->info('About to persist testresult ' . $testresultModel->getId() . '...' . "\n");
$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);
$testresultRepository->add($testresultModel);
$logger->info('Finished persisting testresult ' . $testresultModel->getId() . '.' . "\n");

$logger->info('About to handle notifications...' . "\n");
$sendMail = function($receiver, $subject, $body) use ($logger) {
    $logger->info('Sending mail to ' . $receiver . '...' . "\n");
    mail($receiver, $subject, $body);
    $logger->info('Finished sending mail to ' . $receiver . '.' . "\n");
};

$notifier = new Notifier($testresultRepository, $sendMail, new Logger());
$notifier->handle($testresultModel);
$logger->info('Finished handling notifications.' . "\n");
