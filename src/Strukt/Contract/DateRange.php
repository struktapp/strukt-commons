<?php

namespace Strukt\Contract;

/** Adds random-date and inclusive-range helpers to date-time values. */
abstract class DateRange extends DateCompare
{
    /**
     * Creates a random date at a second between this value and an end date.
     *
     * @param \DateTimeInterface $end Inclusive upper boundary.
     * @return static Random date created by the concrete date class.
     * @throws \InvalidArgumentException When `$end` precedes this date.
     */
    public function rand(\DateTimeInterface $end): static
    {
        if ($end < $this) {
            throw new \InvalidArgumentException('The range end must not precede the start.');
        }

        $timestamp = random_int($this->getTimestamp(), $end->getTimestamp());

        return static::fromTimestamp($timestamp);
    }

    /**
     * Checks whether this date is inside an inclusive range.
     *
     * @param \DateTimeInterface $start Inclusive lower boundary.
     * @param \DateTimeInterface $end Inclusive upper boundary.
     * @return bool True when this date falls within the range.
     */
    public function btwn(\DateTimeInterface $start, \DateTimeInterface $end): bool
    {
        if ($end < $start) {
            return false;
        }

        return $this->gte($start) && $this->lte($end);
    }
}
