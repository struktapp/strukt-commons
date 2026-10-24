# Message stacks — `stack()`

`stack(string|int|null $message = null)` records messages in a **bounded,
process-local** stack (default capacity 10). The returned handle stores its
initial message and can later be read as a string.

```php
\Strukt\Stack::clear();

$message = stack('User was not found');
$message->add('A retry was scheduled');

\Strukt\Stack::get()->yield();       // all messages (as an Arr)
\Strukt\Stack::get('/retry/')->yield(); // messages matching a regex

\Strukt\Stack::limit(20);
```

## Reference

| Member | Description |
| --- | --- |
| `Stack::add(mixed $message): void` | Append; oldest entries beyond the limit are dropped. |
| `Stack::limit(int $limit): void` | Change capacity (minimum 1), trimming oldest entries. |
| `Stack::get(?string $pattern = null): Arr` | All messages, or those matching a regex, as an `Arr` wrapper. |
| `Stack::clear(): void` | Remove every message. |
| `Stack::__toString(): string` | The handle's initial message. |

Instantiating `stack('…')` immediately records the message process-wide;
instantiating `new Strukt\Stack()` with no argument records nothing.

The `get()` filter takes a regular expression and validates it: an invalid
pattern throws `\InvalidArgumentException`. Because results come back as an
`Arr` wrapper, the full [arrays.md](arrays.md) API applies:

```php
$retries = \Strukt\Stack::get('/retry/')->count();
```

---

See also: [README.md](README.md) · [arrays.md](arrays.md) ·
[architecture.md](architecture.md)