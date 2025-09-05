<?php

namespace ipl\Tests\Stdlib;

use InvalidArgumentException;
use ipl\Stdlib\Option;
use RuntimeException;

class OptionTest extends TestCase
{
    public function testMissingOption(): void
    {
        $object = new class {
            #[Option(required: true)]
            public string $foo;
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing required option 'foo'");

        $values = [];
        Option::resolveOptions($object, $values);
    }

    public function testRequiredOptionDoesNotAcceptNull(): void
    {
        $object = new class {
            #[Option(required: true)]
            public string $foo;
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Required option 'foo' must not be null");

        $values = ['foo' => null];
        Option::resolveOptions($object, $values);
    }

    public function testRequiredOption(): void
    {
        $object = new class {
            #[Option(required: true)]
            public string $foo;
        };

        $values = ['foo' => 'bar'];
        Option::resolveOptions($object, $values);

        $this->assertSame('bar', $object->foo);
        $this->assertEmpty($values);
    }

    public function testOptionalOption(): void
    {
        $object = new class {
            #[Option]
            public string $foo = '';
        };

        $values = [];
        Option::resolveOptions($object, $values);

        $this->assertSame('', $object->foo);
    }

    public function testNamedOption(): void
    {
        $object = new class {
            #[Option(name: 'bar')]
            public string $foo = '';
        };

        $values = ['bar' => 'baz'];
        Option::resolveOptions($object, $values);

        $this->assertSame('baz', $object->foo);
        $this->assertEmpty($values);
    }

    public function testNamedRequiredOption(): void
    {
        $object = new class {
            #[Option(name: 'bar', required: true)]
            public string $foo;
        };

        $values = ['bar' => 'baz'];
        Option::resolveOptions($object, $values);

        $this->assertSame('baz', $object->foo);
        $this->assertEmpty($values);
    }

    public function testOptionWithMultipleNames(): void
    {
        $object = new class {
            #[Option(name: ['foo', 'bar'])]
            public string $baz = '';
        };

        $values = ['foo' => 'baz', 'bar' => 'oof'];
        Option::resolveOptions($object, $values);

        $this->assertSame('baz', $object->baz);
        $this->assertSame(['bar' => 'oof'], $values);
    }

    public function testMethodAnnotation(): void
    {
        $object = new class {
            public string $foo = '';

            public string $bar = '';

            #[Option]
            public function setFoo(string $value): void
            {
                $this->foo = $value;
            }

            #[Option]
            public function bar(string $value): void
            {
                $this->bar = $value;
            }
        };

        $values = ['foo' => 'bar', 'bar' => 'baz'];
        Option::resolveOptions($object, $values);

        $this->assertSame('bar', $object->foo);
        $this->assertSame('baz', $object->bar);
        $this->assertEmpty($values);
    }

    public function testErroneousMethodAnnotation(): void
    {
        $object = new class {
            #[Option]
            public function setFoo(string $value, string $invalid): void
            {
            }
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to invoke method setFoo');

        $values = ['foo' => 'bar'];
        Option::resolveOptions($object, $values);
    }

    public function testErroneousPropertyAnnotation(): void
    {
        $object = new class {
            #[Option]
            public array $foo = [];
        };

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to set property foo');

        $values = ['foo' => 'bar'];
        Option::resolveOptions($object, $values);
    }

    public function testPropertyCasting(): void
    {
        $object = new class {
            #[Option]
            public string $string = '';

            #[Option]
            public int $int = 0;

            #[Option]
            public float $float = 0.0;

            #[Option]
            public bool $bool = false;
        };

        $values = [
            'string' => 123,
            'int'    => '123',
            'float'  => '123.456',
            'bool'   => '1'
        ];
        Option::resolveOptions($object, $values);

        $this->assertSame('123', $object->string);
        $this->assertSame(123, $object->int);
        $this->assertSame(123.456, $object->float);
        $this->assertSame(true, $object->bool);
        $this->assertEmpty($values);
    }

    public function testMethodParameterCasting(): void
    {
        $object = new class {
            public string $string = '';
            public int $int = 0;
            public float $float = 0.0;
            public bool $bool = false;

            #[Option]
            public function setString(string $value): void
            {
                $this->string = $value;
            }

            #[Option]
            public function setInt(int $value): void
            {
                $this->int = $value;
            }

            #[Option]
            public function setFloat(float $value): void
            {
                $this->float = $value;
            }

            #[Option]
            public function setBool(bool $value): void
            {
                $this->bool = $value;
            }
        };

        $values = [
            'string' => 123,
            'int'    => '123',
            'float'  => '123.456',
            'bool'   => '1'
        ];
        Option::resolveOptions($object, $values);

        $this->assertSame('123', $object->string);
        $this->assertSame(123, $object->int);
        $this->assertSame(123.456, $object->float);
        $this->assertSame(true, $object->bool);
        $this->assertEmpty($values);
    }
}
