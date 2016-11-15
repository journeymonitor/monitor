<?php

namespace JourneyMonitor\Monitor\JobRunner;

use JourneyMonitor\Monitor\Base\Logger;
use JourneyMonitor\Monitor\Base\TestresultModel;
use JourneyMonitor\Monitor\Base\TestresultRepository;

class Notifier
{
    private $testresultRepository;
    private $sendMail;
    private $logger;

    public function __construct(TestresultRepository $testresultRepository, callable $sendMail, Logger $logger)
    {
        $this->testresultRepository = $testresultRepository;
        $this->sendMail = $sendMail;
        $this->logger = $logger;
    }

    public function handle(TestresultModel $testresultModel)
    {
        // Stop notifying if the last 3 results were negative,
        // in order to avoid spamming the user
        $this->logger->info('Getting last 3 testresults in order to decide if notifications should be sent:');
        $lastResults = $this->testresultRepository->getNLastTestresultsForTestcase(3, $testresultModel->getTestcase());
        $allFailed = true;
        $i = 0;
        /** @var TestresultModel $testresultModelToCheck */
        foreach ($lastResults as $testresultModelToCheck) {
            $this->logger->info(' Testresult: datetimeRun ' . $testresultModelToCheck->getDatetimeRun()->format(\DateTime::ISO8601) .
                  ', id ' . $testresultModelToCheck->getId() .
                  ', exitCode ' . $testresultModelToCheck->getExitCode());
            if ($testresultModelToCheck->getExitCode() === 0) {
                $allFailed = false;
            }
            $i++;
        }
        if ($allFailed && $i > 2) {
            $this->logger->info('Not notifying because this is the third negative testresult in a row.');
            return;
        }

        $sendmail = false;
        $subject = '';
        $body = '';
        $reason = '';

        // No valid Selenese or page load timeout
        if ($testresultModel->getExitCode() === 4) {

            $logAnalyzer = new LogAnalyzer();
            $sendmail = true;
            if ($logAnalyzer->pageloadTimeoutOccured($testresultModel->getOutput())) {
                $subject = '[JourneyMonitor] Page load timeout during "' . $testresultModel->getTestcase()->getTitle() . '" testcase.';
                $reason = ', but one of the pages of the journey took more than 30 seconds to load.';
            } else {
                $subject = '[JourneyMonitor] We couldn\'t run your "' . $testresultModel->getTestcase()->getTitle() . '" testcase.';
                $reason = ', but the Selenese code is probably invalid.';
            }
            $body = <<<EOT
Hi there,

at {datetimeRun}, we tried to run your test case named, "{title}"{reason}

Here is the output from our system:

{output}

Go to the details page of this testcase run:
http://journeymonitor.com/testresults/{testresultId}
                    
Edit this testcase:
http://journeymonitor.com/testcases/{testcaseId}

Disable checks and notifications for this testcase:
http://journeymonitor.com/testcases/#testcase-{testcaseId}

Sincerely,
--
 The JourneyMonitor System
EOT;
        }


        // The test case itself ran, but failed
        if ($testresultModel->getExitCode() === 2 || $testresultModel->getExitCode() === 3) {

            $sendmail = true;
            $subject = '⚠ [JourneyMonitor] Testcase "' . $testresultModel->getTestcase()->getTitle() . '" failed!';
            $body = <<<EOT
Hi there,

at {datetimeRun}, your test case "{title}" failed with the following output:

{output}

See all details and a screenshot of the problem:
http://journeymonitor.com/testresults/{testresultId}

Edit this testcase:
http://journeymonitor.com/testcases/{testcaseId}

Disable checks and notifications for this testcase:
http://journeymonitor.com/testcases/{testcaseId}/disable

Re-enable checks and notifications for this testcase:
http://journeymonitor.com/testcases/{testcaseId}/enable

Sincerely,
--
 The JourneyMonitor System
EOT;
        }

        if ($sendmail === true) {
            $outputLines = $testresultModel->getOutput();
            $output = '';
            foreach ($outputLines as $outputLine) {
                if (stristr($outputLine, '[ERROR] Command') || stristr($outputLine, '[ERROR] ErrorTestCase')) {
                    $output .= $outputLine . "\n";
                }
            }

            if (stristr($output, 'UnreachableBrowserException')) {
                $this->logger->info('Not sending notification mail because we had a UnreachableBrowserException.');
                return;
            }

            $body = str_replace('{datetimeRun}', $testresultModel->getDatetimeRun()->format(\DateTime::RFC850), $body);
            $body = str_replace('{title}', $testresultModel->getTestcase()->getTitle(), $body);
            $body = str_replace('{output}', $output, $body);
            $body = str_replace('{exitCode}', $testresultModel->getExitCode(), $body);
            $body = str_replace('{testresultId}', $testresultModel->getId(), $body);
            $body = str_replace('{testcaseId}', $testresultModel->getTestcase()->getId(), $body);
            $body = str_replace('{reason}', $reason, $body);

            $sendMail = $this->sendMail;
            $sendMail(
                $testresultModel->getTestcase()->getNotifyEmail(),
                $subject,
                $body
            );
        }
    }
}
