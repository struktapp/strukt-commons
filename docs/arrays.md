# Arrays — `arr()`

`arr(array $value)` returns a `Strukt\Arr` wrapper. `Arr` implements
`Countable` and `IteratorAggregate`, so `count($wrapper)` and `foreach`
work, and the internal pointer lets you walk the array like PHP's native
arrays.

```php
$numbers = arr([1, 2, 2, 3]);

echo $numbers->sum();                      // 8
echo $numbers->product();                  // 12
print_r($numbers->uniq()->values()->yield()); // [1, 2, 3]
print_r($numbers->distinct()->yield());    // [1 => 1, 2 => 2, 3 => 1]
```

## Size and shape

| Method | Description |
| --- | --- |
| `empty(): bool` | Whether the wrapped array has no entries. |
| `length(): int` | Number of entries. |
| `count(): int` | Alias of `length()` (for `Countable`). |
| `keys(): array` | All keys in current order. |
| `values(): static` | Copy with numeric keys reindexed. |
| `rehash(): static` | Copy with sequential numeric keys. |

## Pointer traversal (in place)

| Method | Description |
| --- | --- |
| `reset(): void` | Move to the first entry. |
| `first(): mixed` | Reset then return the first value. |
| `current(): mixed` | Value at the pointer (also `valid(): mixed`). |
| `next(): bool` | Advance; true while a key exists. |
| `sibling(): object` | One-shot proxy whose `next()` advances the wrapper and returns the current value. |
| `last(): mixed` | `end()` and return the final value. |
| `key(): int|string|null` | Key at the pointer; `null` when exhausted. |

## Adding and removing

| Method | In place? | Description |
| --- | --- | --- |
| `push(mixed $item, int|string|null $key = null): static` | copy | Append, optionally by key. |
| `enqueue(mixed $element, int|string|null $key = null): static` | copy | Alias of `push()`. |
| `prequeue(mixed $element, int|string|null $key = null): static` | copy | Prepend, optionally by key. |
| `merge(array $array): static` | copy | PHP `array_merge`. |
| `enjoin(array $element): static` | copy | Lists appended element-by-element; maps merged. |
| `add(int|string $key, mixed $item): static` | **yes** | Assign a value by key (fluent). |
| `remove(int|string $key): static` | copy | Copy without the key. |
| `unset(int|string|null $key = null): void` | **yes** | Delete the key; defaults to the pointer key. |
| `pop(): mixed` | **yes** | Remove and return the last entry. |
| `dequeue(): mixed` | **yes** | Remove and return the first entry (`array_shift`). |

## Transforming

| Method | Description |
| --- | --- |
| `map(callable $callback): static` | `$callback($key, $value)` per entry, keys retained. |
| `filter(?callable $callback = null): static` | Remove entries for which the predicate is true; with no callback, remove empty values (`null`, `false`, `0`, `''`, whitespace-only strings). List arrays are reindexed, maps keep keys. |
| `each(callable $callback): static` | Transform entries while honoring `skip()`, `jump()`, and `stopAt()` rules. |
| `reverse(): static` | `array_reverse`. |
| `flip(): static` | `array_flip`. |
| `uniq(): static` | Copy with duplicate *serialized* values removed. |
| `slice(int $offset, ?int $length = null): static` | `array_slice`. |
| `column(int|string $key): static` | Extract one column from rows; requires nested values. |
| `distinct(): static` | Counts per integer/string value (`array_count_values`). |
| `join(string $delimiter): string` | `implode`; requires flat strings. |
| `sum(): int|float` | Sum of flat numeric/boolean values. |
| `product(): int|float` | Product of flat numeric/boolean values. |

Aggregate and selection methods throw `\InvalidArgumentException` when the
array is nested or holds incompatible types, so you get an early, loud
failure instead of a silent PHP warning.

## Traversal controls

`each()` can be configured with one-use rules:

| Method | Description |
| --- | --- |
| `skip(int|string|array $key): static` | Keys the next `each()` leaves unchanged. |
| `jump(mixed $value): static` | Values the next `each()` leaves unchanged. |
| `stopAt(mixed $key = null): static` | Key at which the next traversal stops. |
| `will(): object` | Captures and clears pending rules; exposes `jump($v)`, `skip($k)`, `stopAt($k)`, `get($name)`. |

```php
use Strukt\Arr;

Arr::from(['a' => 1, 'b' => 2, 'c' => 3])
    ->skip('b')
    ->stopAt('c')
    ->each(fn (int|string $key, mixed $value): mixed => $value * 10)
    ->yield();
// ['a' => 10, 'b' => 2, 'c' => 3]
```

## Flattening: `level()`

`level(int $target = PHP_INT_MAX)` turns nested arrays into dot-separated
paths:

```php
arr(['user' => ['name' => 'Ada', 'roles' => ['admin', 'author']]])
    ->level()
    ->prefix('payload')
    ->yield();

// [
//   'payload.user.name'       => 'Ada',
//   'payload.user.roles.0'    => 'admin',
//   'payload.user.roles.1'    => 'author',
// ]
```

The helper exposes `prefix(string)`, `noPrefix()` (strips leading numeric
segments such as `0.`), and `yield(): array`. The standalone `level()`
helper offers the same flattening without a wrapper:

```php
level(['user' => ['name' => 'Ada']]); // ['user.name' => 'Ada']
```

## Predicates

| Helper | Method | Description |
| --- | --- | --- |
| `isof()` | `strings(): bool` | Every value is a string. |
| `isof()` | `numbers(): bool` | Every value is numeric. |
| `isof()` | `booleans(): bool` | Every value is boolean. |
| `is()` | `map(): bool` | Non-empty associative array. |
| `is()` | `nested(): bool` | At least one value is an array. |
| `is()` | `any()->empty(): bool` | At least one value is empty. |
| `is()` | `any()->null(): bool` | At least one value is `null`. |
| `are()` | `all($what): object|bool` | With a target, loose equality across all values; without, a predicate object with `assert()`, `empty()`, `null()`. |
| `has(int|string $key): bool` | — | Key exists (via `array_key_exists`, so `null` values count). |
| `contains(mixed $value): bool` | — | A serialized-equal value occurs. |

```php
arr(['ada', 'grace'])->isof()->strings(); // true
arr([1, 1])->are()->all(1);               // true
arr(['name' => 'Ada'])->is()->map();      // true
arr([1, 2, 3])->only([1, 3])->yield();    // [1, 3]
```

## Selecting

| Method | Description |
| --- | --- |
| `only(array $haystack): static` | Copy keeping only values found in the candidate set. |
| `diff(array $toDiff): object` | View of entries not in the comparison array. |
| `cross(array $subject): object` | View of entries shared with the comparison array. |

`diff()` and `cross()` return a helper with three comparison modes:

| Mode | Returns |
| --- | --- |
| `keys(): Arr` | Compare by key. |
| `values(): Arr` | Compare by value (loose `in_array`). |
| `map(): Arr` | Compare by matching key and loosely equal value. |

## Sorting

`sort()` sorts a flat array in place within the helper and returns a PHP
array:

| Method | Description |
| --- | --- |
| `sort()->asc(bool $byKey = false): array` | `asort` or `ksort`. |
| `sort()->desc(bool $byKey = false): array` | `arsort` or `krsort`. |

```php
array_values(arr([3, 1, 2])->sort()->asc()); // [1, 2, 3]
```

`order()` sorts an array of *rows* by one or more columns:

```php
$rows = arr([
    ['name' => 'Ada',     'score' => 92],
    ['name' => 'Grace',   'score' => 88],
    ['name' => 'Barbara', 'score' => 88],
]);

$ordered = $rows->order()->asc('score')->desc('name')->yield();
```

Columns beginning with `asc($column)` / `desc($column)` are applied in
sequence; `null` cells sort first, numeric cells compare numerically, and
everything else compares as strings.

## Serializing to tokens

`tokenize(?array $keys = null): string` renders a map (or a selection of its
keys) into `key:value|key:value` syntax. Lists become comma-separated values.
Non-map input or object values throw.

```php
arr(['page' => 2, 'tag' => ['php', 'composer']])->tokenize();
// page:2|tag:php,composer
```

---

See also: [README.md](README.md) · [strings.md](strings.md) ·
[tokens.md](tokens.md) · [architecture.md](architecture.md)