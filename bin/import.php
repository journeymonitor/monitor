<?php

error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');

require_once __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\EnvironmentInfo;
use JourneyMonitor\Monitor\TestcaseImportor\ScriptTransformer;
use JourneyMonitor\Monitor\TestcaseImportor\Importer;

if (!array_key_exists(1, $argv)) {
    exit(1);
}
$endpoint = $argv[1];

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$testcaseRepository = new TestcaseRepository(new PDO('sqlite:/var/tmp/journeymonitor-monitor-' . $environmentName . '.sqlite3'));

$importer = new Importer(new Client(), $endpoint, $testcaseRepository, new ScriptTransformer());
$importer->run();
