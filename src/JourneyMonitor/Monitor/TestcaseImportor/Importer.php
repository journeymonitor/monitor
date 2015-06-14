<?php

namespace JourneyMonitor\Monitor\TestcaseImportor;

use JourneyMonitor\Monitor\Base\TestcaseModel;
use JourneyMonitor\Monitor\Base\TestcaseRepository;

class Importer
{
    private $client;
    private $endpoint;
    private $testcaseRepository;
    private $scriptTransformer;

    public function __construct($client, $endpoint, TestcaseRepository $testcaseRepository, ScriptTransformer $scriptTransformer)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->testcaseRepository = $testcaseRepository;
        $this->scriptTransformer = $scriptTransformer;
    }

    public function run()
    {
        $response = $this->client->get($this->endpoint);
        $json = $response->json();
        $this->testcaseRepository->removeAll();
        foreach ($json as $testcase) {
            $testcaseModel = new TestcaseModel(
                $testcase['id'],
                $testcase['title'],
                $testcase['notifyEmail'],
                $testcase['cadence'],
                $testcase['script']
            );
            $testcaseModel->setScript($this->scriptTransformer->transform($testcaseModel->getScript()));
            $this->testcaseRepository->add($testcaseModel);
        }
    }
}
