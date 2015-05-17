<?php

namespace Selenior\Monitor\JobCreator;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use Selenior\Monitor\Base\TestcaseModel;
use Selenior\Monitor\Base\TestcaseRepository;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    private $vfsRoot;
    
    public function test() {
        $stubTestcaseModel1 = new TestcaseModel('a', '1', 'x@y.com', '*/5', 'foo');
        $stubTestcaseModel2 = new TestcaseModel('b', '1', 'x@y.com', '*/15', 'foo');
        $stubTestcaseModel3 = new TestcaseModel('c', '2', 'x@y.com', '*/7', 'foo');
        
        $mockTestcaseRepository = $this->getMockBuilder('TestcaseRepository')
            ->setMethods(array('getAll'))
            ->getMock();
        
        $mockTestcaseRepository->expects($this->exactly(2))
            ->method('getAll')
            ->willReturn([$stubTestcaseModel1, $stubTestcaseModel2, $stubTestcaseModel3]);

        $this->vfsRoot = vfsStream::setup('test');

        $creator = new Creator($mockTestcaseRepository, vfsStream::url('test'), 'test');
        $creator->run();
        $creator->run(); // Running twice to ensure that file content is completely overwritten

        $this->assertSame(
            'MAILTO=""' . "\n" . '*/5 * * * * root cd /tmp && sudo -u selenior -H PHP_ENV=test /usr/bin/php /opt/selenior/monitor/bin/run.php a >> /var/tmp/selenior-run-testcase-a-cronjob.log 2>&1' . "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'selenior-run-testcase-a')
        );

        $this->assertSame(
            'MAILTO=""' . "\n" . '*/15 * * * * root cd /tmp && sudo -u selenior -H PHP_ENV=test /usr/bin/php /opt/selenior/monitor/bin/run.php b >> /var/tmp/selenior-run-testcase-b-cronjob.log 2>&1' . "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'selenior-run-testcase-b')
        );

        $this->assertSame(
            'MAILTO=""' . "\n" . '*/7 * * * * root cd /tmp && sudo -u selenior -H PHP_ENV=test /usr/bin/php /opt/selenior/monitor/bin/run.php c >> /var/tmp/selenior-run-testcase-c-cronjob.log 2>&1' . "\n",
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'selenior-run-testcase-c')
        );
    }
}