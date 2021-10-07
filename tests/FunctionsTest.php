<?php

namespace ipl\Tests\Stdlib;

use ArrayIterator;
use InvalidArgumentException;
use stdClass;
use ipl\Stdlib;

class FunctionsTest extends TestCase
{
    public function testGetPhpTypeWithObject()
    {
        $object = (object) [];

        $this->assertSame('stdClass', Stdlib\get_php_type($object));
    }

    public function testGetPhpTypeWithInstance()
    {
        $instance = new stdClass();

        $this->assertSame('stdClass', Stdlib\get_php_type($instance));
    }

    public function testGetPhpTypeWithPhpType()
    {
        $array = [];

        $this->assertSame('array', Stdlib\get_php_type($array));
    }

    public function testArrayvalWithArray()
    {
        $array = ['key' => 'value'];

        $this->assertSame($array, Stdlib\arrayval($array));
    }

    public function testArrayvalWithObject()
    {
        $array = ['key' => 'value'];

        $object = (object) $array;

        $this->assertSame($array, Stdlib\arrayval($object));
    }

    public function testArrayvalWithTraversable()
    {
        $array = ['key' => 'value'];

        $traversable = new ArrayIterator($array);

        $this->assertSame($array, Stdlib\arrayval($traversable));
    }

    public function testArrayvalException()
    {
        $this->expectException(InvalidArgumentException::class);

        Stdlib\arrayval(null);
    }

    public function testIterableKeyFirstReturnsFirstKeyIfIterableImplementsIteratorAndIsNotEmpty()
    {
        $this->assertSame('a', Stdlib\iterable_key_first(new ArrayIterator(['a' => 'a', 'b' => 'b'])));
    }

    public function testIterableKeyFirstReturnsFirstKeyIfIterableIsArrayAndIsNotEmpty()
    {
        $this->assertSame('a', Stdlib\iterable_key_first(['a' => 'a', 'b' => 'b']));
    }

    public function testIterableKeyFirstReturnsFirstKeyIfIterableIsGeneratorAndIsNotEmpty()
    {
        $this->assertSame('a', Stdlib\iterable_key_first(call_user_func(function () {
            yield 'a' => 'a';
            yield 'b' => 'b';
        })));
    }

    public function testIterableKeyFirstReturnsNullIfIterableImplementsIteratorAndIsEmpty()
    {
        $this->assertNull(Stdlib\iterable_key_first(new ArrayIterator([])));
    }

    public function testIterableKeyFirstReturnsNullIfIterableIsArrayAndIsEmpty()
    {
        $this->assertNull(Stdlib\iterable_key_first([]));
    }

    public function testIterableKeyFirstReturnsNullIfIterableIsGeneratorAndIsEmpty()
    {
        $this->assertNull(Stdlib\iterable_key_first(call_user_func(function () {
            return;
            /** @noinspection PhpUnreachableStatementInspection Empty generator */
            yield;
        })));
    }
}
