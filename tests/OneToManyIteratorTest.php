<?php

namespace Val\Iterator\Tests;

use Val\Iterator\OneToManyIterator;

class OneToManyIteratorTest extends OneToManyTest
{
    protected function create(\Traversable $iterator)
    {
        return new OneToManyIterator('id', 'items', $iterator);
    }
}
