<?php

namespace ipl\Stdlib;

use CallbackFilterIterator as SplCallbackFilterIterator;

/**
 * CallbackFilterIterator that behaves like generators in terms of validity before the first call to `rewind()`
 *
 * This class stems from the motivation to have a filter iterator
 * that uses callbacks, which behaves like any other iterator.
 * Just like the SPL's `CallbackFilterIterator`, but that works
 * differently: It isn't valid unless explicitly rewound first.
 *
 * Any other iterator, especially generators, doesn't need to be
 * rewound first. That's most obvious with a generator that yields
 * conditionally, just like a filter iterator would.
 *
 * Any call to `valid()`, `key()` or `current()` should return the
 * same result as the very first iteration step would.
 */
class CallbackFilterIterator extends SplCallbackFilterIterator
{
    /** @var bool Whether iteration has started */
    private $started = false;

    public function rewind(): void
    {
        $this->started = true;

        parent::rewind();
    }

    public function valid(): bool
    {
        if ($this->started) {
            return parent::valid();
        }

        // As per php-src, \CallbackFilterIterator::rewind() forwards the iterator to the first valid element
        // (https://github.com/php/php-src/blob/5cba2a3dc59ef2a0e432b05ab27f2b3ab4da48d0/ext/spl/spl_iterators.c#L1686)
        $this->rewind();

        return parent::valid();
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        if ($this->started) {
            return parent::key();
        }

        $this->rewind();

        return parent::key();
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        if ($this->started) {
            return parent::current();
        }

        $this->rewind();

        return parent::current();
    }
}
