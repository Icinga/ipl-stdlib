<?php

namespace ipl\Stdlib;

use Closure;
use Generator;

/**
 * Collection of utilities for traversables
 */
class Seq
{
    /**
     * Check if the traversable contains the given needle
     *
     * @param iterable $traversable
     * @param mixed $needle Might also be a closure
     * @param bool $caseSensitive Whether strings should be compared case-sensitive
     *
     * @return bool
     */
    public static function contains(iterable $traversable, mixed $needle, bool $caseSensitive = true): bool
    {
        return self::find($traversable, $needle, $caseSensitive)[0] !== null;
    }

    /**
     * Search in the traversable for the given needle and return its key and value
     *
     * @param iterable $traversable
     * @param mixed $needle Might also be a closure
     * @param bool $caseSensitive Whether strings should be compared case-sensitive
     *
     * @return array{0: mixed, 1: mixed} The found key and value, or [null, null] if nothing is found
     */
    public static function find(iterable $traversable, mixed $needle, bool $caseSensitive = true): array
    {
        $usesCallback = $needle instanceof Closure;
        if (! $usesCallback && $caseSensitive && is_array($traversable)) {
            return [array_search($needle, $traversable, true), $needle];
        }

        if (! $caseSensitive && is_string($needle) && ! $usesCallback) {
            $needle = strtolower($needle);
        }

        foreach ($traversable as $key => $item) {
            $originalItem = $item;
            if (! $caseSensitive && is_string($item)) {
                $item = strtolower($item);
            }

            if ($usesCallback) {
                /** @var Closure $needle */
                if ($needle($item)) {
                    return [$key, $originalItem];
                }
            } elseif ($item === $needle) {
                return [$key, $originalItem];
            }
        }

        return [null, null];
    }

    /**
     * Search in the traversable for the given needle and return its key
     *
     * @param iterable $traversable
     * @param mixed $needle Might also be a closure
     * @param bool $caseSensitive Whether strings should be compared case-sensitive
     *
     * @return mixed|null Null if nothing is found
     */
    public static function findKey(iterable $traversable, mixed $needle, bool $caseSensitive = true): mixed
    {
        return self::find($traversable, $needle, $caseSensitive)[0];
    }

    /**
     * Search in the traversable for the given needle and return its value
     *
     * @param iterable $traversable
     * @param mixed $needle Might also be a closure
     * @param bool $caseSensitive Whether strings should be compared case-sensitive
     *
     * @return mixed|null Null if nothing is found
     */
    public static function findValue(iterable $traversable, mixed $needle, bool $caseSensitive = true): mixed
    {
        $usesCallback = $needle instanceof Closure;
        if (! $usesCallback && $caseSensitive && is_array($traversable)) {
            return $traversable[$needle] ?? null;
        }

        if (! $caseSensitive && is_string($needle) && ! $usesCallback) {
            $needle = strtolower($needle);
        }

        foreach ($traversable as $key => $item) {
            if (! $caseSensitive && is_string($key)) {
                $key = strtolower($key);
            }

            if ($usesCallback) {
                /** @var Closure $needle */
                if ($needle($key)) {
                    return $item;
                }
            } elseif ($key === $needle) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Apply the callback to all elements of the given traversable, while preserving keys
     *
     * @param iterable $traversable
     * @param callable $callback
     *
     * @return Generator
     */
    public static function map(iterable $traversable, callable $callback): Generator
    {
        foreach ($traversable as $key => $value) {
            yield $key => $callback($value);
        }
    }

    /**
     * Yield every unique value of the given traversable with its original key
     *
     * @param iterable $traversable
     * @param bool $caseSensitive
     *
     * @return Generator
     */
    public static function unique(iterable $traversable, bool $caseSensitive = true): Generator
    {
        $seen = [];
        foreach ($traversable as $key => $value) {
            if (is_string($value)) {
                $needle = $caseSensitive ? $value : strtolower($value);
            } elseif (is_object($value)) {
                $needle = spl_object_hash($value);
            } else {
                $needle = $value;
            }

            if (! array_key_exists($needle, $seen)) {
                $seen[$needle] = true;
                yield $key => $value;
            }
        }
    }
}
