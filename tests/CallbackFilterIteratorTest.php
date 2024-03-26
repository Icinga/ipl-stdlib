<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\CallbackFilterIterator;

class CallbackFilterIteratorTest extends TestCase
{
    public function testFirstIterationStep()
    {
        $iterator = new CallbackFilterIterator(new \ArrayIterator([1, 2, 3]), function (int $i) {
            return $i === 2;
        });

        foreach ($iterator as $k => $v) {
            $this->assertSame(1, $k);
            $this->assertSame(2, $v);
        }
    }

    public function testValidBeforeRewind()
    {
        $iterator = new CallbackFilterIterator(new \ArrayIterator([1, 2, 3]), function (int $i) {
            return $i === 2;
        });

        $this->assertTrue($iterator->valid());

        $iterator->rewind();

        $this->assertTrue($iterator->valid());
    }

    public function testKeyBeforeRewind()
    {
        $iterator = new CallbackFilterIterator(new \ArrayIterator([1, 2, 3]), function (int $i) {
            return $i === 2;
        });

        $this->assertSame(1, $iterator->key());

        $iterator->rewind();

        $this->assertSame(1, $iterator->key());
    }

    public function testCurrentBeforeRewind()
    {
        $iterator = new CallbackFilterIterator(new \ArrayIterator([1, 2, 3]), function (int $i) {
            return $i === 2;
        });

        $this->assertSame(2, $iterator->current());

        $iterator->rewind();

        $this->assertSame(2, $iterator->current());
    }

    public function testInvalidBeforeRewind()
    {
        $iterator = new CallbackFilterIterator(new \ArrayIterator([1, 2, 3]), function (int $i) {
            return $i === 4;
        });

        $this->assertFalse($iterator->valid());
        $this->assertNull($iterator->key());
        $this->assertNull($iterator->current());
    }
}
