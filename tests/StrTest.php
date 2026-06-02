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

    public function testStartsWithReturnsTrueForUtf8String()
    {
        $this->assertTrue(Str::startsWith('接続エラーが発生しました', '接続エラー'));
    }

    public function testStartsWithReturnsFalseForUtf8StringWithWrongPrefix()
    {
        $this->assertFalse(Str::startsWith('接続エラーが発生しました', 'エラーが'));
    }

    public function testEndsWithReturnsTrueIfStringEndsWithTheSpecifiedSubstring()
    {
        $this->assertTrue(Str::endsWith('config.ini', '.ini'));
    }

    public function testEndsWithReturnsFalseIfStringDoesNotEndWithTheSpecifiedSubstring()
    {
        $this->assertFalse(Str::endsWith('config.ini', '.php'));
    }

    public function testEndsWithReturnsTrueIfStringEndsWithTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertTrue(Str::endsWith('config.INI', '.INI', true));
    }

    public function testEndsWithReturnsFalseIfStringDoesNotEndWithTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertFalse(Str::endsWith('config.INI', '.ini', true));
    }

    public function testEndsWithReturnsTrueIfStringEndsWithTheSpecifiedSubstringCaseInsensitively()
    {
        $this->assertTrue(Str::endsWith('config.ini', '.INI', false));
    }

    public function testEndsWithReturnsFalseIfStringDoesNotEndWithTheSpecifiedSubstringCaseInsensitively()
    {
        $this->assertFalse(Str::endsWith('config.ini', '.PHP', false));
    }

    public function testEndsWithReturnsTrueForUtf8String()
    {
        $this->assertTrue(Str::endsWith('データベースへの接続に失敗しました', '失敗しました'));
    }

    public function testEndsWithReturnsFalseForUtf8StringWithWrongSuffix()
    {
        $this->assertFalse(Str::endsWith('データベースへの接続に失敗しました', '成功しました'));
    }

    public function testEndsWithReturnsTrueForUtf8StringCaseInsensitively()
    {
        $this->assertTrue(Str::endsWith('Der Schlüssel ist ungültig', 'UNGÜLTIG', false));
    }

    public function testContainsReturnsTrueIfStringContainsTheSpecifiedSubstring()
    {
        $this->assertTrue(Str::contains('MySQL server has gone away', 'server has gone away'));
    }

    public function testContainsReturnsFalseIfStringDoesNotContainTheSpecifiedSubstring()
    {
        $this->assertFalse(Str::contains('Query executed successfully', 'server has gone away'));
    }

    public function testContainsReturnsTrueIfStringContainsTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertTrue(Str::contains(
            'Lost connection to MySQL server during query',
            'Lost connection',
            true,
        ));
    }

    public function testContainsReturnsFalseIfStringDoesNotContainTheSpecifiedSubstringAndCaseIsStrict()
    {
        $this->assertFalse(Str::contains(
            'lost connection to MySQL server during query',
            'Lost connection',
            true,
        ));
    }

    public function testContainsReturnsTrueForUtf8String()
    {
        $this->assertTrue(Str::contains('データベースへの接続に失敗しました', '接続に失敗'));
    }

    public function testContainsReturnsFalseForUtf8StringWithWrongNeedle()
    {
        $this->assertFalse(Str::contains('データベースへの接続に失敗しました', '接続に成功'));
    }

    public function testIsEmptyReturnsTrueForNull()
    {
        $this->assertTrue(Str::isEmpty(null));
    }

    public function testIsEmptyReturnsTrueForEmptyString()
    {
        $this->assertTrue(Str::isEmpty(''));
    }

    public function testIsEmptyReturnsTrueForStringWithLeadingAndTrailingWhitespace()
    {
        $this->assertTrue(Str::isEmpty('   '));
    }

    public function testIsEmptyReturnsTrueForStringWithOnlyWhitespace()
    {
        $this->assertTrue(Str::isEmpty("\t\n"));
    }

    public function testIsEmptyReturnsFalseForZero()
    {
        $this->assertFalse(Str::isEmpty('0'));
    }

    public function testIsEmptyReturnsFalseForNonEmptyString()
    {
        $this->assertFalse(Str::isEmpty('Warning'));
    }

    public function testIsEmptyReturnsFalseForStringWithContentAndSurroundingWhitespace()
    {
        $this->assertFalse(Str::isEmpty('  Warning  '));
    }

    public function testIsEmptyReturnsFalseForUtf8String()
    {
        $this->assertFalse(Str::isEmpty('接続エラー'));
    }

    public function testIsEmptyReturnsFalseForUtf8StringWithSurroundingWhitespace()
    {
        $this->assertFalse(Str::isEmpty('  接続エラー  '));
    }

    public function testSymmetricSplitReturnsArrayPaddedToTheSizeSpecifiedByLimitUsingNullAsValueByDefault()
    {
        $this->assertSame(['foo', 'bar', null, null], Str::symmetricSplit('foo,bar', ',', 4));
    }

    public function testSymmetricSplitReturnsArrayPaddedToTheSizeSpecifiedByLimitUsingCustomValue()
    {
        $this->assertSame(['foo', 'bar', 'default', 'default'], Str::symmetricSplit('foo,bar', ',', 4, 'default'));
    }

    public function testSymmetricSplitReturnsUnpaddedArrayIfTheSizeOfTheExplodedStringIsLessThanLimit()
    {
        $this->assertSame(['foo', 'bar,baz'], Str::symmetricSplit('foo,bar,baz', ',', 2));
    }

    public function testSymmetricSplitReturnsUnpaddedArrayIfTheSizeOfTheExplodedStringIsEqualToLimit()
    {
        $this->assertSame(['foo', 'bar'], Str::symmetricSplit('foo,bar', ',', 2));
    }

    public function testSymmetricSplitForSymmetricArrayDestructuring()
    {
        list($user, $password) = Str::symmetricSplit('root', ':', 2);

        $this->assertSame('root', $user);
        $this->assertNull($password);
    }

    public function testSymmetricSplitWithEmptySubjectStillReturnsAnArrayPaddedToTheDesiredSize()
    {
        $this->assertSame([null, null], Str::symmetricSplit(null, ',', 2));
    }

    public function testTrimSplitTrimsWhitespacesAndSplitsByCommaByDefault()
    {
        $this->assertSame(['foo', 'bar', 'baz'], Str::trimSplit(' foo ,bar  , baz  '));
    }

    public function testTrimSplitRespectsCustomDelimiter()
    {
        $this->assertSame(['foo', 'bar', 'baz'], Str::trimSplit(' foo .bar  . baz  ', '.'));
    }

    public function testTrimSplitRespectsLimit()
    {
        $this->assertSame(['foo', 'bar  , baz'], Str::trimSplit(' foo ,bar  , baz  ', ',', 2));
    }
}
