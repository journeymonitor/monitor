<?php

namespace JourneyMonitor\Monitor\JobRunner;

use org\bovigo\vfs\vfsStream;
use JourneyMonitor\Monitor\Base\TestcaseModel;
use JourneyMonitor\Monitor\Base\TestcaseRepository;

class RunnerTest extends \PHPUnit_Framework_TestCase
{
    private $vfsRoot;
    
    public function test() {
        $stubTestcaseModel = new TestcaseModel('b', 'The Foo', 'han.solo@rebels.org', '*/15', 'foo-b');

        $mockTestcaseRepository = $this->getMockBuilder(TestcaseRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getById'))
            ->getMock();
        
        $mockTestcaseRepository->expects($this->exactly(1))
            ->method('getById')
            ->with('b')
            ->willReturn($stubTestcaseModel);

        $this->vfsRoot = vfsStream::setup('test');

        $runner = new Runner($mockTestcaseRepository, vfsStream::url('test'), 'b');
        $runner->prepare();

        $this->assertSame(
            'foo-b',
            file_get_contents(vfsStream::url('test') . DIRECTORY_SEPARATOR . 'journeymonitor-testcase-b.html')
        );
    }
}
