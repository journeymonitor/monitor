<?php

namespace JourneyMonitor\Monitor\JobRunnor;

use JourneyMonitor\Monitor\Base\TestresultModel;

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
            $this->directory . DIRECTORY_SEPARATOR . 'journeymonitor-testcase-' . $this->testcaseModel->getId() . '.html',
            $this->testcaseModel->getScript()
        );
    }
    
    public function run($retry = 0)
    {
        $found = false;
        while (!$found) {
            $jobId = mt_rand(1, 999999999);
            if (!file_exists('/var/tmp/journeymonitor-testcase-run-' . $jobId . '.lock')) {
                $found = true;
                touch('/var/tmp/journeymonitor-testcase-run-' . $jobId . '.lock');
            }
        }

        $found = false;
        while (!$found) {
            $proxyPort = mt_rand(9091, 60000);
            if (!file_exists('/var/tmp/journeymonitor-testcase-run-proxyport-' . $proxyPort . '.lock')) {
                $found = true;
                touch('/var/tmp/journeymonitor-testcase-run-proxyport-' . $proxyPort . '.lock');
            }
        }

        $output = [];
        $exitCode = 0;
        sleep(mt_rand(0, 10)); // Firefoxes should not be started in parallel it seems
        $datetimeRun = new \DateTime('now');

        $commandline = '/bin/bash ' .
            __DIR__ . DIRECTORY_SEPARATOR . '../../../../bin/run-testcase.sh ' .
            $jobId .
            ' ' .
            $proxyPort .
            ' ' .
            $this->directory . DIRECTORY_SEPARATOR . 'journeymonitor-testcase-' . $this->testcaseModel->getId() . '.html ' .
            '2>&1 ' .
            '| tee -a /var/tmp/journeymonitor-run-testcase-' . $this->testcaseModel->getId() . '.log ; ' .
            'exit `cat /var/tmp/journeymonitor-testcase-run-' . $jobId . '-exit-status`';

        echo $commandline . "\n";

        exec(
            $commandline,
            $output,
            $exitCode
        );

        $har = file_get_contents('/var/tmp/journeymonitor-testcase-run-' . $jobId . '-har');

        unlink('/var/tmp/journeymonitor-testcase-run-' . $jobId . '-har');
        unlink('/var/tmp/journeymonitor-testcase-run-' . $jobId . '.lock');
        unlink('/var/tmp/journeymonitor-testcase-run-proxyport-' . $proxyPort . '.lock');
        unlink('/var/tmp/journeymonitor-testcase-run-' . $jobId . '-exit-status');

        if ($exitCode === 1 && $retry < 5) { // Internal selenium-runner error, retry
            print_r($output);
            print('Internal selenium-runner error, trying again...' . "\n");
            return $this->run($retry + 1);
        } else {
            $la = new LogAnalyzer();
            $failScreenshotFilenameWithoutExtension = null;
            foreach ($output as $line) {
                if ($la->lineContainsScreenshot($line)) {
                    $failScreenshotFilenameWithoutExtension = substr($la->getScreenshotFilenameFromLine($line), 0, -4);
                    exec(
                        '/usr/bin/convert /var/tmp/journeymonitor-screenshots/' .
                        $failScreenshotFilenameWithoutExtension .
                        '.png -resize 256 /var/tmp/journeymonitor-screenshots/' .
                        $failScreenshotFilenameWithoutExtension .
                        '_256.png'
                    );
                }
            }

            return new TestresultModel(TestresultModel::generateId(), $this->testcaseModel, $datetimeRun, $exitCode, $output, $failScreenshotFilenameWithoutExtension, (string)$har);
        }
    }
}
