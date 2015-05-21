<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use Selenior\Monitor\Base\TestcaseRepository;
use Selenior\Monitor\Base\TestresultRepository;
use Selenior\Monitor\Base\EnvironmentInfo;
use Selenior\Monitor\JobRunnor\Runner;
use Selenior\Monitor\JobRunnor\Notifier;

if (!array_key_exists(1, $argv)) {
    exit(1);
}
$testcaseId = $argv[1];

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$dbConnection = new PDO('sqlite:/var/tmp/selenior-monitor.sqlite-' . $environmentName);

$testcaseRepository = new TestcaseRepository($dbConnection);

$runner = new Runner($testcaseRepository, '/var/tmp', $testcaseId);
$runner->prepare();
$testresultModel = $runner->run();

$testresultRepository = new TestresultRepository($dbConnection, $testcaseRepository);
$testresultRepository->add($testresultModel);

$notifier = new Notifier();
$notifier->handle($testresultModel);
