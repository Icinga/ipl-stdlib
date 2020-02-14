<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\Str;

class StrTest extends TestCase
{
    public function testCamelDoesNothingIfStringHasNoDelimitersAndIsLowerCase()
    {
        $this->assertSame('noop', Str::camel('noop'));
    }

    public function testCamelFromSnakeCaseString()
    {
        $this->assertSame('snakeCase', Str::camel('snake_case'));
    }

    public function testCamelFromKebabCaseString()
    {

        $this->assertSame('kebabCase', Str::camel('kebab-case'));
    }

    public function testCamelFromSpaceDelimitedString()
    {
        $this->assertSame('spaceDelimited', Str::camel('space delimited'));
    }

    public function testStartsWithReturnsTrueIfStringStartsWithTheSpecifiedSubstring()
    {
        $this->assertTrue(Str::startsWith('foobar', 'foo'));
    }

    public function testStartsWithReturnsFalseIfStringDoesNotStartWithTheSpecifiedSubstring()
    {
        $this->assertFalse(Str::startsWith('foobar', 'bar'));
    }

    public function testStartsWithReturnsTrueIfStringStartsWithTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertTrue(Str::startsWith('FOOBAR', 'FOO', true));
    }

    public function testStartsWithReturnsFalseIfStringDoesNotStartWithTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertFalse(Str::startsWith('FOOBAR', 'foo', true));
    }
}
