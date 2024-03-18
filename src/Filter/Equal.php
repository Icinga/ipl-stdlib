<?php

namespace ipl\Stdlib\Filter;

class Equal extends Condition
{
    /** @var bool */
    protected $ignoreCase = false;

    /**
     * Ignore case on both sides of the equation
     *
     * @return $this
     */
    public function ignoreCase()
    {
        $this->ignoreCase = true;

        return $this;
    }

    /**
    * Return whether this rule ignores case
    *
    * @return bool
    */
    public function ignoresCase()
    {
        return $this->ignoreCase;
    }

    public function sameAs(Rule $rule): bool
    {
        if (! $rule instanceof static) {
            return false;
        }

        if ($this->ignoresCase() !== $rule->ignoresCase()) {
            return false;
        }

        if (is_array($this->value)) {
            return array_diff($this->value, (array) $rule->getValue()) === [];
        }

        return parent::sameAs($rule);
    }
}
