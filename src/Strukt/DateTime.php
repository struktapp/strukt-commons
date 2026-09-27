<?php

namespace Strukt;

use Strukt\Contract\DateRange;

/** Date-time value with range, comparison, and human-readable helpers. */
class DateTime extends DateRange
{
    /** Format used by `__toString()`. */
    private string $outputFormat = 'Y-m-d H:i:s';

    /**
     * Creates a mutable date-time value.
     *
     * An empty value uses the configured `Today` date. When `$format` is
     * supplied, strings are parsed with `DateTime::createFromFormat()` and
     * the same format is used for string conversion.
     *
     * @param \DateTimeInterface|string $datetime Date value or parseable string.
     * @param string $format Optional input and output format.
     * @throws \InvalidArgumentException When a formatted string cannot be parsed.
     * @throws \OutOfBoundsException When a configured period rejects the date.
     */
    public function __construct(\DateTimeInterface|string $datetime = '', string $format = '')
    {
        $source = $datetime;

        if ($source === '') {
            $source = new Today();
        } elseif ($format !== '' && is_string($source)) {
            $parsed = \DateTime::createFromFormat($format, $source);
            $errors = \DateTime::getLastErrors();
            if ($parsed === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
                throw new \InvalidArgumentException(sprintf(
                    'Unable to parse date [%s] with format [%s].',
                    $source,
                    $format,
                ));
            }

            $source = $parsed;
        }

        if ($source instanceof \DateTimeInterface) {
            if (Today::hasPeriod() && !Today::withDate($source)->isValid()) {
                throw new \OutOfBoundsException(sprintf(
                    'Date [%s] is outside the configured period.',
                    $source->format('Y-m-d H:i:s.u'),
                ));
            }

            parent::__construct($source->format('Y-m-d H:i:s.uP'));
        } else {
            $candidate = new \DateTime($source);
            if (Today::hasPeriod() && !Today::withDate($candidate)->isValid()) {
                throw new \OutOfBoundsException(sprintf(
                    'Date [%s] is outside the configured period.',
                    $candidate->format('Y-m-d H:i:s.u'),
                ));
            }

            parent::__construct($candidate->format('Y-m-d H:i:s.uP'));
        }

        $this->outputFormat = $format !== '' ? $format : 'Y-m-d H:i:s';
    }

    /**
     * Creates a date by parsing a string with an explicit format.
     *
     * @param string $datetime Date string to parse.
     * @param string $format Format accepted by `DateTime::createFromFormat()`.
     * @return static Parsed date of the called class.
     * @throws \InvalidArgumentException When parsing fails or emits warnings.
     */
    public static function create(string $datetime, string $format = 'Y-m-d'): static
    {
        $parsed = \DateTime::createFromFormat($format, $datetime);
        $errors = \DateTime::getLastErrors();
        if ($parsed === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to parse date [%s] with format [%s].',
                $datetime,
                $format,
            ));
        }

        return new static($parsed, $format);
    }

    /**
     * Creates a date from a Unix timestamp.
     *
     * @param int $timestamp Unix timestamp in seconds.
     * @return static Date of the called class in the default timezone.
     * @throws \OutOfBoundsException When a configured period rejects the date.
     */
    public static function fromTimestamp(int $timestamp): static
    {
        $date = new static('@' . $timestamp);
        $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $date;
    }

    /**
     * Checks whether a numeric value can be represented as a Unix timestamp.
     *
     * @param int|string $timestamp Candidate timestamp.
     * @return bool True when PHP can construct the timestamp.
     */
    public static function isTimestamp(int|string $timestamp): bool
    {
        if (!is_numeric($timestamp)) {
            return false;
        }

        try {
            new \DateTime('@' . (int) $timestamp);
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    /**
     * Describes this date relative to a reference date.
     *
     * @param \DateTimeInterface|string|null $reference Optional comparison date.
     * @return string Human-readable relative description.
     * @throws \Exception When a reference string cannot be parsed.
     */
    public function when(\DateTimeInterface|string|null $reference = null): string
    {
        $now = match (true) {
            $reference instanceof \DateTimeInterface => $reference,
            is_string($reference) => new \DateTime($reference),
            default => new \DateTime(),
        };

        $seconds = $this->getTimestamp() - $now->getTimestamp();
        $absolute = abs($seconds);

        if ($absolute < 5) {
            return 'just now';
        }

        $units = [
            'year' => 31536000,
            'month' => 2592000,
            'week' => 604800,
            'day' => 86400,
            'hour' => 3600,
            'minute' => 60,
            'second' => 1,
        ];

        foreach ($units as $unit => $size) {
            if ($absolute < $size) {
                continue;
            }

            $count = (int) floor($absolute / $size);
            $label = $count === 1 ? $unit : $unit . 's';

            return $seconds > 0 ? "in {$count} {$label}" : "{$count} {$label} ago";
        }

        return 'just now';
    }

    /**
     * Sets the time component to midnight.
     *
     * @return void
     */
    public function reset(): void
    {
        $this->setTime(0, 0, 0, 0);
    }

    /**
     * Sets the time component to the last microsecond of the day.
     *
     * @return void
     */
    public function last(): void
    {
        $this->setTime(23, 59, 59, 999999);
    }

    /**
     * Clones the date and optionally applies a relative modification.
     *
     * @param string|null $how Relative expression accepted by `modify()`.
     * @return static Independent cloned date.
     */
    public function clone(?string $how = null): static
    {
        $copy = clone $this;
        if ($how !== null) {
            $copy->modify($how);
        }

        return $copy;
    }

    /**
     * Formats the date using the configured output format.
     *
     * @return string Formatted date string.
     */
    public function __toString(): string
    {
        return $this->format($this->outputFormat);
    }
}
