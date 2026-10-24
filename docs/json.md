# JSON helpers — `json()`

`json(string|array $value)` wraps native JSON encoding and decoding. Every
meaningful failure raises `\JsonException` (`JSON_THROW_ON_ERROR`), so
malformed input never silently becomes `null`.

## Reference

| Method | Description |
| --- | --- |
| `encode(int $options = 0): string` | Serialize the wrapped value. |
| `decode(): mixed` | Decode wrapped text; arrays pass through unchanged. |
| `pp(): string` | Pretty-printed `encode(JSON_PRETTY_PRINT)`. |
| `valid(): bool` | Whether wrapped text is valid JSON (arrays are always valid). |

## Examples

```php
$payload = json(['name' => 'Peter', 'active' => true]);

echo $payload->pp();
$decoded = json($payload->encode())->decode();

json('{"ok":true}')->valid(); // true
json(['ok' => true])->valid();  // true (already decoded)
```

`decode()` runs with `JSON_THROW_ON_ERROR`, so invalid text raises
`\JsonException` instead of returning `null`. `encode()` explicitly OR's any
extra flags onto `JSON_THROW_ON_ERROR`, keeping the same failure mode for
unencodable values.

---

See also: [README.md](README.md)