# Registry, configuration & related helpers

## `reg()` and `Strukt\Registry`

`Strukt\Registry` is a **process-wide singleton** backed by a
`Collection`, so dot-paths work exactly as in [collections.md](collections.md).
`reg()` is its facade:

```php
reg('user.name', 'Ada');   // writes and returns the value
echo reg('user.name');     // Ada
reg();                     // the Registry object
```

Reading and writing:

| Member | Description |
| --- | --- |
| `Registry::getInstance(): static` | The shared instance (created on first access). |
| `Registry::resetInstance(): void` | Drop the singleton so the next access starts fresh. |
| `get(string $key): mixed` | Read a dot path. |
| `set(string $key, mixed $value): void` | Write a dot path. |
| `remove(string $key): mixed` | Remove and return a dot path. |
| `exists(string $key): bool` | Path exists (a `null` value still counts). |
| `ask(string $key): ?Collection` | A nested map as a collection. |
| `keys(?string $key = null): array` | Keys at the root or below a path. |
| `ls(): array` | Alias of `keys()` at the root. |

The registry stores the current `today` (`Y-m-d`) under `today` when it is
first created; refresh it with `Registry::resetInstance()`.

> Note: the `reg()` facade writes only when the value is **not** `null`.
> Use `Registry::getInstance()->set($key, null)` for explicit `null` writes.

## `config()`

`config()` reads and writes dot-paths on the registry, and on **first access**
loads every `*.ini` in the package's `cfg/` directory as a namespace:

```php
echo config('app.name'); // payroll   (from cfg/app.ini: name=payroll)

config('app.name', 'payroll-v2');     // write
echo config('app.name');              // payroll-v2
```

INI files are parsed with `INI_SCANNER_TYPED`, so values become real scalars
(booleans, integers, floats) instead of strings.

## `format()`

`format(string $type, mixed $value)` registers a named formatter with a
closure, or invokes an already-registered one:

```php
format('slack', fn (string $m): string => ':' . strtoupper($m) . ':');
echo format('slack', 'post'); // :POST:
```

Three date formatters are pre-registered:

| Formatter | Output |
| --- | --- |
| `format('date', $value)` | `Y-m-d` |
| `format('datetime', $value)` | `Y-m-d H:i:s` |
| `format('humanize', $value)` | Relative description (`when()`; see [dates.md](dates.md)). |

Invoking an unknown formatter throws `\InvalidArgumentException`.

## Inflection: `plural()` and `singular()`

Convert English words between singular and plural forms. Symfony's
`EnglishInflector` is used when installed; otherwise a built-in fallback
handles the common `-ies`, `-es`, and trailing-`s` rules.

```php
plural('story');     // stories
plural('box');       // boxes
singular('stories'); // story
singular('boxes');   // box
```

## UUIDs: `uuid()`

`uuid(int $version = 4, array $options = []): object` generates UUID strings.
With `ramsey/uuid` installed, every supported version/options combination is
delegated; standalone, a native RFC 4122 version 4 value is generated from
`random_bytes()`. The result is a tiny object exposing `yield()` and string
conversion:

```php
echo uuid();            // e.g. 7f2a… (version 4)
strlen(uuid()->yield()); // 36
```

Requesting anything other than version 4 without `ramsey/uuid` raises
`\RuntimeException`.

## Primitives from `strukt/base`

These low-level helpers are provided by the required
[`strukt/base`](../../base/) package rather than by commons:

| Helper | Description |
| --- | --- |
| `is_map(array $value): bool` | Non-empty associative array. |
| `notnull(mixed $value): bool` | Value is not `null`. |
| `negate(bool $value): bool` | Invert a boolean. |
| `raise(string $message, int $code = 500): never` | Throw `Strukt\Raise`. |
| `helper_add(string $name): bool` | Record a helper name in the base registry. |
| `alias()`, `env()`, `ref()`, `timezone()`, `dd()`, `app()` | See the base package. |

## `Strukt\Raise`

The exception thrown by `raise()` lives in `strukt/base` as a plain
`RuntimeException` with a recorded process-local history:

| Member | Description |
| --- | --- |
| `Raise::errors(): array` | Messages raised during this process. |
| `Raise::reset(): void` | Reset the error history. |

```php
try {
    raise('Payment failed', 422);
} catch (Strukt\Raise $e) {
    $e->getMessage(); // Payment failed
    $e->getCode();    // 422
}
```

---

See also: [README.md](README.md) · [collections.md](collections.md) ·
[dates.md](dates.md) · [architecture.md](architecture.md)