<?php

error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use Selenior\Monitor\Base\TestcaseRepository;
use Selenior\Monitor\Base\EnvironmentInfo;
use Selenior\Monitor\TestcaseImportor\ScriptTransformer;
use Selenior\Monitor\TestcaseImportor\Importer;

if (!array_key_exists(1, $argv)) {
    exit(1);
}
$endpoint = $argv[1];

$environmentInfo = new EnvironmentInfo();
$environmentName = $environmentInfo->getName();

$testcaseRepository = new TestcaseRepository(new PDO('sqlite:/var/tmp/selenior-monitor.sqlite' . $environmentName));

$importer = new Importer(new Client(), $endpoint, $testcaseRepository, new ScriptTransformer());
$importer->run();
