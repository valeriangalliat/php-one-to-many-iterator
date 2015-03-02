<?php

namespace Val\Iterator;

class OneToManyIterator extends \IteratorIterator
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
     * @var int Current index.
     */
    protected $key;

    /**
     * @var array Fully aggregated item to yield.
     */
    protected $current;

    /**
     * @var array Previous item (not fully aggregated).
     */
    protected $last;

    /**
     * @param mixed $commonKey Identifier key to identify new items.
     * @param mixed $aggregateKey Aggregation key to fill the items array in.
     * @param \Traversable $iterator Result set iterator.
     */
    public function __construct($commonKey, $aggregateKey, \Traversable $iterator)
    {
        $this->commonKey = $commonKey;
        $this->aggregateKey = $aggregateKey;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     *
     * Rewind the inner iterator, and load the first result if any.
     */
    public function rewind()
    {
        $this->getInnerIterator()->rewind();

        // Will be incremented two times by `$this->fowrard` (see below)
        $this->key = -2;

        // Reset the current and last items
        $this->current = null;
        $this->last = null;

        if (!$this->getInnerIterator()->valid()) {
            // Empty iterator
            return;
        }

        // Always ahead one row
        $this->forward();
        // `$this->key` is now -1

        // Load first item until fully aggregated
        $this->next();
        // `$this->key` is now 0, and we have a valid first item
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Aggregate `$this->last` until a new identifier is found.
     *
     * When fully aggregated, `$this->forward` is called to yield
     * the complete item and prepare the next one.
     *
     * When the inner iterator has ended, `$this->forward` is also called
     * to yield the last working item.
     */
    public function next()
    {
        while (true) {
            if (!$this->getInnerIterator()->valid()) {
                $this->forward();
                break;
            }

            $current = $this->getInnerIterator()->current();

            if ($current[$this->commonKey] !== $this->last[$this->commonKey]) {
                $this->forward();
                break;
            }

            $this->last[$this->aggregateKey][] = $current;
            $this->getInnerIterator()->next();
        }
    }

    /**
     * {@inheritdoc}
     *
     * We keep yielding until the current item is set to `null`.
     */
    public function valid()
    {
        return $this->current !== null;
    }

    /**
     * Advance one item. Meant to be called when `$this->last` is fully
     * aggregated and we want to pass to the next item.
     *
     * This will update `$this->current`, increment `$this->key`, and
     * fetch the next row into `$this->last`.
     */
    protected function forward()
    {
        $this->current = $this->last;

        if (!$this->getInnerIterator()->valid()) {
            // Allow to forward the last item.
            $this->last = null;
        } else {
            $this->last = $this->getInnerIterator()->current();
            $this->last[$this->aggregateKey] = [$this->last];
            $this->getInnerIterator()->next();
        }

        $this->key++;
    }
}
