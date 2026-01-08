<?php

namespace ipl\Stdlib\Filter;

use ipl\Stdlib\Data;

trait MetaData
{
    /** @var ?Data */
    protected ?Data $metaData = null;

    public function metaData(): Data
    {
        if ($this->metaData === null) {
            $this->metaData = new Data();
        }

        return $this->metaData;
    }
}
