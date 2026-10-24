# Dates — `when()`, `today()`, `period()`

## `when()` and `Strukt\DateTime`

`when(string|int $date = 'now')` creates a `Strukt\DateTime`. Accepted input:

- `'now'` → the configured `Today` date when one is set, otherwise the system
  clock.
- ordinary date strings (`'2024-01-01'`, `'+30 days'`, …),
- Unix timestamps (any numeric value that `isTimestamp()` accepts).

```php
$start = when();
$end = when('+30 days');

$random = $start->rand($end); // a random second within [start, end]
$end->gt($start);             // true
$start->btwn($start, $end);   // true
```

`Strukt\DateTime` is a real `DateTime` subclass, so every native `DateTime`
method and the whole `DateTimeInterface` surface are available. On top of
that:

| Method | Description |
| --- | --- |
| `when(DateTimeInterface|string|null $reference = null): string` | Human-readable description: `3 days ago`, `in 2 hours`, `just now`. |
| `gte($to): bool` | At or after `$to`. |
| `gt($to): bool` | After `$to`. |
| `lte($to): bool` | At or before `$to`. |
| `lt($to): bool` | Before `$to`. |
| `equals($to): bool` | Same exact instant (including microseconds). |
| `same($to): bool` | Same calendar date (`Y-m-d`). |
| `btwn($start, $end): bool` | Within an *inclusive* range. |
| `rand($end): static` | A random second between this date and `$end`. |
| `reset(): void` | Set the time to `00:00:00.000000`. |
| `last(): void` | Set the time to `23:59:59.999999`. |
| `clone(?string $how = null): static` | Independent copy, optionally `modify()`-ed. |
| `create(string $datetime, string $format = 'Y-m-d'): static` | Parse through `DateTime::createFromFormat`. |
| `fromTimestamp(int $timestamp): static` | Date from Unix seconds in the default timezone. |
| `isTimestamp(int|string $timestamp): bool` | Whether PHP can construct that timestamp. |

`btwn()` and `rand()` guard their arguments: a range where `$end` precedes
`$start` returns `false` (for `btwn`) or throws (for `rand`).

## Custom formats

The constructor accepts a second `$format` argument used both to parse string
input and as the output format for `__toString()`:

```php
use Strukt\DateTime;

$date = new DateTime('2024-01-01', 'Y-m-d');
echo $date;                 // 2024-01-01

$time = DateTime::create('10:30:00', 'H:i:s');
echo $time;                 // 10:30:00
```

Parse failures are strict: warnings count as errors and raise
`\InvalidArgumentException`.

## Human descriptions

```php
$weekAgo = when()->clone('-1 week')->when();
// e.g. "7 days ago"

$later = when()->clone('+14 days')->when(when());
// e.g. "in 2 weeks"
```

## Deterministic dates: `today()` and `period()`

`period()` controls an *inclusive* date range and can also simulate the
current calendar day — invaluable for reproducible tests and demo runs.

```php
$period = period();
$period->create(when('1900-01-01'), when('1963-12-31'));
$period->reset(when('1960-03-23'));

echo today()->format('Y-m-d'); // 1960-03-23

\Strukt\Today::withDate(when('1959-01-01'))->isValid(); // true

$period->reset(); // back to the real clock
```

`today()` returns a `Strukt\Today` instance. The same state is exposed
statically on `Strukt\Today`:

| Static | Description |
| --- | --- |
| `Today::makePeriod(DateTimeInterface $start, DateTimeInterface $end): void` | Set both inclusive boundaries; throws when `$end` precedes `$start`. |
| `Today::reset(?DateTimeInterface $date = null): void` | Clear the period and simulated day (`null`), or only set a simulated day. |
| `Today::hasPeriod(): bool` | Whether both boundaries are configured. |
| `Today::withDate(DateTimeInterface $date): DateRange` | Range result exposing `isValid()`; throws `\LogicException` when no period exists. |
| `Today::getState(?string $state = null): DateTime|array|null` | `'period.start'` / `'period.end'`, or a full `['today', 'start_date', 'end_date']` map. |

Dates created while a period is active are **validated**: a date or simulated
`today` outside the configured bounds raises `\OutOfBoundsException` at
construction time, so you cannot accidentally build test state outside your
scenario.

```php
$period = period(when('2024-01-01'), when('2024-12-31'));

$in = when('2024-06-01');           // ok
$out = when('2025-01-01');          // OutOfBoundsException

$period->reset();                   // clear
```

---

See also: [README.md](README.md) · [helpers.md](helpers.md) ·
[architecture.md](architecture.md)