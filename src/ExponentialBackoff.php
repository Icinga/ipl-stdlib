<?php

namespace ipl\Stdlib;

use Exception;
use LogicException;

class ExponentialBackoff
{
    /** @var int The minimum wait time for each retry in ms */
    protected $min;

    /** @var int The maximum wait time for each retry in ms */
    protected $max;

    /** @var int Number of retries to be performed before giving up */
    protected $retries;

    /** @var ?int The previous used retry wait time */
    protected $previousWaitTime;

    /**
     * Create a backoff duration with exponential strategy implementation.
     *
     * @param int $retries The number of retries to be used before given up.
     * @param int $min The minimum wait time to be used in milliseconds.
     * @param int $max The maximum wait time to be used in milliseconds.
     */
    public function __construct(int $retries = 1, int $min = 0, int $max = 0)
    {
        $this->retries = $retries;

        $this->setMin($min);
        $this->setMax($max);
    }

    /**
     * Get the minimum wait time
     *
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * Set the minimum wait time
     *
     * @param int $min
     *
     * @return $this
     */
    public function setMin(int $min): self
    {
        if ($min <= 0) {
            $min = 100; // Default minimum wait time 100 ms
        }

        $this->min = $min;

        return $this;
    }

    /**
     * Get the maximum wait time
     *
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * Set the maximum wait time
     *
     * @param int $max
     *
     * @return $this
     * @throws LogicException When the configured minimum wait time is greater than the maximum wait time
     */
    public function setMax(int $max): self
    {
        if ($max <= 0) {
            $max = 10000; // Default max wait time 10 seconds
        }

        $this->max = $max;

        if ($this->min > $this->max) {
            throw new LogicException('Max must be larger than min');
        }

        return $this;
    }

    /**
     * Get the configured number of retries
     *
     * @return int
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * Set number of retries to be used
     *
     * @param int $retries
     *
     * @return $this
     */
    public function setRetries(int $retries): self
    {
        $this->retries = $retries;

        return $this;
    }

    /**
     * Get a new wait time for the given attempt
     *
     * If the given attempt is the initial one, the min wait time is used. For all subsequent requests,
     * the previous wait time is simply multiplied by 2.
     *
     * @param int $attempt
     *
     * @return int
     */
    public function getWaitTime(int $attempt): int
    {
        if ($attempt === 0) {
            $this->previousWaitTime = null;
        }

        if ($this->previousWaitTime >= $this->max) {
            return $this->max;
        }

        $next = min(! $this->previousWaitTime ? $this->min : $this->previousWaitTime * 2, $this->max);
        $this->previousWaitTime = $next;

        return $next;
    }

    /**
     * Execute and retry the given callback
     *
     * @param callable(?Exception $err): mixed $callback The callback to be retried
     *
     * @return mixed
     * @throws Exception When the given callback rethrows an exception that can't be retried or max retries is reached
     */
    public function retry(callable $callback)
    {
        $attempt = 0;
        $previousErr = null;

        do {
            try {
                return $callback($previousErr);
            } catch (Exception $err) {
                if ($attempt >= $this->getRetries() || $err === $previousErr) {
                    throw $err;
                }

                $previousErr = $err;

                $sleep = $this->getWaitTime($attempt++);
                usleep($sleep * 1000);
            }
        } while ($attempt <= $this->getRetries());
    }
}
