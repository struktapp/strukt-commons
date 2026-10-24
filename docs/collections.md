# Dot-path collections

## The `Collection`

`collect(array $value)` returns a **mutable** `Strukt\Collection` whose
values are read and written through dot-separated paths.

```php
$contact = collect([]);
$contact->set('mobile', '+2540770123456');
$contact->set('work-phone', '+2540202345678');

$user = collect([]);
$user->set('contacts', $contact->yield());

echo $user->get('contacts.mobile'); // +2540770123456
$user->exists('contacts.mobile');   // true

$user->remove('contacts.work-phone');
$user->keys('contacts');            // ['mobile']
```

| Method | Description |
| --- | --- |
| `get(string $key): mixed` | Read a dot path; `null` when absent. |
| `set(string $key, mixed $value): void` | Write a dot path, creating parents. |
| `remove(string $key): mixed` | Remove and return a dot path. |
| `exists(string $key): bool` | Every segment exists (a stored `null` still counts). |
| `keys(?string $key = null): array` | Keys at the root or below a nested path. |
| `ask(string $key): ?self` | A child collection when the path holds a non-empty map. |
| `all(): array` / `yield(): array` | The whole backing array. |

## The standalone path helpers

The same primitives are available as static methods on `Collection` and as
global helpers:

```php
$data = [
    'user' => ['db' => ['config' => ['username' => 'root', 'password' => '_root!']]],
];

echo dot('user.db.config.username', $data);      // root
attach('user.db.config.host', $data, 'localhost');
detach('user.db.config.password', $data);
```

| Function | Behaviour |
| --- | --- |
| `dot(string $path, array $map): mixed` | Read a dot path; `null` when absent. |
| `attach(string $path, array &$map, mixed $value): void` | Write a dot path by reference, creating intermediate arrays. |
| `detach(string $path, array &$map): mixed` | Remove and return a dot path; `null` when absent. |
| `level(array $array, int $target = PHP_INT_MAX, int $current = 1, string $prefix = ''): array` | Flatten nested arrays into dot paths (see [arrays.md](arrays.md)). |

## The interface

`CollectionInterface` declares exactly the collection surface
(`keys`, `set`, `get`, `remove`, `exists`) and is implemented by both
`Collection` and `Registry` ([helpers.md](helpers.md)), so any dot-path store
is interchangeable behind one shape.

---

See also: [README.md](README.md) · [arrays.md](arrays.md) ·
[helpers.md](helpers.md) · [architecture.md](architecture.md)