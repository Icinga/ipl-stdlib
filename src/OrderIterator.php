<?php

namespace ipl\Stdlib;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;

class OrderIterator implements IteratorAggregate
{
    /** @var int How many bits a value's order is shifted left to avoid collisions */
    const ORDER_PAD_BITS = 8;

    /** @var array Added values with their internal order as key */
    protected $values = [];

    /**
     * Add a value
     *
     * @param mixed $value
     * @param int $order The order of the value
     *
     * @return $this
     *
     * @throws InvalidArgumentException If order is not an integer
     */
    public function add($value, $order)
    {
        if (! is_int($order)) {
            throw new InvalidArgumentException(sprintf(
                '$order is not an integer, got %s instead',
                get_php_type($order)
            ));
        }

        $order = $order << self::ORDER_PAD_BITS; // TODO: May this end up lowering the order if it's already very high?
        while (isset($this->values[$order])) {
            $order++;
        }

        $this->values[$order] = $value;

        return $this;
    }

    /**
     * Add multiple values
     *
     * @param iterable ...$params Iterables that yield the order as key and value as value
     *
     * @return $this
     *
     * @throws InvalidArgumentException If a param is not iterable
     */
    public function extend(...$params)
    {
        foreach ($params as $i => $objects) {
            if (! is_iterable($objects)) {
                throw new InvalidArgumentException(sprintf(
                    'Param #%d is not iterable, got %s instead',
                    $i + 1,
                    get_php_type($objects)
                ));
            }

            foreach ($objects as $order => $object) {
                $this->add($object, $order);
            }
        }

        return $this;
    }

    public function getIterator()
    {
        $objects = $this->values;

        ksort($objects, SORT_NUMERIC);

        return new ArrayIterator(array_values($objects));
    }
}
