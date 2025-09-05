<?php

namespace ipl\Stdlib;

use Attribute;
use Generator;
use InvalidArgumentException;
use ipl\Stdlib\Contract\MethodAttribute;
use ipl\Stdlib\Contract\PropertyAttribute;
use IteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use Traversable;
use stdClass;

/**
 * Detect and return the PHP type of the given subject
 *
 * If subject is an object, the name of the object's class is returned, otherwise the subject's type.
 *
 * @param mixed $subject
 *
 * @return string
 */
function get_php_type($subject)
{
    if (is_object($subject)) {
        return get_class($subject);
    } else {
        return gettype($subject);
    }
}

/**
 * Get the array value of the given subject
 *
 * @param array<mixed>|object|Traversable $subject
 *
 * @return array<mixed>
 *
 * @throws InvalidArgumentException If subject type is invalid
 */
function arrayval($subject)
{
    if (is_array($subject)) {
        return $subject;
    }

    if ($subject instanceof stdClass) {
        return (array) $subject;
    }

    if ($subject instanceof Traversable) {
        // Works for generators too
        return iterator_to_array($subject);
    }

    throw new InvalidArgumentException(sprintf(
        'arrayval expects arrays, objects or instances of Traversable. Got %s instead.',
        get_php_type($subject)
    ));
}

/**
 * Get the first key of an iterable
 *
 * @param iterable<mixed> $iterable
 *
 * @return mixed The first key of the iterable if it is not empty, null otherwise
 */
function iterable_key_first($iterable)
{
    foreach ($iterable as $key => $_) {
        return $key;
    }

    return null;
}

/**
 * Get the first value of an iterable
 *
 * @param iterable<mixed> $iterable
 *
 * @return ?mixed
 */
function iterable_value_first($iterable)
{
    foreach ($iterable as $_ => $value) {
        return $value;
    }

    return null;
}

/**
 * Yield sets of items from a sorted traversable grouped by a specific criterion gathered from a callback
 *
 * The traversable must be sorted by the criterion. The callback must return at least the criterion,
 * but can also return value and key in addition.
 *
 * @param Traversable<mixed, mixed> $traversable
 * @param callable(mixed $value, mixed $key): array{0: mixed, 1?: mixed, 2?: mixed} $groupBy
 *
 * @return Generator
 */
function yield_groups(Traversable $traversable, callable $groupBy): Generator
{
    $iterator = new IteratorIterator($traversable);
    $iterator->rewind();

    if (! $iterator->valid()) {
        return;
    }

    list($criterion, $v, $k) = array_pad((array) $groupBy($iterator->current(), $iterator->key()), 3, null);
    $group = [$k ?? $iterator->key() => $v ?? $iterator->current()];

    $iterator->next();
    for (; $iterator->valid(); $iterator->next()) {
        list($c, $v, $k) = array_pad((array) $groupBy($iterator->current(), $iterator->key()), 3, null);
        if ($c !== $criterion) {
            yield $criterion => $group;

            $group = [];
            $criterion = $c;
        }

        $group[$k ?? $iterator->key()] = $v ?? $iterator->current();
    }

    yield $criterion => $group;
}

/**
 * Resolve an attribute on an object
 *
 * This function will resolve and apply the attribute on the given object. Depending on the attribute's target,
 * the attribute needs to implement the appropriate interface:
 *
 * - {@see PropertyAttribute} for properties
 * - {@see MethodAttribute} for methods
 *
 * Supported attribute flags:
 * - {@see Attribute::TARGET_PROPERTY}
 * - {@see Attribute::TARGET_METHOD}
 * - {@see Attribute::IS_REPEATABLE}
 *
 * @param class-string $attributeClass The attribute class to resolve. Must be an {@see Attribute}
 * @param object $object The object to resolve the attribute on
 * @param mixed ...$args Optional arguments to pass to the attribute's methods
 *
 * @return void
 *
 * @throws InvalidArgumentException If the given class is not a valid attribute
 */
function resolve_attribute(string $attributeClass, object $object, mixed &...$args): void
{
    $attrRef = new ReflectionClass($attributeClass);
    $attrAttributes = $attrRef->getAttributes(Attribute::class);
    if (empty($attrAttributes)) {
        throw new InvalidArgumentException(sprintf('Class %s is not an attribute', $attributeClass));
    }

    $attr = $attrAttributes[0]->newInstance();
    $objectRef = new ReflectionClass($object);

    if ($attr->flags & Attribute::TARGET_PROPERTY) {
        if (! $attrRef->implementsInterface(PropertyAttribute::class)) {
            throw new InvalidArgumentException(sprintf(
                'Class %s does not implement %s',
                $attributeClass,
                PropertyAttribute::class
            ));
        }

        foreach ($objectRef->getProperties() as $property) {
            $attributes = $property->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $attribute->newInstance()->applyToProperty($property, $object, ...$args);
            }
        }
    }

    if ($attr->flags & Attribute::TARGET_METHOD) {
        if (! $attrRef->implementsInterface(MethodAttribute::class)) {
            throw new InvalidArgumentException(sprintf(
                'Class %s does not implement %s',
                $attributeClass,
                MethodAttribute::class
            ));
        }

        foreach ($objectRef->getMethods() as $method) {
            $attributes = $method->getAttributes($attributeClass, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $attribute->newInstance()->applyToMethod($method, $object, ...$args);
            }
        }
    }
}
