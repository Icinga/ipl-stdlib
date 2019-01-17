<?php

namespace ipl\Tests\Stdlib;

use ArrayIterator;
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayvalException()
    {
        Stdlib\arrayval(null);
    }

    public function testStrPutcsv()
    {
        $data = [
            [
                'Name' => 'John Doe',
                'Age'  => 45
            ],
            [
                'Name' => 'Richard Roe',
                'Age'  => 38
            ],
            [
                'Name' => 'Jane Roe',
                'Age'  => 27
            ]
        ];

        $csv = <<<'CSV'
Name,Age
"John Doe",45
"Richard Roe",38
"Jane Roe",27
CSV;

        $this->assertSame($csv, Stdlib\str_putcsv($data));
    }
}
