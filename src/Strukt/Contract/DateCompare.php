<?php

namespace Strukt\Contract;

/** Adds comparison helpers to mutable PHP date-time values. */
abstract class DateCompare extends \DateTime
{
    /**
     * Checks whether this date is at or after another instant.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when this date is greater than or equal to `$to`.
     */
    public function gte(\DateTimeInterface $to): bool
    {
        return $this->compare($to) >= 0;
    }

    /**
     * Checks whether this date is after another instant.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when this date is later than `$to`.
     */
    public function gt(\DateTimeInterface $to): bool
    {
        return $this->compare($to) > 0;
    }

    /**
     * Checks whether this date is at or before another instant.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when this date is less than or equal to `$to`.
     */
    public function lte(\DateTimeInterface $to): bool
    {
        return $this->compare($to) <= 0;
    }

    /**
     * Checks whether this date is before another instant.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when this date is earlier than `$to`.
     */
    public function lt(\DateTimeInterface $to): bool
    {
        return $this->compare($to) < 0;
    }

    /**
     * Checks whether two values represent the same instant.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when both instants match, including microseconds.
     */
    public function equals(\DateTimeInterface $to): bool
    {
        return $this->compare($to) === 0;
    }

    /**
     * Checks whether two values fall on the same calendar date.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return bool True when both values format to the same `Y-m-d` date.
     */
    public function same(\DateTimeInterface $to): bool
    {
        return $this->format('Y-m-d') === $to->format('Y-m-d');
    }

    /**
     * Compares two instants using Unix seconds and microseconds.
     *
     * @param \DateTimeInterface $to Date to compare with.
     * @return int Negative, zero, or positive according to chronological order.
     */
    private function compare(\DateTimeInterface $to): int
    {
        $left = (float) $this->format('U.u');
        $right = (float) $to->format('U.u');

        return $left <=> $right;
    }
}
