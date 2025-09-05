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
}
