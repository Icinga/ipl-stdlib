<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\Properties;

class TestClassUsingThePropertiesTrait implements \ArrayAccess
{
    use Properties;

    public function __construct()
    {
        $this->accessorsAndMutatorsEnabled = true;
    }

    public function mutateFoobarProperty()
    {
        return 'foobar';
    }

    public function mutateSpecialProperty($value)
    {
        return strtoupper($value);
    }
}
