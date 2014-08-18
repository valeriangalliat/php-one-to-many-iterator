<?php

namespace Val\Iterator;

class OneToManyGenerator implements \IteratorAggregate
{
    /**
     * @var mixed
     */
    protected $commonKey;

    /**
     * @var mixed
     */
    protected $aggregateKey;

    /**
     * @var \Traversable
     */
    protected $iterator;

    /**
     * @param mixed $commonKey Identifier key to identify new items.
     * @param mixed $aggregateKey Aggregation key to fill the items array in.
     * @param \Traversable $iterator Result set iterator.
     */
    public function __construct($commonKey, $aggregateKey, \Traversable $iterator)
    {
        $this->commonKey = $commonKey;
        $this->aggregateKey = $aggregateKey;
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $last = null;

        foreach ($this->iterator as $row) {
            if ($last === null) {
                $last = $row;
            } elseif ($row[$this->commonKey] !== $last[$this->commonKey]) {
                yield $last;
                $last = $row;
            }

            $last[$this->aggregateKey][] = $row;
        }

        if ($last !== null) {
            yield $last;
        }
    }
}
