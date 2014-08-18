<?php

namespace Val\Iterator\Tests;

abstract class OneToManyTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $this->execute([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 1, 'name' => 'bar'],
            ['id' => 2, 'name' => 'baz'],
        ], [
            ['id' => 1, 'items' => [['name' => 'foo'], ['name' => 'bar']]],
            ['id' => 2, 'items' => [['name' => 'baz'], ]],
        ]);
    }

    public function testSingle()
    {
        $this->execute([
            ['id' => 1, 'name' => 'foo'],
        ], [
            ['id' => 1, 'items' => [['name' => 'foo']]],
        ]);
    }

    protected function execute(array $array, array $expected)
    {
        $iterator = new \ArrayIterator($array);
        $iterator = $this->create($iterator);

        $result = iterator_to_array($iterator);
        $this->assertEquals($result + $expected, $result);
    }

    abstract protected function create(\Traversable $iterator);
}
