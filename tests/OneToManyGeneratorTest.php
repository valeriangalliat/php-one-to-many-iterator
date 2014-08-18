<?php

namespace Val\Iterator\Tests;

use Val\Iterator\OneToManyGenerator;

class OneToManyGeneratorTest extends OneToManyTest
{
    protected function create(\Traversable $iterator)
    {
        return new OneToManyGenerator('id', 'items', $iterator);
    }
}
