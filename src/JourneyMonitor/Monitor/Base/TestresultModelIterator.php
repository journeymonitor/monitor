<?php

namespace JourneyMonitor\Monitor\Base;

use Symfony\Component\Config\Definition\Exception\Exception;

class TestresultModelIterator extends RowIterator
{
    protected $testresultRepository;

    public function __construct(TestresultRepository $testresultRepository, \PDOStatement $PDOStatement)
    {
        $this->testresultRepository = $testresultRepository;
        return parent::__construct($PDOStatement);
    }

    protected function createResult(array $row)
    {
        return $this->testresultRepository->arrayToTestresultModel($row);
    }
}
