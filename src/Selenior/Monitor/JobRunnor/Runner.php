<?php

namespace Selenior\Monitor\JobRunnor;

use Selenior\Monitor\Base\TestcaseRepository;

class Runner
{
    private $testcaseRepository;
    private $directory;
    private $testcaseModel;

    public function __construct($testcaseRepository, $directory, $testcaseId)
    {
        $this->testcaseRepository = $testcaseRepository;
        $this->directory = $directory;
        $this->testcaseModel = $this->testcaseRepository->getById($testcaseId);
    }
    
    public function prepare()
    {
        file_put_contents(
            $this->directory . DIRECTORY_SEPARATOR . 'selenior-testcase-' . $this->testcaseModel->getId() . '.html',
            $this->testcaseModel->getScript()
        );
    }
    
    public function run()
    {
        $found = false;
        while (!$found) {
            $jobId = mt_rand(1, 999999999);
            if (!file_exists('/var/tmp/selenior-xvfb-screen-' . $jobId)) {
                $found = true;
            }
        }

        touch('/var/tmp/selenior-xvfb-screen-' . $jobId);

        date_default_timezone_set('Europe/Berlin');
        $output = [];
        $exitCode = 0;
        exec(
            '/bin/bash ' .
                __DIR__ . DIRECTORY_SEPARATOR . '../../../../bin/run-testcase.sh ' .
                $jobId .
                ' ' .
                $this->directory . DIRECTORY_SEPARATOR . 'selenior-testcase-' . $this->testcaseModel->getId() . '.html ' .
                '2>&1 ' .
                '| tee -a /var/tmp/selenior-run-testcase-' . $this->testcaseModel->getId() . '.log ; ' .
                'exit `cat /var/tmp/selenior-testcase-run-' . $jobId . '-exit-status`',
            $output,
            $exitCode
        );
        unlink('/var/tmp/selenior-xvfb-screen-' . $jobId);
        unlink('/var/tmp/selenior-testcase-run-' . $jobId . '-exit-status');

        if ($exitCode === 1) { // Internal selenium-runner error, retry
            print_r($output);
            print('Internal selenium-runner error, trying again...');
            return $this->run();
        } else {
            return new TestresultModel($this->testcaseModel, new \DateTime('now'), $exitCode, $output);
        }
    }
}
