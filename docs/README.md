# Strukt Commons — Documentation

Strukt Commons (`strukt/commons`) is a small, self-contained PHP utility
library providing chainable array and string values, dot-separated
collections, date values with ranges and a simulated calendar, token queries,
a bounded message stack, JSON helpers, a process-local registry, and a set of
global compatibility functions.

## Features at a glance

| Area | Entry points | Guide |
| --- | --- | --- |
| Chainable strings | `str()` → `Strukt\Str` | [strings.md](strings.md) |
| Chainable arrays | `arr()` → `Strukt\Arr` | [arrays.md](arrays.md) |
| Dot-path collections | `collect()`, `dot()`, `attach()`, `detach()` | [collections.md](collections.md) |
| Dates & human-described dates | `when()`, `today()`, `period()` | [dates.md](dates.md) |
| Token queries | `token()`, `tokenize()`, `Strukt\TokenQuery` | [tokens.md](tokens.md) |
| Message stacks | `stack()`, `Strukt\Stack` | [stack.md](stack.md) |
| JSON helpers | `json()` | [json.md](json.md) |
| Registry, config & misc helpers | `reg()`, `config()`, `uuid()` … | [helpers.md](helpers.md) |
| Internals and design rationale | classes, layers, state model | [architecture.md](architecture.md) |

Requirements:

- PHP **8.2 or newer**.
- **No runtime dependencies.** Optional integrations with `ramsey/uuid` and
  Symfony's English inflector are used automatically when those packages are
  already installed; built-in fallbacks keep every helper working standalone.

## Installation

```sh
composer require strukt/commons
```

From a checkout of this repository:

```sh
composer install
composer test
```

Composer maps the `Strukt\` namespace to `src/Strukt` and registers the global
helper functions from `src/helpers.php`. Load Composer's autoloader:

```php
require __DIR__ . '/vendor/autoload.php';
```

## Quick tour

```php
use Strukt\Arr;

// Chainable strings
echo str('user name')->toSnake();        // user_name
str('admin@example.com')->is()->email(); // true

// Chainable arrays
print_r(arr([1, 2, 2, 3])->uniq()->yield()); // [1, 2, 3]
Arr::from(['a' => 1, 'b' => 2])->sum();      // 3

// Dot-path collections
$user = collect([]);
$user->set('contacts.mobile', '+2540770123456');
echo $user->get('contacts.mobile');          // +2540770123456

// Dates
$end = when('+30 days');
$end->gt(when());                            // true
when()->clone('-1 day')->when();             // "1 day ago" (approx)

// Tokens
$query = token('page:2|tag:php,composer');
$query->get('tag');                          // ['php', 'composer']

// Stack, JSON, registry
stack('User was not found');
json(['ok' => true])->valid();               // true
reg('user.name', 'Ada');                     // write, returns the value
echo reg('user.name');                       // Ada
```

## The wrapper pattern

`Str` and `Arr` are **value objects**: they wrap a raw PHP value and expose a
fluent API. The shared base class `Strukt\ValueObject` provides four members:

| Method | Behaviour |
| --- | --- |
| `yield(): mixed` | Returns the raw wrapped value. |
| `equals(mixed $value): bool` | Loose equality with another value or wrapper. |
| `clone(): static` | Shallow copy of the wrapper. |
| `create(mixed $value): static` | Late-statically bound constructor. |

Transformations generally **return a new wrapper and leave the original
untouched**, so calls chain safely. Two exceptions:

- `Strukt\Arr` mirrors PHP's internal array pointer, so `first()`, `current()`,
  `next()`, `sibling()`, `last()`, `key()`, `reset()`, `pop()`, `dequeue()`,
  and `unset()` operate **in place**.
- `Strukt\DateTime` extends PHP's mutable `DateTime`, so calls such as
  `reset()` and `last()` and the underlying `modify()`/`setTime()`
  **mutate the instance**; use `clone()` when you need an independent copy.

Each guide below documents its own section of this surface in full.

## Development

Run the Pest test suite:

```sh
composer test
```

Coverage (requires an installed Xdebug/PCOV driver):

```sh
composer test:coverage
```

Contributors should keep changes dependency-light, preserve the conditional
`function_exists()` helper registration, and add Pest coverage alongside new
behaviour. The [architecture.md](architecture.md) document explains the
design so changes fit the existing shape.