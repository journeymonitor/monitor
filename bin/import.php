<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;
use JourneyMonitor\Monitor\Base\Logger;
use JourneyMonitor\Monitor\TestcaseImporter\ScriptTransformer;
use JourneyMonitor\Monitor\TestcaseImporter\Importer;

if (!array_key_exists(1, $argv)) {
    exit(1);
}
$endpoint = $argv[1];

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$logger = new Logger();

$testcaseRepository = new TestcaseRepository(new PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3'), $logger);

$importer = new Importer(new Client(), $endpoint, $testcaseRepository, new ScriptTransformer());
$importer->run();
