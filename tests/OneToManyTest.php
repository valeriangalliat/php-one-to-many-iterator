<?php

namespace Val\Iterator\Tests;

abstract class OneToManyTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $this->execute([], []);
    }

    public function testSingle()
    {
        $this->execute([
            ['id' => 1, 'name' => 'foo'],
        ], [
            ['id' => 1, 'items' => [['name' => 'foo']]],
        ]);
    }

    public function testSingleAggregation()
    {
        $this->execute([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 1, 'name' => 'bar'],
        ], [
            ['id' => 1, 'items' => [['name' => 'foo'], ['name' => 'bar']]],
        ]);
    }

    public function testRegular()
    {
        $this->execute([
            ['id' => 1, 'name' => 'foo'],
            ['id' => 1, 'name' => 'bar'],
            ['id' => 2, 'name' => 'baz'],
            ['id' => 3, 'name' => 'boo'],
            ['id' => 3, 'name' => 'boz'],
        ], [
            ['id' => 1, 'items' => [['name' => 'foo'], ['name' => 'bar']]],
            ['id' => 2, 'items' => [['name' => 'baz'], ]],
            ['id' => 3, 'items' => [['name' => 'boo'], ['name' => 'boz']]],
        ]);
    }

    protected function execute(array $array, array $expected)
    {
        $iterator = new \ArrayIterator($array);
        $iterator = $this->create($iterator);

        for ($i = 0; $i < 2; $i++) {
            $result = iterator_to_array($iterator);
            $this->assertLike($result, $expected);
        }
    }

    abstract protected function create(\Traversable $iterator);

    protected function assertLike(array $result, array $expected)
    {
        $this->assertEquals(empty($expected), empty($result));

        foreach ($expected as $k => $v) {
            if (is_array($v)) {
                $this->assertLike($result[$k], $v);
            } else {
                $this->assertEquals($result[$k], $v);
            }
        }
    }
}
