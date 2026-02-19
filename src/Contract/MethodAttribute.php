<?php

namespace ipl\Stdlib\Contract;

use ReflectionMethod;
use RuntimeException;

/**
 * Interface for attributes that target methods
 */
interface MethodAttribute
{
    /**
     * Apply this attribute to the given method
     *
     * @param ReflectionMethod $method
     * @param object $object
     * @param mixed ...$args Additional arguments that may be needed to apply the attribute
     *
     * @return void
     *
     * @throws RuntimeException If method invocation fails
     */
    public function applyToMethod(ReflectionMethod $method, object $object, mixed &...$args): void;
}
