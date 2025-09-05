<?php

namespace ipl\Stdlib\Contract;

use ReflectionProperty;

/**
 * Interface for attributes that target properties
 */
interface PropertyAttribute
{
    /**
     * Apply this attribute to the given property
     *
     * @param ReflectionProperty $property
     * @param object $object
     * @param mixed ...$args Additional arguments that may be needed to apply the attribute
     *
     * @return void
     */
    public function applyToProperty(ReflectionProperty $property, object $object, mixed &...$args): void;
}
