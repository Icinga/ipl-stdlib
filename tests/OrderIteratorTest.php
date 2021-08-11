<?php

namespace ipl\Tests\Stdlib;

use InvalidArgumentException;
use ipl\Stdlib\OrderIterator;

class OrderIteratorTest extends TestCase
{
    public function testOrderIteratorOnlyAcceptsIntegerAsOrder()
    {
        $this->expectException(InvalidArgumentException::class);

        $it = new OrderIterator();
        $it->add('foo', 'bar');
    }

    public function testOrderIteratorAcceptsAnyTypeAsValue()
    {
        $it = new OrderIterator();
        $it->add(1, 1);
        $it->add('foo', 2);
        $it->add(3.3, 3);
        $it->add([4], 4);
        $it->add(null, 5);
        $it->add((object) ['foo' => 'bar'], 6);

        $this->assertEquals(
            [1, 'foo', 3.3, [4], null, (object) ['foo' => 'bar']],
            iterator_to_array($it)
        );
    }

    public function testOrderIteratorCanBeExtendedByArrays()
    {
        $it = new OrderIterator();
        $it->extend(['foo', 'bar']);

        $this->assertEquals(
            ['foo', 'bar'],
            iterator_to_array($it)
        );
    }

    public function testOrderIteratorCanBeExtendedByGenerators()
    {
        $makeGen = function () {
            yield 42 => 'foo';
            yield 41 => 'bar';
        };

        $it = new OrderIterator();
        $it->extend($makeGen());

        $this->assertEquals(
            ['bar', 'foo'],
            iterator_to_array($it)
        );
    }

    public function testOrderIteratorSortsProperly()
    {
        $it = new OrderIterator();
        $it->add('foo', 4);
        $it->add('bar', 3);
        $it->add('oof', 2);

        $this->assertEquals(
            ['oof', 'bar', 'foo'],
            iterator_to_array($it)
        );
    }

    public function testOrderIteratorAllowsOrderReuse()
    {
        $it = new OrderIterator();
        $it->add('foo', 4);
        $it->add('bar', 3);
        $it->add('oof', 3);

        $this->assertEquals(
            ['bar', 'oof', 'foo'],
            iterator_to_array($it)
        );
    }
}
