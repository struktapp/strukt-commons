# Token queries — `token()`, `tokenize()`

`token(string $value)` parses compact `key:value|key:value` strings; commas
inside a value produce a list.

```php
$query = token('page:2|tag:php,composer');

$query->has('page');       // true
$query->get('tag');        // ['php', 'composer']

$query->set('sort', 'name')->remove('page');
echo $query->yield();      // tag:php,composer|sort:name
```

## Reference

| Method | Description |
| --- | --- |
| `get(string $key): mixed` | Scalar, list, or `null` when absent. |
| `has(string $key): bool` | Key was parsed or assigned. |
| `set(string $key, string|array|int|float|bool|null $value): static` | Assign a scalar or string list (fluent). |
| `remove(string $key): static` | Delete a key (fluent). |
| `keys(): array` | Keys in insertion order. |
| `token(): string` | The *original* constructor string. |
| `yield(): string` | Serialize current parts back to token syntax. |
| `__toString(): string` | Same as `yield()`. |

`set()` with an array throws `\InvalidArgumentException` unless every item is
a string. Serialization renders lists comma-joined, booleans as
`true`/`false`, and `null` as an empty value.

## Serializing maps

`tokenize()` serializes an associative array directly:

```php
echo tokenize(['page' => 2, 'tag' => ['php', 'composer']]);
// page:2|tag:php,composer
```

`tokenize()` is a pure serialization — it never parses, so round-tripping a
map through `token()` again is up to the caller.

> Token syntax is also produced by `arr(...)->tokenize()` and
> `Strukt\Arr::tokenize()`; see [arrays.md](arrays.md).

---

See also: [README.md](README.md) · [arrays.md](arrays.md)