<?php

namespace JourneyMonitor\Monitor\JobRunner;

use JourneyMonitor\Monitor\Base\TestresultModel;
use JourneyMonitor\Monitor\Base\TestcaseModel;
use JourneyMonitor\Monitor\Base\Logger;

class NotifierTest extends \PHPUnit_Framework_TestCase
{
    public function testMixedResults()
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

    public function testTwoErrorsInRow()
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

    public function testThreeErrorsInRow()
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

}
