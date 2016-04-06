<?php

namespace JourneyMonitor\Monitor\JobCreator;

use org\bovigo\vfs\vfsStream;
use JourneyMonitor\Monitor\Base\TestcaseModel;
use JourneyMonitor\Monitor\Base\TestcaseRepository;
use JourneyMonitor\Monitor\Base\Logger;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    private $vfsRoot;
    
    public function test() {
        $stubTestcaseModel1 = new TestcaseModel('a', 't1', 'x@y.com', '*/5', 'foo a');
        $stubTestcaseModel2 = new TestcaseModel('b', 't2', 'x@y.com', '*/15', 'foo b');
        $stubTestcaseModel3 = new TestcaseModel('c', 't3', 'x@y.com', '*/7', 'foo c');
        
        $mockTestcaseRepository = $this->getMockBuilder(TestcaseRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getAll'))
            ->getMock();
        
        $mockTestcaseRepository->expects($this->exactly(2))
            ->method('getAll')
            ->willReturn([$stubTestcaseModel1, $stubTestcaseModel2, $stubTestcaseModel3]);

        $this->vfsRoot = vfsStream::setup('test');

        $creator = new Creator($mockTestcaseRepository, vfsStream::url('test'), 'test', new Logger());
        $creator->run();
        $creator->run(); // Running twice to ensure that file content is completely overwritten

        $this->assertSame(
            'MAILTO=""' .
                "\n" .
                '# t1' .
                "\n" .
                '*/5 * * * * root cd /tmp && sudo -u journeymonitor -H PHP_ENV=test /usr/bin/php /opt/journeymonitor/monitor/bin/run.php a | while IFS= read -r line;do echo "$(date) $line";done >> /var/tmp/journeymonitor-run-testcase-a-cronjob.log 2>&1' .
                "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'journeymonitor-run-testcase-a')
        );

        $this->assertSame(
            'MAILTO=""' .
                "\n" .
                '# t2' .
                "\n" .
                '*/15 * * * * root cd /tmp && sudo -u journeymonitor -H PHP_ENV=test /usr/bin/php /opt/journeymonitor/monitor/bin/run.php b | while IFS= read -r line;do echo "$(date) $line";done >> /var/tmp/journeymonitor-run-testcase-b-cronjob.log 2>&1' .
                "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'journeymonitor-run-testcase-b')
        );

        $this->assertSame(
            'MAILTO=""' .
                "\n" .
                '# t3' .
                "\n" .
                '*/7 * * * * root cd /tmp && sudo -u journeymonitor -H PHP_ENV=test /usr/bin/php /opt/journeymonitor/monitor/bin/run.php c | while IFS= read -r line;do echo "$(date) $line";done >> /var/tmp/journeymonitor-run-testcase-c-cronjob.log 2>&1' .
                "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'journeymonitor-run-testcase-c')
        );
    }
}