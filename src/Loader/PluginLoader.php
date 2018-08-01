<?php

namespace ipl\Stdlib\Loader;

use InvalidArgumentException;

interface PluginLoader
{
    /**
     *
     *
     * @param $name
     * @return string|null
     */
    public function eventuallyGetClassByName($name);

    /**
     * @param $name
     * @return object|null
     */
    public function eventuallyLoad($name);

    /**
     * @param $name
     * @throws InvalidArgumentException
     * @return object
     */
    public function load($name);
}
