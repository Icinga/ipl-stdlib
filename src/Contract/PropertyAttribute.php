<?php

namespace ipl\Stdlib\Contract;

use ReflectionProperty;
use RuntimeException;

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
     *
     * @throws RuntimeException If the property could not be set
     */
    public function applyToProperty(ReflectionProperty $property, object $object, mixed &...$args): void;
}
