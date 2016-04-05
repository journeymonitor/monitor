<?php

set_time_limit(3600);

header('Content-Type: application/json');
flush();

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use JourneyMonitor\Monitor\Base\TestresultRepository;
use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$dbConnection = new PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3');

if (!is_object($dbConnection)) {
    error_log('Problem with sqlite db connection: ' . print_r($dbConnection));
    exit(1);
}

$testcaseRepository = new TestcaseRepository($dbConnection);
$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);

$testresultModelIterator = $testresultRepository->getIteratorForAllSince((new \DateTime())->modify('-2 hours'));

echo "[\n";

$firstLoop = true;
foreach ($testresultModelIterator as $testresultModel) {
    if (!$firstLoop) {
        echo ",\n";
    } else {
        $firstLoop = false;
    }
    echo(
        json_encode([
            'id'                     => $testresultModel->getId(),
            'testcaseId'             => $testresultModel->getTestcase()->getId(),
            'datetimeRun'            => $testresultModel->getDatetimeRun()->format('Y-m-d H:i:s'),
            'exitCode'               => $testresultModel->getExitCode(),
            'output'                 => implode("\n", $testresultModel->getOutput()),
            'failScreenshotFilename' => $testresultModel->getFailScreenshotFilename(),
            'har'                    => $testresultModel->getHar(),
        ])
    );
    flush();
}

echo "\n]";
