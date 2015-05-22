<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use Selenior\Monitor\Base\TestresultRepository;
use Selenior\Monitor\Base\TestcaseRepository;
use Selenior\Monitor\Base\EnvironmentInfo;

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$dbConnection = new PDO('sqlite:/var/tmp/selenior-monitor.sqlite-' . $environmentName);

$testcaseRepository = new TestcaseRepository($dbConnection);
$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);

$testresultModels = $testresultRepository->getAllSince((new \DateTime())->modify('-1 day'));

$testresultsArray = [];
foreach ($testresultModels as $testresultModel) {

    $outputLines = $testresultModel->getOutput();
    $output = '';
    foreach ($outputLines as $outputLine) {
        $output .= $outputLine . "\n";
    }

    $testresultsArray[] = [
        'id'          => $testresultModel->getId(),
        'testcaseId'  => $testresultModel->getTestcase()->getId(),
        'datetimeRun' => $testresultModel->getDatetimeRun()->format('Y-m-d H:i:s'),
        'exitCode'    => $testresultModel->getExitCode(),
        'output'      => $output,
    ];
}

header('Content-Type: application/json');

echo json_encode($testresultsArray);