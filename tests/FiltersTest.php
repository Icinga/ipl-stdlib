<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\Contract\Filterable;
use ipl\Stdlib\Filter;
use ipl\Tests\Stdlib\FiltersTest\FiltersUser;

class FiltersTest extends \PHPUnit\Framework\TestCase
{
    /** @var Filterable */
    protected $filterable;

    public function setUp()
    {
        $this->filterable = new FiltersUser();
    }

    public function testFilterKeepsCurrentHierarchy()
    {
        $this->filterable->filter(Filter::equal('', ''));
        $this->filterable->filter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::all(
            Filter::equal('', ''),
            Filter::unequal('', '')
        ));
    }

    public function testFilterWrapsCurrentHierarchy()
    {
        $this->filterable->orFilter(Filter::equal('', ''));
        $this->filterable->filter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::all(
            Filter::any(Filter::equal('', '')),
            Filter::unequal('', '')
        ));
    }

    public function testOrFilterKeepsCurrentHierarchy()
    {
        $this->filterable->orFilter(Filter::equal('', ''));
        $this->filterable->orFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::any(
            Filter::equal('', ''),
            Filter::unequal('', '')
        ));
    }

    public function testOrFilterWrapsCurrentHierarchy()
    {
        $this->filterable->filter(Filter::equal('', ''));
        $this->filterable->orFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::any(
            Filter::all(Filter::equal('', '')),
            Filter::unequal('', '')
        ));
    }

    public function testNotFilterKeepsCurrentHierarchy()
    {
        $this->filterable->notFilter(Filter::equal('', ''));
        $this->filterable->notFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::all(
            Filter::none(Filter::equal('', '')),
            Filter::none(Filter::unequal('', ''))
        ));
    }

    public function testNotFilterWrapsCurrentHierarchy()
    {
        $this->filterable->orFilter(Filter::equal('', ''));
        $this->filterable->notFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::all(
            Filter::any(Filter::equal('', '')),
            Filter::none(Filter::unequal('', ''))
        ));
    }

    public function testOrNotFilterKeepsCurrentHierarchy()
    {
        $this->filterable->orNotFilter(Filter::equal('', ''));
        $this->filterable->orNotFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::any(
            Filter::none(Filter::equal('', '')),
            Filter::none(Filter::unequal('', ''))
        ));
    }

    public function testOrNotFilterWrapsCurrentHierarchy()
    {
        $this->filterable->filter(Filter::equal('', ''));
        $this->filterable->orNotFilter(Filter::unequal('', ''));

        $this->assertSameFilterHierarchy(Filter::any(
            Filter::all(Filter::equal('', '')),
            Filter::none(Filter::unequal('', ''))
        ));
    }

    protected function assertSameFilterHierarchy(Filter\Chain $expected)
    {
        $actual = $this->filterable->getFilter();

        $checkHierarchy = function ($expected, $actual) use (&$checkHierarchy) {
            $expectedArray = iterator_to_array($expected);
            $actualArray = iterator_to_array($actual);
            foreach ($expectedArray as $key => $rule) {
                $this->assertTrue(isset($actualArray[$key]));
                $this->assertInstanceOf(get_class($rule), $actualArray[$key]);
                if ($rule instanceof Filter\Chain) {
                    $checkHierarchy($rule, $actualArray[$key]);
                }
            }
        };

        $checkHierarchy($expected, $actual);
    }
}
