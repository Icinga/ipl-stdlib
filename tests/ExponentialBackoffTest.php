<?php

namespace ipl\Tests\Stdlib;

use Exception;
use ipl\Stdlib\ExponentialBackoff;
use LogicException;

class ExponentialBackoffTest extends \PHPUnit\Framework\TestCase
{
    public function testInvalidMaxWaitTime()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Max must be larger than min');

        new ExponentialBackoff(1, 500, 100);
    }

    public function testMinAndMaxWaitTime()
    {
        $backoff = new ExponentialBackoff();
        $this->assertSame(100, $backoff->getMin());
        $this->assertSame(10 * 1000, $backoff->getMax());

        $backoff
            ->setMin(200)
            ->setMax(500);

        $this->assertSame(200, $backoff->getMin());
        $this->assertSame(500, $backoff->getMax());
    }

    public function testRetriesSetCorrectly()
    {
        $backoff = new ExponentialBackoff();

        $this->assertSame(1, $backoff->getRetries());
        $this->assertSame(5, $backoff->setRetries(5)->getRetries());
        $this->assertNotSame(10, $backoff->setRetries(5)->getRetries());
    }

    public function testGetWaitTime()
    {
        $backoff = new ExponentialBackoff(100, 1000);

        $this->assertSame($backoff->getMin(), $backoff->getWaitTime(0));
        $this->assertGreaterThan($backoff->getWaitTime(0), $backoff->getWaitTime(1));
        $this->assertGreaterThan($backoff->getWaitTime(1), $backoff->getWaitTime(2));
        $this->assertSame($backoff->getMax(), $backoff->getWaitTime(3));
    }

    public function testExecutionRetries()
    {
        $backoff = new ExponentialBackoff(10);
        $attempt = 0;
        $result = $backoff->retry(function (Exception $err = null) use (&$attempt) {
            if (++$attempt < 5) {
                throw new Exception('SQLSTATE[HY000] [2002] No such file or directory');
            }

            return 'succeeded';
        });

        $this->assertSame(5, $attempt);
        $this->assertSame('succeeded', $result);
    }

    public function testExecutionRetriesGivesUpAfterMaxRetries()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('SQLSTATE[HY000] [2002] No such file or directory');

        $backoff = new ExponentialBackoff(3);
        $backoff->retry(function (Exception $err = null) {
            throw new Exception('SQLSTATE[HY000] [2002] No such file or directory');
        });
    }
}
