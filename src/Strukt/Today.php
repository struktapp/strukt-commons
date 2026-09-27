<?php

namespace Strukt;

use Strukt\Contract\DateRange;

/** Configurable current day and optional inclusive date period. */
class Today extends DateRange
{
    /** Inclusive start of the configured period. */
    private static ?\DateTime $start = null;
    /** Inclusive end of the configured period. */
    private static ?\DateTime $end = null;
    /** Simulated calendar day, or `null` for the system day. */
    private static ?\DateTime $today = null;

    /**
     * Creates the configured current date with the current clock time.
     *
     * @throws \OutOfBoundsException When the simulated day is outside the period.
     */
    public function __construct()
    {
        if (static::$today === null) {
            static::$today = new \DateTime();
        }

        $current = static::reMake();
        parent::__construct($current->format('Y-m-d H:i:s.uP'));

        if (static::hasPeriod() && !$this->btwn(static::$start, static::$end)) {
            throw new \OutOfBoundsException(sprintf(
                'Today [%s] is outside the configured period.',
                $current->format('Y-m-d H:i:s'),
            ));
        }
    }

    /**
     * Rebuilds the configured day with the current time of day.
     *
     * @return \DateTime Current date using the simulated calendar day.
     */
    private static function reMake(): \DateTime
    {
        if (static::$today === null) {
            static::$today = new \DateTime();
        }

        $now = new \DateTime();
        return new \DateTime(sprintf(
            '%s %s',
            static::$today->format('Y-m-d'),
            $now->format('H:i:s.uP'),
        ));
    }

    /**
     * Returns the current simulated date or period state.
     *
     * @param string|null $state `period.start` or `period.end` for one boundary.
     * @return \DateTime|array|null Requested boundary or complete state map.
     */
    public static function getState(?string $state = null): \DateTime|array|null
    {
        return match ($state) {
            'period.start' => static::$start,
            'period.end' => static::$end,
            default => [
                'today' => static::reMake(),
                'start_date' => static::$start,
                'end_date' => static::$end,
            ],
        };
    }

    /**
     * Clears period state and optionally sets a simulated calendar day.
     *
     * Passing `null` clears both the period and simulated day. Passing a date
     * changes only the simulated day and keeps an existing period.
     *
     * @param \DateTimeInterface|null $date Simulated day, or `null` to reset.
     * @return void
     */
    public static function reset(?\DateTimeInterface $date = null): void
    {
        if ($date === null) {
            static::$start = null;
            static::$end = null;
        }

        static::$today = $date === null
            ? null
            : new \DateTime($date->format('Y-m-d H:i:s.uP'));
    }

    /**
     * Configures an inclusive allowed date period.
     *
     * @param \DateTimeInterface $start Inclusive lower boundary.
     * @param \DateTimeInterface $end Inclusive upper boundary.
     * @return void
     * @throws \InvalidArgumentException When `$end` precedes `$start`.
     */
    public static function makePeriod(\DateTimeInterface $start, \DateTimeInterface $end): void
    {
        if ($end < $start) {
            throw new \InvalidArgumentException(sprintf(
                'end_date [%s] must not precede start_date [%s].',
                $end->format('Y-m-d H:i:s'),
                $start->format('Y-m-d H:i:s'),
            ));
        }

        static::$start = new \DateTime($start->format('Y-m-d H:i:s.uP'));
        static::$end = new \DateTime($end->format('Y-m-d H:i:s.uP'));
    }

    /**
     * Checks whether both period boundaries are configured.
     *
     * @return bool True when an active period exists.
     */
    public static function hasPeriod(): bool
    {
        return static::$start !== null && static::$end !== null;
    }

    /**
     * Creates a range result for validating a date against the active period.
     *
     * @param \DateTimeInterface $date Date to validate.
     * @return DateRange Range object exposing `isValid()`.
     * @throws \LogicException When no period has been configured.
     */
    public static function withDate(\DateTimeInterface $date): DateRange
    {
        if (!static::hasPeriod()) {
            throw new \LogicException('A date period has not been configured.');
        }

        $start = static::$start;
        $end = static::$end;

        return new class($date, $start, $end) extends DateRange {
            /** Whether the candidate date falls inside the configured period. */
            private bool $valid;

            /**
             * Creates a date range validation result.
             *
             * @param \DateTimeInterface $date Candidate date.
             * @param \DateTimeInterface $start Inclusive lower boundary.
             * @param \DateTimeInterface $end Inclusive upper boundary.
             */
            public function __construct(\DateTimeInterface $date, \DateTimeInterface $start, \DateTimeInterface $end)
            {
                parent::__construct($date->format('Y-m-d H:i:s.uP'));
                $this->valid = $this->btwn($start, $end);
            }

            /**
             * Reports whether the candidate date is inside the period.
             *
             * @return bool True when the date is within both boundaries.
             */
            public function isValid(): bool
            {
                return $this->valid;
            }
        };
    }
}
