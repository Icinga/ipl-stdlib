<?php

namespace ipl\Stdlib;

use ipl\Stdlib\Filter\Rule;

trait BaseFilter
{
    /** @var ?Rule Base filter */
    private ?Rule $baseFilter = null;

    /**
     * Get whether a base filter has been set
     *
     * @return bool
     */
    public function hasBaseFilter(): bool
    {
        return $this->baseFilter !== null;
    }

    /**
     * Get the base filter
     *
     * @return ?Rule
     */
    public function getBaseFilter(): ?Rule
    {
        return $this->baseFilter;
    }

    /**
     * Set the base filter
     *
     * @param ?Rule $baseFilter
     *
     * @return $this
     */
    public function setBaseFilter(?Rule $baseFilter = null): static
    {
        $this->baseFilter = $baseFilter;

        return $this;
    }
}
