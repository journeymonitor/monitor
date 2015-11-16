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
        $this->result = $this->pdoStatement->fetch();
        if (false === $this->result) {
            $this->valid = false;
        } else {
            $this->valid = true;
        }
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
        $this->key++;
        $this->result = $this->pdoStatement->fetch();
        if (false === $this->result) {
            $this->valid = false;
            return null;
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
