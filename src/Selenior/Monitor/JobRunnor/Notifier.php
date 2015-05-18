<?php

namespace Selenior\Monitor\JobRunnor;

class Notifier
{
    public function handle(TestresultModel $testresultModel)
    {
        $body = <<<EOT
Hi there,

at {datetimeRun}, your test case "{title}" failed with the following output:

{output}

The POSIX exit code was {exitCode}.

Disable checks and notifications for this testcase:
http://journeymonitor.com/testcases/disable/{id}

Re-enable checks and notifications for this testcase:
http://journeymonitor.com/testcases/enable/{id}

Sincerely,
--
 The JourneyMonitor System
EOT;

        $outputLines = $testresultModel->getOutput();
        $output = '';
        foreach ($outputLines as $outputLine) {
            $output .= $outputLine . "\n";
        }
        $body = str_replace('{datetimeRun}', $testresultModel->getDatetimeRun()->format(\DateTime::RFC850), $body);
        $body = str_replace('{title}', $testresultModel->getTestcase()->getTitle(), $body);
        $body = str_replace('{output}', $output, $body);
        $body = str_replace('{exitCode}', $testresultModel->getExitCode(), $body);
        $body = str_replace('{id}', $testresultModel->getTestcase()->getId(), $body);

        if ($testresultModel->getExitCode() != 0) {
            mail(
                $testresultModel->getTestcase()->getNotifyEmail(),
                '⚠ [JourneyMonitor] Testcase "' . $testresultModel->getTestcase()->getTitle() . '" failed!',
                $body
            );
        }
    }
}
