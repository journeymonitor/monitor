<?php

namespace Selenior\Monitor\JobRunnor;

use Selenior\Monitor\Base\TestresultModel;

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
    
    public function run($retry = 0)
    {
        $found = false;
        while (!$found) {
            $jobId = mt_rand(1, 999999999);
            if (!file_exists('/var/tmp/selenior-xvfb-screen-' . $jobId)) {
                $found = true;
            }
        }

        touch('/var/tmp/selenior-testcase-run-' . $jobId . '.lock');

        $output = [];
        $exitCode = 0;
        sleep(rand(0, 10)); // Firefoxes should not be started in parallel
        $datetimeRun = new \DateTime('now');
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
        unlink('/var/tmp/selenior-testcase-run-' . $jobId . '.lock');
        unlink('/var/tmp/selenior-testcase-run-' . $jobId . '-exit-status');

        if ($exitCode === 1 && $retry < 5) { // Internal selenium-runner error, retry
            print_r($output);
            print('Internal selenium-runner error, trying again...' . "\n");
            return $this->run($retry + 1);
        } else {
            $failScreenshotFilename = null;
            foreach ($output as $line) {
                //[2015-05-22 20:48:21.939] [INFO] - captured screenshot: /var/tmp/selenior-screenshots/test_20150522_204820088_3_fail.png
                if (strstr($line, '[INFO] - captured screenshot: ')) {
                    $failScreenshotFilename = substr($line, 86);
                    $failScreenshotFilename = substr($failScreenshotFilename, 0, -4);
                    exec(
                        '/usr/bin/convert /var/tmp/selenior-screenshots/' .
                        $failScreenshotFilename .
                        '.png -resize 256 /var/tmp/selenior-screenshots/' .
                        $failScreenshotFilename .
                        '_256.png'
                    );
                }
            }
            return new TestresultModel(TestresultModel::generateId(), $this->testcaseModel, $datetimeRun, $exitCode, $output, $failScreenshotFilename);
        }
    }
}
