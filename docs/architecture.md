# Strukt Commons — Architecture

This document explains how `strukt/commons` is built: the layering, the class
hierarchy, the immutability model, how global helpers and process-local state
work, and the deliberate compatibility choices. It assumes you have skimmed
the [reference guide](README.md).

## Positioning

Strukt Commons is the assembly-line of the broader Strukt ecosystem: dozens of
small, dependency-light utilities that other packages rely on. Its design is
shaped by three constraints:

1. **Layered.** `require` is `php: >=8.2` plus `strukt/base`, the lower-level
   package that owns the lowest primitives (`alias()`, `raise()`/`Strukt\Raise`,
   `helper_add()` with the helper registry, and the `app()` container accessor).
   Commons builds its utilities on top of those.
2. **Coexistence.** Global helpers register conditionally, because older Strukt
   packages provide the same function names.
3. **Process-local state.** Values such as the registry, the message stack, and
   the simulated calendar all live in static memory and are reset per PHP
   process — nothing touches the filesystem at runtime except configuration
   loading.

## Directory layout

```
strukt4/commons
├── autoload.php            Convenience bootstrap (throws without vendor/)
├── cfg/                    INI files loaded by config() on first access
├── composer.json
├── phpunit.xml
├── src/
│   ├── helpers.php         Conditionally-declared global functions
│   └── Strukt/
│       ├── ValueObject.php  Base wrapper (yield/equals/clone/create)
│       ├── Str.php          Chainable byte-oriented strings
│       ├── Arr.php          Chainable arrays (+ array_all polyfill)
│       ├── Collection.php   Dot-path collection (+ static dot/attach/detach)
│       ├── Registry.php     Process-wide singleton over Collection
│       ├── DateTime.php     Mutable date value (human labels, formats)
│       ├── Today.php        Static simulated calendar + inclusive period
│       ├── TokenQuery.php   key:value|key:value parser/serializer
│       ├── Stack.php        Concrete bounded message-stack handle
│       └── Contract/
│           ├── CollectionInterface.php
│           ├── Stack.php             Abstract bounded stack (static storage)
│           ├── DateCompare.php       DateTime comparison mixin
│           └── DateRange.php         DateTime range/random mixin
└── tests/                  Pest suites (Unit/*Test.php)
```

## Layering

The package has four cooperating layers:

```
global helpers (src/helpers.php)          facade, conditional registration
        │  delegates
        ▼
concrete utilities (Strukt\*)             the fluent, real API
        │  extends / implements
        ▼
contracts & bases (Strukt\Contract\*,     shared behaviour and shapes
                        ValueObject)
        │  extends
        ▼
PHP standard library                     DateTime, array_*, str_*, json_*
```

### 1. Global helper layer (`src/helpers.php`)

Every function is wrapped in `if (!function_exists(...))`. This is a
deliberate compatibility seam: a project may autoload both this package and an
older Strukt package that already declared `str()`, `arr()`, `dot()`, and so
on. Whichever package is loaded first wins; runtime behaviour stays
predictable.

Helpers are thin facades — they construct a wrapper and return it, or forward
to a static method:

- `str()` → `new Str`
- `arr()` → `Arr::from`
- `collect()` → `new Collection`
- `dot()` / `attach()` / `detach()` → `Collection::dot|attach|detach`
- `when()` / `today()` / `period()` → `DateTime` / `Today`
- `token()` / `tokenize()` → `TokenQuery`
- `stack()` → `Stack`
- `reg()` → `Registry`
- `json()` → an anonymous helper object
- `config()` → registry-backed INI config

Two helpers are slightly more than facades:

- `when()` **normalizes input**. `'now'` maps to the simulated `Today` date;
  numeric values that `DateTime::isTimestamp()` accepts become timestamps;
  anything else is passed as a date string. This gives callers one entry point
  regardless of how a date is represented.
- `period()` returns an anonymous *controller* object wrapping two static
  calls (`Today::makePeriod` / `Today::reset`) into a fluent,
  one-expression API.

### 2. Concrete utilities

Each class owns one concern and leans on PHP built-ins rather than pulling in
dependencies:

| Class | Backing PHP | Notes |
| --- | --- | --- |
| `Str` | `strlen`, `substr`, `strpos`, `str_replace`, `preg_*` | Byte-oriented, documented as such. |
| `Arr` | `array_*`, `array_*_key`, `sort`, ... | Full pointer emulation + fluent transforms. |
| `Collection` | plain arrays + `explode('.')` addressing | Mutable by design. |
| `Registry` | `Collection` | Singleton, one backing store. |
| `DateTime` | native `DateTime` | Mutable, format-aware, period-validated. |
| `Today` | static `DateTime` fields | Simulates "now". |
| `TokenQuery` | `explode('|')`/`explode(':')` | Round-trips tokens. |
| `Stack` | static array + `array_slice` | Bounded history. |

### 3. Contracts and shared bases

The `Contract\` namespace separates *what* from *how*:

- `CollectionInterface` is implemented by both `Collection` and `Registry`,
  so any dot-path store is interchangeable behind one shape.
- `Contract\Stack` (abstract) owns the static message store and the bounded
  eviction rules; the concrete `Strukt\Stack` adds only a handle string. The
  contract is abstract rather than an interface **on purpose**: the bounded
  capacity and eviction are centralized, and the concrete class stays tiny.
- `DateCompare` (abstract, extends `DateTime`) adds the comparison surface —
  `gte/gt/lte/lt/equals/same` — implemented against a microsecond-precise
  floating-point comparison (`format('U.u') <=>`). Both `DateTime` and
  `Today` inherit it.
- `DateRange` (abstract, extends `DateCompare`) layers on
  `rand()` (random second between two instants) and `btwn()` (inclusive
  range). `Today` uses the same machinery to validate dates against its
  configured period, which is why `withDate()` returns a `DateRange` typed
  result.

`ValueObject` is the root of the fluent wrappers. It deliberately keeps the
property type `mixed` because PHP does not allow a child class to *narrow* an
inherited property type — `Str` re-declares `protected mixed $value`, and
`Arr` uses the inherited slot. Subclasses decide per-operation whether a call
mutates the wrapper (`Arr::pop`, `Arr::unset`, `DateTime::reset`) or returns
a fresh wrapper (`Arr::push`, `Str::concat`); the API contract is spelled out
on the reader's side in the docs.

`Arr` is `abstract`: plain `new Arr(...)` is impossible, and the static
factory `Arr::from()` produces an anonymous concrete subclass when called as
`Arr::from()` so the global `arr()` helper always gets an instantiable
wrapper. Subclassing (for domain-specific arrays) works through **late static
binding** in `from()`, `create()`, and the `new static(...)` calls scattered
through push/merge/map/etc., so a subclass preserves its own type across every
transform.

### 4. The `array_all` polyfill

PHP 8.4 introduced `array_all()`; `Arr.php` declares a `function_exists()`
guarded equivalent so the package runs on PHP 8.2 without it. Same pattern as
the helper layer: provide the primitive, never collide with the platform.

## State model

All shareable state is **static and process-local**, kept deliberately minimal:

| Owner | State | Reset point |
| --- | --- | --- |
| `Registry` | `$registry` singleton (+ `today` seed) | `Registry::resetInstance()` |
| `Today` | `$start`, `$end`, `$today` | `Today::reset()`, `period()->reset()` |
| `Contract\Stack` | `$messages`, `$limit` | `Stack::clear()`, `Stack::limit()` |
| `Raise` (strukt/base) | `$errors` history | `Raise::reset()` |
| `Alias` (strukt/base) | `$aliases` | `Alias::reset()` |

The registry seeds `today` with the *currently configured* calendar day at
construction (line in `Registry::__construct`), which ties the two together:
re-`resetInstance()`ing the registry after changing the simulated date keeps
`today` consistent.

### Period enforcement

When `Today::makePeriod()` has been called, construction of both `DateTime`
and `Today` validates the candidate date; an out-of-bounds date raises
`\OutOfBoundsException`. This turns "the script used a date outside the test
scenario" from a silent data bug into a loud failure at construction time. The
boundary check itself is `btwn()` (inclusive) inherited from `DateRange`.

`Today::reset(null)` clears both the period and the simulated day; passing a
date only changes the simulated calendar day while leaving the period in
place, which allows "freeze a day inside an active period" workflows.

## Error strategy

Guards are local and explicit, never swallowed:

- Shape/type violations in `Arr` (`sum`, `product`, `join`, `column`,
  `distinct`, `tokenize`, `order`, …) throw `\InvalidArgumentException`.
- Date parse and range violations throw `\InvalidArgumentException`
  (`create`, `rand`) or `\OutOfBoundsException` (period).
- JSON always uses `JSON_THROW_ON_ERROR` → `\JsonException`.
- `Str::replaceFirst` and `Stack::get()` validate their regex and throw on
  invalid patterns.
- The legacy `raise()` path still surfaces as `Strukt\Raise` (a
  `RuntimeException`, code default 500) so old call sites keep working; the
  class separately records a process-local error history for debugging.

There are no custom caught-and-rethrown wrappers; built-in SPL exceptions
carry the errors, keeping the package dependency-free and the semantics
obvious.

## Optional integrations

Two integrations are *proactive but optional* — they degrade gracefully when
the sibling package is absent:

1. **`ramsey/uuid`** in `uuid()`. If the class exists, every version and
   option set is delegated. Otherwise a native version-4 value is built from
   `random_bytes()` (variant and version bits are OR-ed in explicitly).
2. **Symfony `EnglishInflector`** in `plural()`/`singular()`. If present, its
   results are used; otherwise compact regex fallbacks cover `-ies`, `-es`,
   and final-`s` noun forms.

Both checks are `class_exists()` guards — no Composer requirements, no
optional dependencies to install, no autoloading tricks. This is the same
philosophy as the `function_exists()` helper seam pushed a level further: the
package never *requires* a dependency to appear complete.

## Configuration loading

`config()` lazily loads `cfg/*.ini` (parsed with `INI_SCANNER_TYPED`) on the
first call, namespacing each file by its basename, and stores the result on
the registry under `config`. Subsequent calls are pure registry reads/writes.
The shipped `cfg/app.ini` (`name=payroll`) is what makes `config('app.name')`
resolve out-of-the-box.

## Conventions worth copying

- **Dot-separated paths** are the one addressing language across the package:
  `Collection`, `Registry`, `config()`, the helper trio
  `dot/attach/detach`, and `Arr::level()` all speak it. One convention,
  reused everywhere.
- **Facades return wrappers, never raw strings** in the value-object layer —
  except where the shape demands it (`Str::split()` returns a list,
  `pad()`/`sort()`/`order()`/`diff()`/`cross()` return one-shot helper objects
  that yield either arrays or further `Arr`s). The one-shot helper is the
  package's pattern for multi-step operations that don't belong as multiple
  wrapper methods: `Arr::sort()->asc()`, `Arr::level()->prefix('x')->yield()`,
  `period()->create(...)->reset(...)`.
- **Fluent state that must be reverting** is captured with `will()`: traversal
  rules (`skip`, `jump`, `stopAt`) are snapshotted into a one-use object and
  cleared, so a configured wrapper doesn't carry hidden rules into unexpected
  subsequent calls.
- **Public API surface mirrors the docs.** Every method has a docblock with
  returns and throws; the `docs/README.md` reference is generated from that
  same surface.

## Testing

Tests are Pest-suites under `tests/Unit`, split by concern
(`ArrTest`, `StrTest`, `DateTest`, `CollectionTest`, `TokenQueryTest`,
`HelpersTest`). The suite is the executable contract for the behaviour
described in these documents; `composer test` runs it, and
`composer test:coverage` reports coverage when a driver is installed.

Suggested reading order after this document:

1. `docs/README.md` — the full user reference.
2. `src/helpers.php` — every global entry point in one place.
3. `src/Strukt/ValueObject.php` + one wrapper class (`Str` is the smallest).
4. `src/Strukt/Contract/` — the shared bases, especially `DateCompare`.