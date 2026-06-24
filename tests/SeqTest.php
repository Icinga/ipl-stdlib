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
        $this->assertSame(
            [null, null],
            Seq::find(['foo' => 'bar', 'oof' => 'BAR'], 'missing')
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

    public function testUniqueWithArray(): void
    {
        $firstObject = new stdClass();
        $secondObject = new stdClass();
        $values = [1, 2, 2, 3, '3', $firstObject, $firstObject, $secondObject];
        $result = iterator_to_array(Seq::unique($values));

        $this->assertSame(
            [0 => 1, 1 => 2, 3 => 3, 4 => '3', 5 => $firstObject, 7 => $secondObject],
            $result,
        );
    }

    public function testUniqueWithKeyedArray(): void
    {
        $firstObject = new stdClass();
        $secondObject = new stdClass();
        $values = [
            'first-int'        => 1,
            'second-int'       => 2,
            'duplicate-int'    => 2,
            'different-int'    => 3,
            'string-int'       => '3',
            'first-object'     => $firstObject,
            'duplicate-object' => $firstObject,
            'second-object'    => $secondObject,
        ];
        $result = iterator_to_array(Seq::unique($values));

        $this->assertSame(
            [
                'first-int'     => 1,
                'second-int'    => 2,
                'different-int' => 3,
                'string-int'    => '3',
                'first-object'  => $firstObject,
                'second-object' => $secondObject,
            ],
            $result,
        );
    }

    public function testUniqueWithGenerator(): void
    {
        $firstObject = new stdClass();
        $secondObject = new stdClass();
        $generator = function () use ($firstObject, $secondObject) {
            yield from [1, 2, 2, 3, '3', $firstObject, $firstObject, $secondObject];
        };
        $result = iterator_to_array(Seq::unique($generator()));

        $this->assertSame(
            [0 => 1, 1 => 2, 3 => 3, 4 => '3', 5 => $firstObject, 7 => $secondObject],
            $result,
        );
    }

    public function testUniqueWithKeyedGenerator(): void
    {
        $firstObject = new stdClass();
        $secondObject = new stdClass();
        $generator = function () use ($firstObject, $secondObject) {
            yield from [
                'first-int'        => 1,
                'second-int'       => 2,
                'duplicate-int'    => 2,
                'different-int'    => 3,
                'string-int'       => '3',
                'first-object'     => $firstObject,
                'duplicate-object' => $firstObject,
                'second-object'    => $secondObject,
            ];
        };
        $result = iterator_to_array(Seq::unique($generator()));

        $this->assertSame(
            [
                'first-int'     => 1,
                'second-int'    => 2,
                'different-int' => 3,
                'string-int'    => '3',
                'first-object'  => $firstObject,
                'second-object' => $secondObject,
            ],
            $result,
        );
    }

    public function testUniqueWithStringsIsCaseSensitiveByDefault(): void
    {
        $values = ['icinga.com', 'host.name', 'ICINGA.COM', 'HOST.NAME', 'icinga.com'];
        $result = iterator_to_array(Seq::unique($values));

        $this->assertSame(
            [0 => 'icinga.com', 1 => 'host.name', 2 => 'ICINGA.COM', 3 => 'HOST.NAME'],
            $result,
        );
    }

    public function testUniqueWithStringsCaseInsensitive(): void
    {
        $values = ['icinga.com', 'host.name', 'ICINGA.COM', 'HOST.NAME', 'icinga.com'];
        $result = iterator_to_array(Seq::unique($values, false));

        $this->assertSame(
            [0 => 'icinga.com', 1 => 'host.name'],
            $result,
        );
    }

    public function testUniqueKeepsDifferentScalarTypesDistinct(): void
    {
        $values = [
            'int-zero'        => 0,
            'string-zero'     => '0',
            'false'           => false,
            'null'            => null,
            'empty-string'    => '',
            'int-one'         => 1,
            'string-one'      => '1',
            'true'            => true,
            'float-one'       => 1.0,
            'float-fraction'  => 1.2,
            'string-fraction' => '1.2',
        ];

        $this->assertSame(
            $values,
            iterator_to_array(Seq::unique($values)),
        );
    }

    public function testUniqueRemovesOnlySameTypedScalarDuplicates(): void
    {
        $values = [
            'first-int-zero'      => 0,
            'second-int-zero'     => 0,
            'first-string-zero'   => '0',
            'second-string-zero'  => '0',
            'first-false'         => false,
            'second-false'        => false,
            'first-null'          => null,
            'second-null'         => null,
            'first-empty-string'  => '',
            'second-empty-string' => '',
            'first-float-one'     => 1.0,
            'second-float-one'    => 1.0,
        ];

        $this->assertSame(
            [
                'first-int-zero'     => 0,
                'first-string-zero'  => '0',
                'first-false'        => false,
                'first-null'         => null,
                'first-empty-string' => '',
                'first-float-one'    => 1.0,
            ],
            iterator_to_array(Seq::unique($values)),
        );
    }

    public function testUniqueAcceptsArraysAsValues(): void
    {
        $values = [
            'first-list'             => [0],
            'second-list'            => [0],
            'string-zero-list'       => ['0'],
            'false-list'             => [false],
            'null-list'              => [null],
            'empty-string-list'      => [''],
            'nested-int'             => [['value' => 0]],
            'nested-string'          => [['value' => '0']],
            'keyed-array'            => ['a' => 1, 'b' => 2],
            'same-order-keyed-array' => ['a' => 1, 'b' => 2],
            'reordered-keyed-array'  => ['b' => 2, 'a' => 1],
        ];

        $this->assertSame(
            [
                'first-list'            => [0],
                'string-zero-list'      => ['0'],
                'false-list'            => [false],
                'null-list'             => [null],
                'empty-string-list'     => [''],
                'nested-int'            => [['value' => 0]],
                'nested-string'         => [['value' => '0']],
                'keyed-array'           => ['a' => 1, 'b' => 2],
                'reordered-keyed-array' => ['b' => 2, 'a' => 1],
            ],
            iterator_to_array(Seq::unique($values)),
        );
    }

    public function testUniqueCaseInsensitiveComparisonOnlyAppliesToDirectStrings(): void
    {
        $values = [
            'direct-string'                    => 'icinga.com',
            'direct-string-duplicate'          => 'ICINGA.COM',
            'array-string'                     => ['icinga.com'],
            'array-string-with-different-case' => ['ICINGA.COM'],
        ];

        $this->assertSame(
            [
                'direct-string'                    => 'icinga.com',
                'array-string'                     => ['icinga.com'],
                'array-string-with-different-case' => ['ICINGA.COM'],
            ],
            iterator_to_array(Seq::unique($values, false)),
        );
    }

    public function testUniqueKeepsObjectDistinctFromStringMatchingItsHash(): void
    {
        $object = new stdClass();
        $otherObject = new stdClass();
        $values = [
            'object'             => $object,
            'object-hash-string' => spl_object_hash($object),
            'same-object'        => $object,
            'other-object'       => $otherObject,
        ];

        $this->assertSame(
            [
                'object'             => $object,
                'object-hash-string' => spl_object_hash($object),
                'other-object'       => $otherObject,
            ],
            iterator_to_array(Seq::unique($values)),
        );
    }

    public function testUniqueAcceptsResourcesAsValues(): void
    {
        $stream = fopen('php://memory', 'rb');
        if ($stream === false) {
            $this->fail('Failed to open a memory stream');
        }

        $values = [
            'resource'           => $stream,
            'resource-id-as-int' => get_resource_id($stream),
            'same-resource'      => $stream,
        ];

        try {
            $this->assertSame(
                [
                    'resource'           => $stream,
                    'resource-id-as-int' => get_resource_id($stream),
                ],
                iterator_to_array(Seq::unique($values)),
            );
        } finally {
            fclose($stream);
        }
    }
}
