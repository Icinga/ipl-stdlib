<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\Filter;

class FilterChainTest extends \PHPUnit\Framework\TestCase
{
    public function testYieldRules()
    {
        $filter = Filter::all(
            Filter::equal('a', 'b'),
            Filter::any(
                Filter::equal('c', 'd'),
                Filter::equal('e', 'f'),
                Filter::none(
                    Filter::equal('g', 'h')
                )
            )
        );

        $flat = [];
        foreach ($filter->yieldRules() as $rule) {
            $flat[] = $rule;
        }

        $this->assertCount(4, $flat);
        $this->assertContainsOnlyInstancesOf(Filter\Rule::class, $flat);
    }
}
