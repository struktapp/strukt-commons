<?php

namespace Strukt;

/** Parses and updates `key:value|key:value1,value2` strings. */
class TokenQuery
{
    /** Original token string supplied to the constructor. */
    private string $original;
    /** @var array<string, string|int|float|bool|list<string>|null> Parsed token parts. */
    private array $parts = [];

    /**
     * Parses pipe-separated key/value segments.
     *
     * @param string $token Token text in `key:value|key:value` format.
     */
    public function __construct(string $token)
    {
        $this->original = $token;

        foreach (explode('|', $token) as $item) {
            $separator = strpos($item, ':');
            if ($separator === false) {
                continue;
            }

            $key = substr($item, 0, $separator);
            $value = substr($item, $separator + 1);
            $this->parts[$key] = str_contains($value, ',') ? explode(',', $value) : $value;
        }
    }

    /**
     * Checks whether a token key exists.
     *
     * @param string $key Token key to look up.
     * @return bool True when the key was parsed or assigned.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parts);
    }

    /**
     * Returns a parsed token value.
     *
     * @param string $key Token key to read.
     * @return mixed Scalar, list, null, or `null` when the key is absent.
     */
    public function get(string $key): mixed
    {
        return $this->parts[$key] ?? null;
    }

    /**
     * Removes a token key.
     *
     * @param string $key Token key to remove.
     * @return static This query for fluent chaining.
     */
    public function remove(string $key): static
    {
        unset($this->parts[$key]);

        return $this;
    }

    /**
     * Assigns a scalar or string-list token value.
     *
     * @param string $key Token key to assign.
     * @param string|array<int, string>|int|float|bool|null $value Value to assign.
     * @return static This query for fluent chaining.
     * @throws \InvalidArgumentException When an array contains a non-string item.
     */
    public function set(string $key, string|array|int|float|bool|null $value): static
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_string($item)) {
                    throw new \InvalidArgumentException('Token arrays must contain strings.');
                }
            }
        }

        $this->parts[$key] = $value;

        return $this;
    }

    /** @return list<string> */
    /**
     * Returns parsed keys in insertion order.
     *
     * @return list<string> Token keys.
     */
    public function keys(): array
    {
        return array_keys($this->parts);
    }

    /**
     * Returns the original constructor token.
     *
     * @return string Unmodified input token.
     */
    public function token(): string
    {
        return $this->original;
    }

    /**
     * Serializes the current token parts.
     *
     * @return string Current `key:value|key:value` representation.
     */
    public function yield(): string
    {
        $parts = [];
        foreach ($this->parts as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif ($value === null) {
                $value = '';
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $parts[] = $key . ':' . $value;
        }

        return implode('|', $parts);
    }

    /**
     * Converts the current query to its serialized token form.
     *
     * @return string Current token representation.
     */
    public function __toString(): string
    {
        return $this->yield();
    }
}
