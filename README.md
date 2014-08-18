One-to-many iterator
====================

> Helper iterator and generator for one-to-many joins

Overview
--------

When you want to fetch a one-to-many relation, you're probably using
a `JOIN` to avoid the [N+1 selects problem][0].

[0]: http://use-the-index-luke.com/sql/join/nested-loops-join-n1-problem

Though, it may be difficult to iterate over the result, especially when
you need the whole "many" part of the relation loaded for a process.

This is why I created this tiny library. It takes a `Traversable`
of arrays, having a common key to distinguish the "one" part of the
one-to-many relation, and sorted on this key (so it can yield items
in streaming, without loading the whole set in memory).

It will then aggregate the "many" part of the relation in a configurable
key.

Installation
------------

Through [Composer][1] as [`val/one-to-many-iterator`][2].

[1]: https://getcomposer.org/
[2]: https://packagist.org/packages/val/one-to-many-iterator

Example
-------

Your database result iterator, once converted into an array, looks like
this (a typical `JOIN`):

```php
<?php

[
    ['id' => 1, 'parent_column' => 'hello', 'child_column' => 'foo'],
    ['id' => 1, 'parent_column' => 'hello', 'child_column' => 'bar'],
    ['id' => 2, 'parent_column' => 'world', 'child_column' => 'baz'],
];
```

But you'd like to iterate over something like this:

```php
<?php

[
    [
        'id' => 1,
        'parent_column' => 'hello',
        'children' => [
            ['child_column' => 'foo'],
            ['child_column' => 'bar'],
        ],
    ],
    [
        'id' => 2,
        'parent_column' => 'world',
        'children' => [
            ['child_column' => 'baz'],
        ],
    ],
];
```

To achieve this, just pass your database result to
`Val\Iterator\OneToManyIterator` or `Val\Iterator\OneToManyGenerator`,
while configuring the common key (here, `id`), and aggregate key (here,
`children`).

Assuming `$result` contains the raw SQL result iterator:

```php
<?php

// With an iterator
$aggregated = new OneToManyIterator('id', 'children', $result);

// With a generator
$aggregated = new OneToManyGenerator('id', 'children', $result);

foreach ($aggregated as $i => $parent) {
    $parent['id'];
    $parent['parent_column'];

    foreach ($parent['children'] as $child) {
        $child['child_column'];
    }
}
```

The difference between the iterator and the generator, is, well.. that
the former implements a raw [PHP iterator][php-iterator] while
the latter uses a [PHP generator][php-generator] (available since
version 5.5).

[php-iterator]: http://php.net/manual/en/class.iterator.php
[php-generator]: http://php.net/manual/en/language.generators.overview.php

Bugs
----

* When using a `LEFT JOIN` instead of a `JOIN`, thus not guaranteeing
  the presence of at least one relation item, the aggregate field
  will still contain one item of `null` values.
