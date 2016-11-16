<?php

namespace JourneyMonitor\Monitor\JobRunner;

use JourneyMonitor\Monitor\Base\TestresultModel;
use JourneyMonitor\Monitor\Base\TestcaseModel;
use JourneyMonitor\Monitor\Base\Logger;

class NotifierTest extends \PHPUnit_Framework_TestCase
{
    public function testMixedResultsYieldMail()
    {
        $testcaseModel = new TestcaseModel('abc', 'tc', 'foo@example.org', '0', 'bar');

        $testresultModel3 = new TestresultModel('123', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');
        $testresultModel2 = new TestresultModel('122', $testcaseModel, new \DateTime(), '0', ['foo'],  '/x/y.png', 'har');
        $testresultModel1 = new TestresultModel('121', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');

        $mockTestresultRepository = $this->getMockBuilder('\JourneyMonitor\Monitor\Base\TestresultRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('getNLastTestresultsForTestcase'))
            ->getMock();

        $mockTestresultRepository->expects($this->exactly(1))
            ->method('getNLastTestresultsForTestcase')
            ->with(3, $testresultModel3->getTestcase())
            ->willReturn([$testresultModel3, $testresultModel2, $testresultModel1]);

        $didSend = false;
        $sendMail = function($receiver, $subject, $body) use (&$didSend) {
            $didSend = true;
        };

        $notifier = new Notifier($mockTestresultRepository, $sendMail, new Logger());
        $notifier->handle($testresultModel3);

        $this->assertTrue($didSend);
    }

    public function testTwoErrorsInRowYieldMail()
    {
        $testcaseModel = new TestcaseModel('abc', 'tc', 'foo@example.org', '0', 'bar');

        $testresultModel3 = new TestresultModel('123', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');
        $testresultModel2 = new TestresultModel('122', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');
        $testresultModel1 = new TestresultModel('121', $testcaseModel, new \DateTime(), '0', ['foo'],  '/x/y.png', 'har');

        $mockTestresultRepository = $this->getMockBuilder('\JourneyMonitor\Monitor\Base\TestresultRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $mockTestresultRepository->expects($this->exactly(1))
            ->method('getNLastTestresultsForTestcase')
            ->with(3, $testresultModel3->getTestcase())
            ->willReturn([$testresultModel3, $testresultModel2, $testresultModel1]);

        $didSend = false;
        $sendMail = function($receiver, $subject, $body) use (&$didSend) {
            $didSend = true;
        };

        $notifier = new Notifier($mockTestresultRepository, $sendMail, new Logger());
        $notifier->handle($testresultModel2);

        $this->assertTrue($didSend);
    }

    public function testThreeErrorsInRowDoNotYieldMail()
    {
        $testcaseModel = new TestcaseModel('abc', 'tc', 'foo@example.org', '0', 'bar');

        $testresultModel3 = new TestresultModel('123', $testcaseModel, new \DateTime(), '2', ['foo'],  '/x/y.png', 'har');
        $testresultModel2 = new TestresultModel('122', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');
        $testresultModel1 = new TestresultModel('121', $testcaseModel, new \DateTime(), '4', ['foo'],  '/x/y.png', 'har');

        $mockTestresultRepository = $this->getMockBuilder('\JourneyMonitor\Monitor\Base\TestresultRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('getNLastTestresultsForTestcase'))
            ->getMock();

        $mockTestresultRepository->expects($this->exactly(1))
            ->method('getNLastTestresultsForTestcase')
            ->with(3, $testresultModel3->getTestcase())
            ->willReturn([$testresultModel3, $testresultModel2, $testresultModel1]);

        $didSend = false;
        $sendMail = function($receiver, $subject, $body) use (&$didSend) {
            $didSend = true;
        };

        $notifier = new Notifier($mockTestresultRepository, $sendMail, new Logger());
        $notifier->handle($testresultModel3);

        $this->assertFalse($didSend);
    }
    
    public function testPageloadTimeoutForErrorcode4YieldsCorrectMail() {
        $testcaseModel = new TestcaseModel('abc', 'tc', 'foo@example.org', '0', 'bar');

        $testresultModel = new TestresultModel(
            '121',
            $testcaseModel,
            new \DateTime(),
            '4',
            ['[2016-11-07 20:00:51.576 +01:00] [ERROR] Command#1: open("/") => [Error: TimeoutException - Timed out waiting for page load.'],
            '/x/y.png',
            'har'
        );

        $mockTestresultRepository = $this->getMockBuilder('\JourneyMonitor\Monitor\Base\TestresultRepository')
                ->disableOriginalConstructor()
                ->setMethods(array('getNLastTestresultsForTestcase'))
                ->getMock();

        $mockTestresultRepository->expects($this->exactly(1))
                ->method('getNLastTestresultsForTestcase')
                ->with(3, $testresultModel->getTestcase())
                ->willReturn([$testresultModel]);

        $actualSubject = null;
        $actualBody = null;
        $sendMail = function($receiver, $subject, $body) use (&$actualSubject, &$actualBody) {
            $actualSubject = $subject;
            $actualBody = $body;
        };

        $notifier = new Notifier($mockTestresultRepository, $sendMail, new Logger());
        $notifier->handle($testresultModel);

        $this->assertSame('[JourneyMonitor] Page load timeout during "tc" testcase.', $actualSubject);
        $this->assertContains('we tried to run your test case named, "tc", but one of the pages of the journey took more than 30 seconds to load.', $actualBody);
    }
    
    public function testPageloadTimeoutForErrorcode3YieldsCorrectMail() {
        $testcaseModel = new TestcaseModel('abc', 'tc', 'foo@example.org', '0', 'bar');

        $testresultModel = new TestresultModel(
            '121',
            $testcaseModel,
            new \DateTime(),
            '3',
            ['[2016-11-16 13:21:43.070 +01:00] [ERROR] Command#3: clickAndWait("id=gk-header__search__button") => [Failure: Timed out waiting for page load. / Command duration or timeout: 30.04 seconds / Build info: version: \'unknown\', revision: \'unknown\', time: \'unknown\' / System info: host: \'v22015051223725490\', ip: \'37.120.178.155\', os.name: \'Linux\', os.arch: \'amd64\', os.version: \'3.13.0-96-generic\', java.version: \'1.8.0_111\' / Driver info: org.openqa.selenium.firefox.FirefoxDriver / Capabilities [{applicationCacheEnabled=true, rotatable=false, handlesAlerts=true, databaseEnabled=true, version=45.4.0, platform=LINUX, nativeEvents=false, acceptSslCerts=true, webStorageEnabled=true, locationContextEnabled=true, browserName=firefox, takesScreenshot=true, javascriptEnabled=true, cssSelectorsEnabled=true}] / Session ID: e498cff3-e4b2-4f7d-9ea8-f11d6b562195] URL: [https://www.galeria-kaufhof.de/search?q=Hose] / Title: [Suchergebnis für Hose | GALERIA Kaufhof]'],
            '/x/y.png',
            'har'
        );

        $mockTestresultRepository = $this->getMockBuilder('\JourneyMonitor\Monitor\Base\TestresultRepository')
                ->disableOriginalConstructor()
                ->setMethods(array('getNLastTestresultsForTestcase'))
                ->getMock();

        $mockTestresultRepository->expects($this->exactly(1))
                ->method('getNLastTestresultsForTestcase')
                ->with(3, $testresultModel->getTestcase())
                ->willReturn([$testresultModel]);

        $actualSubject = null;
        $actualBody = null;
        $sendMail = function($receiver, $subject, $body) use (&$actualSubject, &$actualBody) {
            $actualSubject = $subject;
            $actualBody = $body;
        };

        $notifier = new Notifier($mockTestresultRepository, $sendMail, new Logger());
        $notifier->handle($testresultModel);

        $this->assertSame('[JourneyMonitor] Page load timeout during "tc" testcase.', $actualSubject);
    }

}
