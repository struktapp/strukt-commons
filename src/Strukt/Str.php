<?php

namespace Strukt;

/** Chainable operations for byte-oriented PHP strings. */
class Str extends ValueObject
{
    /** String wrapped by this value object. */
    protected mixed $value;

    /**
     * Creates a string wrapper.
     *
     * @param string $value Text to wrap.
     */
    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    /**
     * Creates a string wrapper through late static binding.
     *
     * @param mixed $value Text accepted by the string constructor.
     * @return static New string wrapper.
     */
    public static function create(mixed $value): static
    {
        return new static($value);
    }

    /**
     * Returns a copy with text prepended.
     *
     * @param string $value Text to place before the current value.
     * @return static New string wrapper.
     */
    public function prepend(string $value): static
    {
        return new static($value . $this->value);
    }

    /**
     * Returns a copy with text appended.
     *
     * @param string $value Text to place after the current value.
     * @return static New string wrapper.
     */
    public function concat(string $value): static
    {
        return new static($this->value . $value);
    }

    /**
     * Returns the byte length of the string.
     *
     * @return int String length from `strlen()`.
     */
    public function len(): int
    {
        return strlen($this->value);
    }

    /**
     * Counts non-overlapping occurrences of a substring.
     *
     * @param string $needle Substring to count.
     * @return int Number of occurrences.
     */
    public function count(string $needle): int
    {
        return substr_count($this->value, $needle);
    }

    /** @return list<string> */
    /**
     * Splits the string at a delimiter.
     *
     * @param string $delimiter Delimiter passed to `explode()`.
     * @return list<string> Resulting string segments.
     */
    public function split(string $delimiter): array
    {
        return explode($delimiter, $this->value);
    }

    /**
     * Returns a substring copy using byte offsets.
     *
     * @param int $start Starting offset; negative offsets count from the end.
     * @param int|null $length Optional number of bytes.
     * @return static Substring wrapper.
     */
    public function slice(int $start, ?int $length = null): static
    {
        return new static(substr($this->value, $start, $length));
    }

    /**
     * Returns an uppercase copy.
     *
     * @return static Uppercase string wrapper.
     */
    public function toUpper(): static
    {
        return new static(strtoupper($this->value));
    }

    /**
     * Returns a lowercase copy.
     *
     * @return static Lowercase string wrapper.
     */
    public function toLower(): static
    {
        return new static(strtolower($this->value));
    }

    /**
     * Converts camel, spaced, and hyphenated text to snake_case.
     *
     * @return static Snake-case string wrapper.
     */
    public function toSnake(): static
    {
        $value = preg_replace('/([a-z\d])([A-Z])/', '$1_$2', $this->value) ?? $this->value;
        $value = preg_replace('/([A-Za-z])([\d])/', '$1_$2', $value) ?? $value;
        $value = preg_replace('/([\d])([A-Za-z])/', '$1_$2', $value) ?? $value;
        $value = preg_replace('/[\s\-]+/', '_', $value) ?? $value;

        return new static(strtolower(trim($value, '_')));
    }

    /**
     * Converts separated words to PascalCase text.
     *
     * @return static Pascal-case string wrapper.
     */
    public function toCamel(): static
    {
        $parts = preg_split('/[_\s\-]+/', trim($this->value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $value = implode('', array_map(static fn (string $part): string => ucfirst($part), $parts));

        return new static($value);
    }

    /**
     * Checks whether the string begins with a prefix.
     *
     * @param string $needle Prefix to find.
     * @return bool True when the prefix matches at position zero.
     */
    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->value, $needle);
    }

    /**
     * Checks whether the string ends with a suffix.
     *
     * @param string $needle Suffix to find.
     * @return bool True when the suffix matches the end.
     */
    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->value, $needle);
    }

    /**
     * Checks whether a substring occurs anywhere in the value.
     *
     * @param string $needle Substring to find.
     * @return bool True when the substring occurs.
     */
    public function contains(string $needle): bool
    {
        return str_contains($this->value, $needle);
    }

    /**
     * Compares the string with another value's string representation.
     *
     * @param mixed $value Value or string wrapper to compare.
     * @return bool True when the representations are identical.
     */
    public function equals(mixed $value): bool
    {
        if ($value instanceof self) {
            $value = $value->yield();
        }

        return $this->value === (string) $value;
    }

    /**
     * Checks that the string differs from another value.
     *
     * @param mixed $value Value or string wrapper to compare.
     * @return bool True when the representations differ.
     */
    public function notEquals(mixed $value): bool
    {
        return !$this->equals($value);
    }

    /**
     * Finds the first occurrence of a substring.
     *
     * @param string $needle Substring to find.
     * @param int|null $offset Optional starting offset.
     * @return int|false Match offset, or `false` when absent.
     */
    public function at(string $needle, ?int $offset = null): int|false
    {
        return $offset === null
            ? strpos($this->value, $needle)
            : strpos($this->value, $needle, $offset);
    }

    /**
     * Finds the final occurrence of a substring.
     *
     * @param string $needle Substring to find.
     * @param int|null $offset Optional offset passed to `strrpos()`.
     * @return int|false Match offset, or `false` when absent.
     */
    public function startBackwardFindAt(string $needle, ?int $offset = null): int|false
    {
        return $offset === null
            ? strrpos($this->value, $needle)
            : strrpos($this->value, $needle, $offset);
    }

    /**
     * Extracts text between the first opening and closing delimiters.
     *
     * @param string $from Opening delimiter.
     * @param string $to Closing delimiter.
     * @return static Text between the delimiters, or the remaining text when the
     * closing delimiter is absent.
     */
    public function btwn(string $from, string $to): static
    {
        $start = strpos($this->value, $from);
        if ($start === false) {
            return new static('');
        }

        $start += strlen($from);
        $end = strpos($this->value, $to, $start);

        return new static($end === false
            ? substr($this->value, $start)
            : substr($this->value, $start, $end - $start));
    }

    /**
     * Replaces literal search values throughout the string.
     *
     * @param array|string $search Text or texts to find.
     * @param array|string $replace Replacement text or texts.
     * @return static Updated string wrapper.
     */
    public function replace(array|string $search, array|string $replace): static
    {
        return new static(str_replace($search, $replace, $this->value));
    }

    /**
     * Replaces text at one or more byte offsets.
     *
     * @param array|string $replace Replacement text.
     * @param array|int $start Offset or offsets to replace.
     * @param int|null $length Optional number of bytes to replace.
     * @return static Updated string wrapper.
     */
    public function replaceAt(array|string $replace, array|int $start, ?int $length = null): static
    {
        return new static($length === null
            ? substr_replace($this->value, $replace, $start)
            : substr_replace($this->value, $replace, $start, $length));
    }

    /**
     * Replaces the first regular-expression match.
     *
     * @param string $search Regular-expression body.
     * @param string $replace Replacement text.
     * @return static Updated string wrapper.
     * @throws \InvalidArgumentException When `$search` is invalid.
     */
    public function replaceFirst(string $search, string $replace): static
    {
        $result = preg_replace('~' . $search . '~', $replace, $this->value, 1, $count);
        if ($result === null) {
            throw new \InvalidArgumentException('The search pattern is not a valid regular expression.');
        }

        return new static($result);
    }

    /**
     * Replaces the final literal occurrence of a substring.
     *
     * @param string $search Literal text to find.
     * @param string $replace Replacement text.
     * @return static Updated string wrapper.
     */
    public function replaceLast(string $search, string $replace): static
    {
        $position = $this->startBackwardFindAt($search);
        if ($position === false) {
            return new static($this->value);
        }

        return $this->replaceAt($replace, $position, strlen($search));
    }

    /**
     * Returns the first number of bytes from the string.
     *
     * @param int $length Number of bytes to return.
     * @return static Leading substring wrapper.
     */
    public function first(int $length): static
    {
        return $this->slice(0, $length);
    }

    /**
     * Returns the final number of bytes from the string.
     *
     * @param int $length Number of bytes to return.
     * @return static Trailing substring wrapper.
     */
    public function last(int $length): static
    {
        return $length <= 0 ? new static('') : new static(substr($this->value, -$length));
    }

    /**
     * Returns a substring from an offset for a fixed length.
     *
     * @param int $start Starting byte offset.
     * @param int $length Number of bytes to return.
     * @return static Substring wrapper.
     */
    public function part(int $start, int $length): static
    {
        return new static(substr($this->value, $start, $length));
    }

    /**
     * Checks whether the trimmed string is empty.
     *
     * @return bool True when no non-whitespace characters remain.
     */
    public function empty(): bool
    {
        return trim($this->value) === '';
    }

    /**
     * Checks whether a pattern is accepted by `preg_match()`.
     *
     * @param string $pattern Regular-expression pattern to validate.
     * @return bool True when the pattern is valid and non-empty.
     */
    public function isRegEx(string $pattern): bool
    {
        if ($pattern === '') {
            return false;
        }

        return @preg_match($pattern, '') !== false;
    }

    /**
     * Creates helpers for left, right, both-side, and block padding.
     *
     * @param string $padString Sequence used to pad the value.
     * @return object Padding helper exposing `left()`, `right()`, `both()`, and
     *                `block()->left()`.
     */
    public function pad(string $padString = "\t"): object
    {
        return new class($this->value, $padString) {
            /**
             * Creates a string padding helper.
             *
             * @param string $value String to pad.
             * @param string $padString Padding sequence.
             */
            public function __construct(private string $value, private string $padString)
            {
            }

            /**
             * Pads the left side to the requested total width.
             *
             * @param int $length Number of padding bytes to add.
             * @return string Padded string.
             */
            public function left(int $length = 1): string
            {
                return $this->pad($length, STR_PAD_LEFT);
            }

            /**
             * Pads the right side to the requested total width.
             *
             * @param int $length Number of padding bytes to add.
             * @return string Padded string.
             */
            public function right(int $length = 1): string
            {
                return $this->pad($length, STR_PAD_RIGHT);
            }

            /**
             * Pads both sides using PHP's `STR_PAD_BOTH` behavior.
             *
             * @param int $length Total padding bytes to add.
             * @return string Padded string.
             */
            public function both(int $length = 1): string
            {
                return $this->pad($length, STR_PAD_BOTH);
            }

            /**
             * Creates a helper for padding every line in a text block.
             *
             * @return object Block padding helper.
             */
            public function block(): object
            {
                return new class($this->value, $this->padString) {
                    /**
                     * Creates a block padding helper.
                     *
                     * @param string $value Multiline text.
                     * @param string $padString Padding sequence.
                     */
                    public function __construct(private string $value, private string $padString)
                    {
                    }

                    /**
                     * Pads each line on the left.
                     *
                     * @param int $length Number of padding units per line.
                     * @return string Padded multiline text.
                     */
                    public function left(int $length = 1): string
                    {
                        $lines = explode("\n", $this->value);
                        $prefix = str_repeat($this->padString, max(0, $length));

                        return implode("\n", array_map(static fn (string $line): string => $prefix . $line, $lines));
                    }
                };
            }

            /**
             * Applies a PHP string padding mode.
             *
             * @param int $length Number of padding bytes to add.
             * @param int $type One of PHP's `STR_PAD_*` constants.
             * @return string Padded string.
             */
            private function pad(int $length, int $type): string
            {
                $length = max(0, $length);

                return str_pad($this->value, strlen($this->value) + $length, $this->padString, $type);
            }
        };
    }

    /**
     * Creates validators for the wrapped string.
     *
     * @return object Validator exposing `email()`.
     */
    public function is(): object
    {
        return new class($this->value) {
            /**
             * Creates a string validator.
             *
             * @param string $value String to validate.
             */
            public function __construct(private string $value)
            {
            }

            /**
             * Checks whether the value is a valid email address.
             *
             * @return bool True when PHP's email validator accepts the value.
             */
            public function email(): bool
            {
                return filter_var(trim($this->value), FILTER_VALIDATE_EMAIL) !== false;
            }
        };
    }

    /**
     * Returns a copy repeated a number of times.
     *
     * @param int $times Number of repetitions; negative values produce no text.
     * @return static Repeated string wrapper.
     */
    public function repeat(int $times): static
    {
        return new static(str_repeat($this->value, max(0, $times)));
    }

    /**
     * Returns a copy with newline characters appended.
     *
     * @param int $times Number of newline characters.
     * @return static Updated string wrapper.
     */
    public function newline(int $times = 1): static
    {
        return $this->concat(str_repeat("\n", max(0, $times)));
    }

    /**
     * Converts the wrapper to its raw string value.
     *
     * @return string Wrapped text.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
