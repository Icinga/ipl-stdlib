<?php

namespace ipl\Tests\Stdlib;

use ArrayIterator;
use ipl\Stdlib\Seq;
use stdClass;

class SeqTest extends TestCase
{
    public function testFindWithArrays()
    {
        $this->assertEquals(
            ['oof', 'BAR'],
            Seq::find(['foo' => 'bar', 'oof' => 'BAR'], 'BAR')
        );
        $this->assertEquals(
            ['foo', 'bar'],
            Seq::find(['foo' => 'bar', 'oof' => 'BAR'], 'BAR', false)
        );
    }

    public function testFindWithGenerators()
    {
        $generatorCreator = function () {
            yield 'foo' => 'bar';
            yield 'oof' => 'BAR';
        };

        $this->assertEquals(
            ['oof', 'BAR'],
            Seq::find($generatorCreator(), 'BAR')
        );
        $this->assertEquals(
            ['foo', 'bar'],
            Seq::find($generatorCreator(), 'BAR', false)
        );
    }

    public function testFindWithIterators()
    {
        $this->assertEquals(
            ['oof', 'BAR'],
            Seq::find(new ArrayIterator(['foo' => 'bar', 'oof' => 'BAR']), 'BAR')
        );
        $this->assertEquals(
            ['foo', 'bar'],
            Seq::find(new ArrayIterator(['foo' => 'bar', 'oof' => 'BAR']), 'BAR', false)
        );
    }
    public function testFindValueWithArrays()
    {
        $this->assertEquals(
            'BAR',
            Seq::findValue(['foo' => 'bar', 'FOO' => 'BAR'], 'FOO')
        );
        $this->assertEquals(
            'bar',
            Seq::findValue(['foo' => 'bar', 'FOO' => 'BAR'], 'FOO', false)
        );
    }

    public function testFindValueWithGenerators()
    {
        $generatorCreator = function () {
            yield 'foo' => 'bar';
            yield 'FOO' => 'BAR';
        };

        $this->assertEquals(
            'BAR',
            Seq::findValue($generatorCreator(), 'FOO')
        );
        $this->assertEquals(
            'bar',
            Seq::findValue($generatorCreator(), 'FOO', false)
        );
    }

    public function testFindValueWithIterators()
    {
        $this->assertEquals(
            'BAR',
            Seq::findValue(new ArrayIterator(['foo' => 'bar', 'FOO' => 'BAR']), 'FOO')
        );
        $this->assertEquals(
            'bar',
            Seq::findValue(new ArrayIterator(['foo' => 'bar', 'FOO' => 'BAR']), 'FOO', false)
        );
    }

    public function testFindWithCallback()
    {
        $this->assertEquals(
            [1, 'foo'],
            Seq::find(
                ['bar', 'foo'],
                function ($value) {
                    return $value !== 'bar';
                },
                false // Should have no effect
            )
        );
        $this->assertEquals(
            'foo',
            Seq::findValue(
                ['bar', 'foo'],
                function ($value) {
                    return $value !== 0;
                },
                false // Should have no effect
            )
        );
    }

    public function testFindWithFunctionName()
    {
        $this->assertEquals(
            [1, 'sleep'],
            Seq::find(
                ['awake', 'sleep'],
                'sleep'
            )
        );
        $this->assertEquals(
            'sleep',
            Seq::findValue(
                ['awake', 'sleep' => 'sleep'],
                'sleep'
            )
        );
    }

    public function testMapWithSequencedArray()
    {
        $arr = [1, 2, 3];

        $result = [];
        foreach (Seq::map($arr, fn ($v) => $v + 1) as $k => $v) {
            $result[$k] = $v;
        }

        $this->assertSame(
            [2, 3, 4],
            $result
        );
    }

    public function testMapWithKeyedArray()
    {
        $arr = [
            'one' => 1,
            'two' => 2,
            'three' => 3
        ];

        $result = [];
        foreach (Seq::map($arr, fn ($v) => $v + 1) as $k => $v) {
            $result[$k] = $v;
        }

        $this->assertSame(
            [
                'one' => 2,
                'two' => 3,
                'three' => 4
            ],
            $result
        );
    }

    public function testMapWithGenerators()
    {
        $generator = function () {
            foreach ([1, 2, 3] as $v) {
                yield $v;
            }
        };

        $result = [];
        foreach (Seq::map($generator(), fn ($v) => $v + 1) as $k => $v) {
            $result[$k] = $v;
        }

        $this->assertSame(
            [2, 3, 4],
            $result
        );
    }

    public function testMapWithKeyedGenerators()
    {
        $generator = function () {
            $arr = [
                'one' => 1,
                'two' => 2,
                'three' => 3
            ];
            foreach ($arr as $k => $v) {
                yield $k => $v;
            }
        };

        $result = [];
        foreach (Seq::map($generator(), fn ($v) => $v + 1) as $k => $v) {
            $result[$k] = $v;
        }

        $this->assertSame(
            [
                'one' => 2,
                'two' => 3,
                'three' => 4
            ],
            $result
        );
    }

    public function testUniqueWithArray()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $arr = [1, 2, 2, 3, '3', $object1, $object1, $object2];
        $result = iterator_to_array(Seq::unique($arr));

        $this->assertSame(
            [0 => 1, 1 => 2, 3 => 3, 5 => $object1, 7 => $object2],
            $result
        );
    }

    public function testUniqueWithKeyedArray()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $arr = ['a' => 1, 'b' => 2, 0 => 2, -1 => 3, 1 => '3', 2 => $object1, 3 => $object1, 4 => $object2];
        $result = iterator_to_array(Seq::unique($arr));

        $this->assertSame(
            ['a' => 1, 'b' => 2, -1 => 3, 2 => $object1, 4 => $object2],
            $result
        );
    }

    public function testUniqueWithGenerator()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $generator = function () use ($object1, $object2) {
            yield from [1, 2, 2, 3, '3', $object1, $object1, $object2];
        };
        $result = iterator_to_array(Seq::unique($generator()));

        $this->assertSame(
            [0 => 1, 1 => 2, 3 => 3, 5 => $object1, 7 => $object2],
            $result
        );
    }

    public function testUniqueWithKeyedGenerator()
    {
        $object1 = new stdClass();
        $object2 = new stdClass();
        $generator = function () use ($object1, $object2) {
            yield from ['a' => 1, 'b' => 2, 0 => 2, -1 => 3, 1 => '3', 2 => $object1, 3 => $object1, 4 => $object2];
        };
        $result = iterator_to_array(Seq::unique($generator()));

        $this->assertSame(
            ['a' => 1, 'b' => 2, -1 => 3, 2 => $object1, 4 => $object2],
            $result
        );
    }

    public function testUniqueWithStringsIsCaseSensitiveByDefault()
    {
        $arr = ['foo', 'bar', 'FOO', 'BAR', 'foo'];
        $result = iterator_to_array(Seq::unique($arr));

        $this->assertSame(
            [0 => 'foo', 1 => 'bar', 2 => 'FOO', 3 => 'BAR'],
            $result
        );
    }

    public function testUniqueWithStringsCaseInsensitive()
    {
        $arr = ['foo', 'bar', 'FOO', 'BAR', 'foo'];
        $result = iterator_to_array(Seq::unique($arr, false));

        $this->assertSame(
            [0 => 'foo', 1 => 'bar'],
            $result
        );
    }
}
