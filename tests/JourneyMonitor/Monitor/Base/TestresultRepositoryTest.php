<?php

namespace JourneyMonitor\Monitor\Base;

$calls = 0;

class MockStatement extends \PDOStatement
{
    public function execute($bound_input_params = null) {}

    public function fetch($how = null, $orientation = null, $offset = null)
    {
        global $calls;

        if ($calls == 0) {
            $calls++;
            return [
                'id' => 'a1',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:34:56',
                'exitCode' => '0',
                'output' => 'Hello World',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($calls == 1) {
            $calls++;
            return [
                'id' => 'a2',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:34:57',
                'exitCode' => '4',
                'output' => 'Foobar',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } else {
            return false;
        }
    }
}

class MockPdo extends \PDO
{
    public function __construct() {}
}

class TestResultRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIteratorForAllSince()
    {
        $mockPdo = $this->getMockBuilder('JourneyMonitor\Monitor\Base\MockPdo')
            ->getMock();
        $mockPdo->method('prepare')->willReturn(new MockStatement());

        $mockTestcaseRepository = $this->getMockBuilder('JourneyMonitor\Monitor\Base\TestcaseRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $mockTestcaseRepository
            ->method('getById')
            ->with('1')
            ->willReturn(new TestcaseModel('1', 'One', 'foo@example.org', '*/5', 'bar'));

        $repo = new TestresultRepository($mockPdo, $mockTestcaseRepository);
        $iterator = $repo->getIteratorForAllSince(new \DateTime("now"));

        $res = [];
        foreach ($iterator as $testresult) {
            $res[] = $testresult;
        }

        $this->assertSame(2, sizeof($res));
        $this->assertSame('a1', $res[0]->getId());
        $this->assertSame('One', $res[0]->getTestcase()->getTitle());
        $this->assertSame('a2', $res[1]->getId());
        $this->assertSame('One', $res[1]->getTestcase()->getTitle());
    }
}
