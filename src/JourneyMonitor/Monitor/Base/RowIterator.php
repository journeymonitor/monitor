<?php

namespace JourneyMonitor\Monitor\Base;

class RowIterator implements \Iterator
{
    protected $pdoStatement;
    /** @var int $key The cursor pointer */
    protected $key;
    /** @var  bool|\stdClass The resultset for a single row */
    protected $result;
    /** @var  bool $valid Flag indicating there's a valid resource or not */
    protected $valid;

    public function __construct(\PDOStatement $PDOStatement)
    {
        $this->pdoStatement = $PDOStatement;
        $this->key = -1;
        $this->next();
    }

    /**
     * @inheritDoc
     */
    public function current()
    {
        return $this->result;
    }

    /**
     * @inheritDoc
     */
    public function next()
    {
        $row = $this->pdoStatement->fetch();
        if (false === $row) {
            $this->result = false;
            $this->valid = false;
            return null;
        } else {
            try {
                $this->result = $this->createResult($row);
                $this->key++;
                $this->valid = true;
            } catch (\Exception $e) {
                return $this->next();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function valid()
    {
        return $this->valid;
    }

    /**
     * @inheritDoc
     */
    public function rewind()
    {
        $this->key = 0;
    }
}
