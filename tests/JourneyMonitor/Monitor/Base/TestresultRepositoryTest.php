<?php

namespace JourneyMonitor\Monitor\Base;

class MockStatement1 extends \PDOStatement
{
    private $calls = 0;

    public function execute($bound_input_params = null) {}

    public function fetch($how = null, $orientation = null, $offset = null)
    {
        if ($this->calls == 0) {
            $this->calls++;
            return [
                'id' => 'a1',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:34:56',
                'exitCode' => '0',
                'output' => 'Hello World',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($this->calls == 1) {
            $this->calls++;
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

class MockStatement2 extends \PDOStatement
{
    private $calls = 0;

    public function execute($bound_input_params = null) {}

    public function fetch($how = null, $orientation = null, $offset = null)
    {
        if ($this->calls == 0) {
            $this->calls++;
            return [
                'id' => 'a1',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:34:56',
                'exitCode' => '0',
                'output' => 'Hello World',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($this->calls == 1) {
            $this->calls++;
            return [
                'id' => 'a2',
                'testcaseId' => '2',
                'datetimeRun' => '2015-01-02 12:34:57',
                'exitCode' => '4',
                'output' => 'Foobar',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($this->calls == 2) {
            $this->calls++;
            return [
                'id' => 'a3',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:34:58',
                'exitCode' => '4',
                'output' => 'Foobar',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($this->calls == 3) {
            $this->calls++;
            return [
                'id' => 'a4',
                'testcaseId' => '2',
                'datetimeRun' => '2015-01-02 12:34:59',
                'exitCode' => '4',
                'output' => 'Foobar',
                'failScreenshotFilename' => null,
                'har' => '{}'
            ];
        } elseif ($this->calls == 4) {
            $this->calls++;
            return [
                'id' => 'a5',
                'testcaseId' => '1',
                'datetimeRun' => '2015-01-02 12:35:00',
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
        $mockPdo->method('prepare')->willReturn(new MockStatement1());

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

    public function testGetIteratorForAllSinceSkipsTestresultsWithoutTestcase()
    {
        $mockPdo = $this->getMockBuilder('JourneyMonitor\Monitor\Base\MockPdo')
            ->getMock();
        $mockPdo->method('prepare')->willReturn(new MockStatement2());

        $mockTestcaseRepository = $this->getMockBuilder('JourneyMonitor\Monitor\Base\TestcaseRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $tcm = new TestcaseModel('2', 'Two', 'foo@example.org', '*/5', 'bar');

        $map = [
            ['1', null],
            ['2', $tcm]
        ];

        $mockTestcaseRepository
            ->method('getById')
            ->will($this->returnValueMap($map));

        $repo = new TestresultRepository($mockPdo, $mockTestcaseRepository);
        $iterator = $repo->getIteratorForAllSince(new \DateTime("now"));

        $res = [];
        $keys = [];
        foreach ($iterator as $key => $testresult) {
            $keys[] = $key;
            $res[] = $testresult;
        }

        $this->assertSame([0, 1], $keys);
        $this->assertSame(2, sizeof($res));
        $this->assertSame('a2', $res[0]->getId());
        $this->assertSame('Two', $res[0]->getTestcase()->getTitle());
        $this->assertSame('a4', $res[1]->getId());
        $this->assertSame('Two', $res[1]->getTestcase()->getTitle());
    }
}
