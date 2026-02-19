<?php

namespace ipl\Stdlib;

use Attribute;
use Generator;
use InvalidArgumentException;
use ipl\Stdlib\Contract\MethodAttribute;
use ipl\Stdlib\Contract\PropertyAttribute;
use ReflectionMethod;
use ReflectionProperty;
use RuntimeException;
use Throwable;

/**
 * Option attribute
 *
 * Use this class to denote that a property or method should be filled with an option value.
 * Options need to be resolved by use of the {@see Option::resolveOptions()} static method.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Option implements PropertyAttribute, MethodAttribute
{
    /**
     * The name(s) of the option
     *
     * If multiple names are given, only the first one found is used.
     *
     * @var ?array<string> if null, the property or method name is used
     */
    public ?array $name;

    /** @var bool Whether the option is required */
    public bool $required;

    /**
     * Create a new option
     *
     * @param null|string|string[] $name The name(s) of the option; if null, the property or method name is used
     * @param bool $required Whether the option is required
     */
    public function __construct(null|string|array $name = null, bool $required = false)
    {
        $this->name = $name !== null ? (array) $name : null;
        $this->required = $required;
    }

    public function applyToProperty(ReflectionProperty $property, object $object, mixed &...$args): void
    {
        [&$values] = $args;
        $names = $this->name ?? [$property->getName()];
        foreach ($this->extractValue($names, $values) as $name => $value) {
            try {
                $property->setValue($object, $value);
                unset($values[$name]);

                break;
            } catch (Throwable $e) {
                throw new RuntimeException('Failed to set property ' . $property->getName(), previous: $e);
            }
        }
    }

    public function applyToMethod(ReflectionMethod $method, object $object, mixed &...$args): void
    {
        [&$values] = $args;
        $names = $this->name;
        if ($names === null) {
            $methodName = $method->getName();
            if (str_starts_with($methodName, 'set')) {
                $methodName = lcfirst(substr($methodName, 3));
            }

            $names = [$methodName];
        }

        foreach ($this->extractValue($names, $values) as $name => $value) {
            try {
                $method->invoke($object, $value);
                unset($values[$name]);

                break;
            } catch (Throwable $e) {
                throw new RuntimeException('Failed to invoke method ' . $method->getName(), previous: $e);
            }
        }
    }

    /**
     * Find and yield a value from the given array
     *
     * @param array<string> $names
     * @param array<string, mixed> $values
     *
     * @return Generator<string, mixed>
     *
     * @throws InvalidArgumentException If a required option is missing or null
     */
    protected function extractValue(array $names, array $values): Generator
    {
        // Using a generator here to distinguish between an actual returned (yield) value and nothing at all (exhaust)
        foreach ($names as $name) {
            if (array_key_exists($name, $values)) {
                if ($this->required && $values[$name] === null) {
                    throw new InvalidArgumentException("Required option '$name' must not be null");
                }

                yield $name => $values[$name];
            }
        }

        if ($this->required) {
            throw new InvalidArgumentException("Missing required option '" . $names[0] . "'");
        }
    }

    /**
     * Resolve and assign values to the given target
     *
     * @param object $target The target to assign the values to
     * @param array<string, mixed> $values The values to assign
     *
     * @return void
     *
     * @throws InvalidArgumentException If a required option is missing or null
     * @throws RuntimeException If method invocation fails or the property could not be set
     */
    final public static function resolveOptions(object $target, array &$values): void
    {
        resolve_attribute(self::class, $target, $values);
    }
}
