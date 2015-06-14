<?php

namespace JourneyMonitor\Monitor\JobRunnor;

use JourneyMonitor\Monitor\Base\TestresultModel;

class Notifier
{
    public function handle(TestresultModel $testresultModel)
    {
        $sendmail = false;
        $subject = '';
        $body = '';

        // No valid Selenese
        if ($testresultModel->getExitCode() === 4) {

            $sendmail = true;
            $subject = '[JourneyMonitor] We couldn\'t run your "' . $testresultModel->getTestcase()->getTitle() . '" testcase.';
            $body = <<<EOT
Hi there,

at {datetimeRun}, we tried to run your test case named, "{title}", but the Selenese HTML code seems to be invalid.

Here is the output from our system:

{output}

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
                print('Not sending notification mail because we had a UnreachableBrowserException.');
                return;
            }

            $body = str_replace('{datetimeRun}', $testresultModel->getDatetimeRun()->format(\DateTime::RFC850), $body);
            $body = str_replace('{title}', $testresultModel->getTestcase()->getTitle(), $body);
            $body = str_replace('{output}', $output, $body);
            $body = str_replace('{exitCode}', $testresultModel->getExitCode(), $body);
            $body = str_replace('{testresultId}', $testresultModel->getId(), $body);
            $body = str_replace('{testcaseId}', $testresultModel->getTestcase()->getId(), $body);

            mail(
                $testresultModel->getTestcase()->getNotifyEmail(),
                $subject,
                $body
            );
        }
    }
}
