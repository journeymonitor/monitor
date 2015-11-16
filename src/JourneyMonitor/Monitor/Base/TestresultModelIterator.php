<?php

namespace JourneyMonitor\Monitor\Base;

class TestresultModelIterator extends RowIterator
{
    protected $testresultRepository;

    public function __construct(TestresultRepository $testresultRepository, \PDOStatement $PDOStatement)
    {
        $this->testresultRepository = $testresultRepository;
        return parent::__construct($PDOStatement);
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        $currentRow = parent::current();
        if (false === $currentRow) {
            return false;
        }
        return $this->testresultRepository->arrayToTestresultModel($currentRow);
    }
}
