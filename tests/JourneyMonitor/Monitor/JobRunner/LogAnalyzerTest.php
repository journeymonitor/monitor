<?php

namespace JourneyMonitor\Monitor\JobRunner;

class LogAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    public function testLineContainsScreenshot()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertTrue($la->lineContainsScreenshot($line));
    }

    public function testLineDoesNotContainScreenshot()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - i like screenshots: foo';
        $la = new LogAnalyzer();
        $this->assertFalse($la->lineContainsScreenshot($line));
    }

    public function testGetScreenshotFilenameFromLine()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'test_20150522_204820088_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromLineWithLongerTimestamp()
    {
        $line = '[2016-04-05 11:20:31.211 +02:00] [INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromPartlyCorrupted()
    {
        $line = '[2016-04-05 11:20[INFO] - captured screenshot: /var/tmp/journeymonitor-screenshots/journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertSame(
            'journeymonitor-testcase-40B659CF-28BA-4D27-9F33-D109B735B019_20160405_112024644_3_fail.png',
            $la->getScreenshotFilenameFromLine($line)
        );
    }

    public function testGetScreenshotFilenameFromLineFails()
    {
        $line = '[2015-05-22 20:48:21.939] [INFO] - i like screenshots: /var/tmp/journeymonitor-screenshots/test_20150522_204820088_3_fail.png';
        $la = new LogAnalyzer();
        $this->assertFalse($la->getScreenshotFilenameFromLine($line));
    }
}
