<?php

namespace ipl\Stdlib\Filter;

interface Rule
{
    /**
     * Get whether the given rule is semantically the same as this one
     *
     * @param Rule $rule
     *
     * @return bool
     */
    public function sameAs(Rule $rule): bool;
}
