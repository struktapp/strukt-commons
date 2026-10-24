# Strings — `str()`

`str(string $value)` returns a `Strukt\Str` wrapper. All operations are
**byte-oriented** (they pass straight through to `strlen`, `substr`,
`strpos`, and friends); for multibyte text use dedicated tooling.

## Transformations

```php
str('commons')->prepend('Strukt ')->yield();      // Strukt commons
str('Strukt')->concat(' Commons')->yield();        // Strukt Commons
str('hello')->toUpper()->yield();                  // HELLO
str('HELLO')->toLower()->yield();                  // hello
str('Strukt Commons')->toSnake()->yield();         // strukt_commons
str('strukt_commons')->toCamel()->yield();         // StruktCommons
str('admin@example.com')->is()->email();           // true
```

## Case conversion

`toSnake()` normalizes camelCase, PascalCase, spaces, and hyphens:

```php
echo str('userId')->toSnake();        // user_id
echo str('PascalCase')->toSnake();    // pascal_case
echo str('my_table name')->toCamel(); // MyTableName
```

Any transition between letters and digits also earns a separator, so
`str('apiV2')->toSnake()` is `api_v_2` and `str('2fa')->toSnake()` is `2_fa`.

`toCamel()` splits on underscores, spaces, and hyphens and returns
PascalCase; it does not lowercase input, so the source casing is preserved in
each word.

## Inspecting

| Method | Description |
| --- | --- |
| `len(): int` | Byte length (`strlen`). |
| `count(string $needle): int` | Occurrences of a substring (`substr_count`). |
| `startsWith(string $needle): bool` | Prefix match at position zero. |
| `endsWith(string $needle): bool` | Suffix match at the end. |
| `contains(string $needle): bool` | Substring occurs anywhere. |
| `equals(mixed $value): bool` | Identical to another value's string form. |
| `notEquals(mixed $value): bool` | Negation of `equals()`. |
| `at(string $needle, ?int $offset = null): int|false` | First match offset via `strpos`. |
| `startBackwardFindAt(string $needle, ?int $offset = null): int|false` | Last match offset via `strrpos`. |
| `empty(): bool` | True when trimming leaves nothing. |
| `isRegEx(string $pattern): bool` | Whether a pattern is valid for `preg_match`. |

## Slicing

| Method | Description |
| --- | --- |
| `slice(int $start, ?int $length = null): static` | `substr` view, negative offsets count from the end. |
| `first(int $length): static` | Leading bytes. |
| `last(int $length): static` | Trailing bytes (empty when `$length <= 0`). |
| `part(int $start, int $length): static` | Fixed-length substring. |
| `split(string $delimiter): array` | `explode` into a list. |
| `btwn(string $from, string $to): static` | Text between the first `$from`/`$to` pair. |

`btwn()` behaves tolerantly: if the opening delimiter is absent it returns an
empty string; if the closing delimiter is absent it returns everything after
the opening delimiter.

```php
str('{bold}Black Beauty{/bold}')->btwn('{bold}', '{/bold}')->yield(); // Black Beauty
```

## Editing

| Method | Description |
| --- | --- |
| `replace(array|string $search, array|string $replace): static` | Literal `str_replace` (all occurrences). |
| `replaceFirst(string $search, string $replace): static` | First regex match (`~…~` wrapper). Throws `\InvalidArgumentException` on an invalid pattern. |
| `replaceLast(string $search, string $replace): static` | Final literal occurrence. |
| `replaceAt(array|string $replace, array|int $start, ?int $length = null): static` | `substr_replace` at byte offsets. |
| `prepend(string $value): static` | Text before the current value. |
| `concat(string $value): static` | Text after the current value. |
| `repeat(int $times): static` | Repeated copies; negatives produce no text. |
| `newline(int $times = 1): static` | Append `\n` characters. |

```php
str('Blah blah blah!')->replaceFirst('blah', 'yaba daba')->yield(); // Blah yaba daba blah!
```

## Padding

`pad(string $padString = "\t")` returns a helper with four directional
variants. All padding is additive: `$length` is the number of padding units
**added**, not the final total width.

| Method | Description |
| --- | --- |
| `pad()->left(int $length = 1): string` | Prefix (`STR_PAD_LEFT`). |
| `pad()->right(int $length = 1): string` | Suffix (`STR_PAD_RIGHT`). |
| `pad()->both(int $length = 1): string` | Split across sides (`STR_PAD_BOTH`). |
| `pad()->block()->left(int $length = 1): string` | Prefix every line of multiline text. |

```php
str('log')->pad('.')->right(3);      // log...
str('x')->pad('-')->both(2);         // -x-
str("a\nb")->pad(' ')->block()->left(2); // "  a\n  b"
```

## Validation

`is()` currently exposes one validator, `email()`:

```php
str('admin@example.com')->is()->email(); // true
```

---

See also: [README.md](README.md) · [arrays.md](arrays.md) ·
[architecture.md](architecture.md)