<?php

namespace Strukt;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/** Chainable operations for PHP arrays. */
abstract class Arr extends ValueObject implements Countable, IteratorAggregate
{
    /** Key at which a traversal should stop. */
    private mixed $stopAt = null;
    /** @var list<int|string> */
    private array $skip = [];
    /** @var list<mixed> */
    private array $jump = [];

    /**
     * Creates an array wrapper and resets its internal pointer.
     *
     * @param array<int|string, mixed> $value Values to wrap.
     */
    public function __construct(array $value)
    {
        parent::__construct($value);
        $this->reset();
    }

    /** Create a concrete wrapper even when called as `Arr::from(...)`. */
    /**
     * Creates a concrete array wrapper from a PHP array.
     *
     * @param array<int|string, mixed> $value Values to wrap.
     * @return static Concrete wrapper instance.
     */
    public static function from(array $value): static
    {
        if (static::class === self::class) {
            return new class($value) extends Arr {
            };
        }

        return new static($value);
    }

    /**
     * Returns an iterator over the wrapped values.
     *
     * @return Traversable Array iterator preserving keys.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->value);
    }

    /**
     * Checks whether the wrapped array has no entries.
     *
     * @return bool True when the array is empty.
     */
    public function empty(): bool
    {
        return $this->value === [];
    }

    /**
     * Counts entries in the wrapped array.
     *
     * @return int Number of entries.
     */
    public function length(): int
    {
        return count($this->value);
    }

    /**
     * Returns the entry count for `Countable` and API compatibility.
     *
     * @return int Number of entries.
     */
    public function count(): int
    {
        return $this->length();
    }

    /**
     * Removes and returns the final entry in place.
     *
     * @return mixed Removed value, or `null` when empty.
     */
    public function pop(): mixed
    {
        return array_pop($this->value);
    }

    /**
     * Returns a copy with a value appended or assigned by key.
     *
     * @param mixed $item Value to add.
     * @param int|string|null $key Optional key; omitted values use the next list key.
     * @return static New wrapper containing the value.
     */
    public function push(mixed $item, int|string|null $key = null): static
    {
        $values = $this->value;

        if ($key === null) {
            $values[] = $item;
        } else {
            $values[$key] = $item;
        }

        return new static($values);
    }

    /**
     * Removes a key from this wrapper in place.
     *
     * @param int|string|null $key Key to remove, or `null` for the current key.
     * @return void
     */
    public function unset(int|string|null $key = null): void
    {
        unset($this->value[$key ?? $this->key()]);
    }

    /**
     * Returns a copy without a selected key.
     *
     * @param int|string $key Key to remove from the copy.
     * @return static New wrapper without the key.
     */
    public function remove(int|string $key): static
    {
        $values = $this->value;
        unset($values[$key]);

        return new static($values);
    }

    /**
     * Removes and returns the first entry in place.
     *
     * @return mixed Removed value, or `null` when empty.
     */
    public function dequeue(): mixed
    {
        return array_shift($this->value);
    }

    /**
     * Alias for `push()` that returns a new wrapper.
     *
     * @param mixed $element Value to append.
     * @param int|string|null $key Optional key to assign.
     * @return static New wrapper containing the value.
     */
    public function enqueue(mixed $element, int|string|null $key = null): static
    {
        return $this->push($element, $key);
    }

    /**
     * Returns a copy with a value inserted at the beginning.
     *
     * @param mixed $element Value to prepend.
     * @param int|string|null $key Optional key to assign.
     * @return static New wrapper with the value at the front.
     */
    public function prequeue(mixed $element, int|string|null $key = null): static
    {
        if ($key === null) {
            $values = $this->value;
            array_unshift($values, $element);
        } else {
            $values = array_merge([$key => $element], $this->value);
        }

        return new static($values);
    }

    /**
     * Moves the internal pointer to the first entry.
     *
     * @return void
     */
    public function reset(): void
    {
        reset($this->value);
    }

    /**
     * Moves to and returns the first entry.
     *
     * @return mixed First value, or `false` when empty.
     */
    public function first(): mixed
    {
        $this->reset();

        return $this->current();
    }

    /**
     * Returns the entry at the internal pointer.
     *
     * @return mixed Current value, or `false` when the pointer is invalid.
     */
    public function current(): mixed
    {
        return current($this->value);
    }

    /**
     * Returns the current value for legacy iterator-style access.
     *
     * @return mixed Current value, or `false` when invalid.
     */
    public function valid(): mixed
    {
        return $this->current();
    }

    /**
     * Advances the internal pointer.
     *
     * @return bool True when the pointer now references an entry.
     */
    public function next(): bool
    {
        next($this->value);

        return key($this->value) !== null;
    }

    /**
     * Creates a proxy that advances this wrapper and returns each next value.
     *
     * @return object Proxy exposing a `next()` method.
     */
    public function sibling(): object
    {
        return new class($this) {
            /**
             * Stores the wrapper advanced by this proxy.
             *
             * @param Arr $parent Parent array wrapper.
             */
            public function __construct(private Arr $parent)
            {
            }

            /**
             * Advances the parent wrapper and returns its current value.
             *
             * @return mixed Next current value.
             */
            public function next(): mixed
            {
                $this->parent->next();

                return $this->parent->current();
            }
        };
    }

    /**
     * Moves to and returns the final entry.
     *
     * @return mixed Last value, or `false` when empty.
     */
    public function last(): mixed
    {
        return end($this->value);
    }

    /**
     * Returns the key at the internal pointer.
     *
     * @return int|string|null Current key, or `null` when invalid.
     */
    public function key(): int|string|null
    {
        return key($this->value);
    }

    /**
     * Returns all keys in their current order.
     *
     * @return array<int, int|string> Array keys.
     */
    public function keys(): array
    {
        return array_keys($this->value);
    }

    /**
     * Returns a copy containing values with numeric keys reindexed.
     *
     * @return static Reindexed wrapper.
     */
    public function values(): static
    {
        return new static(array_values($this->value));
    }

    /**
     * Returns a copy with duplicate serialized values removed.
     *
     * @return static Wrapper containing unique values.
     */
    public function uniq(): static
    {
        $seen = [];
        $values = [];

        foreach ($this->value as $key => $value) {
            $hash = serialize($value);
            if (array_key_exists($hash, $seen)) {
                continue;
            }

            $seen[$hash] = true;
            $values[$key] = $value;
        }

        return new static($values);
    }

    /**
     * Returns a copy with entries in reverse order.
     *
     * @return static Reversed wrapper.
     */
    public function reverse(): static
    {
        return new static(array_reverse($this->value));
    }

    /**
     * Returns a copy with keys and values exchanged.
     *
     * @return static Flipped wrapper.
     */
    public function flip(): static
    {
        return new static(array_flip($this->value));
    }

    /**
     * Returns a copy with sequential numeric keys.
     *
     * @return static Reindexed wrapper.
     */
    public function rehash(): static
    {
        return new static(array_values($this->value));
    }

    /**
     * Checks whether a key exists, including a key containing `null`.
     *
     * @param int|string $key Key to find.
     * @return bool True when the key exists.
     */
    public function has(int|string $key): bool
    {
        return array_key_exists($key, $this->value);
    }

    /**
     * Checks whether a serialized value occurs in the array.
     *
     * @param mixed $value Value to find.
     * @return bool True when an equivalent serialized value exists.
     */
    public function contains(mixed $value): bool
    {
        $needle = serialize($value);

        foreach ($this->value as $candidate) {
            if (serialize($candidate) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Configures keys that the next `each()` call should leave unchanged.
     *
     * @param int|string|array<int, int|string> $key Key or keys to skip.
     * @return static This wrapper for fluent configuration.
     */
    public function skip(int|string|array $key): static
    {
        $this->skip = array_merge($this->skip, is_array($key) ? $key : [$key]);

        return $this;
    }

    /**
     * Configures values that the next `each()` call should leave unchanged.
     *
     * @param mixed|array<int, mixed> $value Value or values to skip.
     * @return static This wrapper for fluent configuration.
     */
    public function jump(mixed $value): static
    {
        $this->jump = array_merge($this->jump, is_array($value) ? $value : [$value]);

        return $this;
    }

    /**
     * Configures the key at which the next traversal stops.
     *
     * @param mixed|null $key Stop key, or `null` to clear the rule.
     * @return static This wrapper for fluent configuration.
     */
    public function stopAt(mixed $key = null): static
    {
        $this->stopAt = $key;

        return $this;
    }

    /**
     * Captures and clears pending traversal rules.
     *
     * @return object{jump:callable,skip:callable,stopAt:callable,get:callable}
     *         One-use traversal rule object.
     */
    public function will(): object
    {
        $rules = [
            'jumps' => $this->jump,
            'skips' => $this->skip,
            'stop_at' => $this->stopAt,
        ];

        $this->jump = [];
        $this->skip = [];
        $this->stopAt = null;

        return new class($rules) {
            /**
             * Stores a traversal rule snapshot.
             *
             * @param array{jumps: list<mixed>, skips: list<int|string>, stop_at: mixed} $rules Rules.
             */
            public function __construct(private array $rules)
            {
            }

            /**
             * Checks whether a value is configured to be skipped.
             *
             * @param mixed $value Value to compare.
             * @return bool True when the value should be skipped.
             */
            public function jump(mixed $value): bool
            {
                return in_array($value, $this->rules['jumps'], true);
            }

            /**
             * Checks whether a key is configured to be skipped.
             *
             * @param mixed $key Key to compare.
             * @return bool True when the key should be skipped.
             */
            public function skip(mixed $key): bool
            {
                return in_array($key, $this->rules['skips'], true);
            }

            /**
             * Checks whether traversal has reached its stop key.
             *
             * @param mixed $key Current key.
             * @return bool True when traversal should stop.
             */
            public function stopAt(mixed $key): bool
            {
                return $this->rules['stop_at'] !== null && $this->rules['stop_at'] == $key;
            }

            /**
             * Returns one captured rule by name.
             *
             * @param string $name Rule name.
             * @return mixed Captured rule value, or `null` when unknown.
             */
            public function get(string $name): mixed
            {
                return $this->rules[$name] ?? null;
            }
        };
    }

    /**
     * Moves the internal pointer to a key when it exists.
     *
     * @param int|string $key Key at which to resume.
     * @return static This wrapper.
     */
    protected function seek(int|string $key): static
    {
        $this->reset();
        while ($this->key() !== null && $this->key() != $key) {
            $this->next();
        }

        return $this;
    }

    /**
     * Multiplies numeric and boolean values.
     *
     * @return int|float Product of all values.
     * @throws \InvalidArgumentException When values are nested or non-numeric.
     */
    public function product(): int|float
    {
        $this->assertFlatNumeric();

        return array_product($this->value);
    }

    /**
     * Adds numeric and boolean values.
     *
     * @return int|float Sum of all values.
     * @throws \InvalidArgumentException When values are nested or non-numeric.
     */
    public function sum(): int|float
    {
        $this->assertFlatNumeric();

        return array_sum($this->value);
    }

    /**
     * Assigns a value by key in place.
     *
     * @param int|string $key Key to assign.
     * @param mixed $item Value to store.
     * @return static This wrapper after mutation.
     */
    public function add(int|string $key, mixed $item): static
    {
        $this->value[$key] = $item;

        return $this;
    }

    /**
     * Returns a copy containing this array and another array merged by PHP rules.
     *
     * @param array<int|string, mixed> $array Values to merge.
     * @return static Merged wrapper.
     */
    public function merge(array $array): static
    {
        return new static(array_merge($this->value, $array));
    }

    /**
     * Returns a copy with a list appended or a map merged.
     *
     * @param array<int|string, mixed> $element Values to append or merge.
     * @return static Combined wrapper.
     */
    public function enjoin(array $element): static
    {
        if (array_is_list($element)) {
            $values = $this->value;
            array_push($values, ...$element);

            return new static($values);
        }

        return new static(array_merge($this->value, $element));
    }

    /**
     * Joins a flat string array with a delimiter.
     *
     * @param string $delimiter Separator passed to `implode()`.
     * @return string Joined string.
     * @throws \InvalidArgumentException When values are nested or not strings.
     */
    public function join(string $delimiter): string
    {
        if (!$this->isof()->strings() || $this->is()->nested()) {
            throw new \InvalidArgumentException('Incompatible array.');
        }

        return implode($delimiter, $this->value);
    }

    /**
     * Returns a copy containing a portion of the array.
     *
     * @param int $offset Starting offset.
     * @param int|null $length Optional number of entries.
     * @return static Sliced wrapper.
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return new static(array_slice($this->value, $offset, $length));
    }

    /**
     * Extracts one column from an array of rows.
     *
     * @param int|string $key Row key or object property name.
     * @return static Column values.
     * @throws \InvalidArgumentException When the wrapped value has no nested rows.
     */
    public function column(int|string $key): static
    {
        if (!$this->is()->nested()) {
            throw new \InvalidArgumentException('A column requires an array of rows.');
        }

        return new static(array_column($this->value, $key));
    }

    /**
     * Counts occurrences of each integer or string value.
     *
     * @return static Map of values to occurrence counts.
     * @throws \InvalidArgumentException When values are nested or unsupported.
     */
    public function distinct(): static
    {
        if ($this->is()->nested()) {
            throw new \InvalidArgumentException('Distinct counts require scalar values.');
        }

        foreach ($this->value as $value) {
            if (!is_int($value) && !is_string($value)) {
                throw new \InvalidArgumentException('Distinct counts require integer or string values.');
            }
        }

        return new static(array_count_values($this->value));
    }

    /**
     * Creates views of entries absent from another array.
     *
     * @param array<int|string, mixed> $toDiff Comparison array.
     * @return object Difference helper exposing `keys()`, `values()`, and `map()`.
     */
    public function diff(array $toDiff): object
    {
        return $this->comparison($toDiff, false);
    }

    /**
     * Creates views of entries shared with another array.
     *
     * @param array<int|string, mixed> $subject Comparison array.
     * @return object Intersection helper exposing `keys()`, `values()`, and `map()`.
     */
    public function cross(array $subject): object
    {
        return $this->comparison($subject, true);
    }

    /**
     * Returns a copy containing only values found in a candidate set.
     *
     * @param array<int, mixed> $haystack Allowed values.
     * @return static Filtered wrapper.
     */
    public function only(array $haystack): static
    {
        return $this->filter(
            fn (int|string $key, mixed $value): bool => !in_array($value, $haystack, true)
        );
    }

    /**
     * Maps each entry through a key/value callback.
     *
     * @param callable(int|string, mixed): mixed $callback Mapping callback.
     * @return static Mapped wrapper retaining original keys.
     */
    public function map(callable $callback): static
    {
        $values = [];
        foreach ($this->value as $key => $value) {
            $values[$key] = $callback($key, $value);
        }

        return new static($values);
    }

    /**
     * Returns a copy after removing matching entries.
     *
     * With no callback, empty values are removed. List arrays are reindexed;
     * associative arrays retain their keys.
     *
     * @param callable(int|string, mixed): bool|null $callback Predicate returning
     *        `true` for entries to remove.
     * @return static Filtered wrapper.
     */
    public function filter(?callable $callback = null): static
    {
        $wasList = array_is_list($this->value);

        if ($callback === null) {
            $isEmpty = static fn (mixed $value): bool => $value === null
                || $value === false
                || $value === 0
                || $value === ''
                || (is_string($value) && trim($value) === '');

            $callback = $this->is()->map()
                ? static fn (int|string $key, mixed $value): bool => $isEmpty($key) || $isEmpty($value)
                : static fn (int|string $key, mixed $value): bool => $isEmpty($value);
        }

        $values = $this->value;
        foreach ($this->value as $key => $value) {
            if ($this->stopAt !== null && $key == $this->stopAt) {
                break;
            }

            if ($callback($key, $value)) {
                unset($values[$key]);
            }
        }

        $this->stopAt = null;

        if ($wasList) {
            $values = array_values($values);
        }

        return new static($values);
    }

    /**
     * Applies a key/value callback while honoring traversal rules.
     *
     * @param callable(int|string, mixed): mixed $callback Transformation callback.
     * @return static Transformed wrapper.
     */
    public function each(callable $callback): static
    {
        $rules = $this->will();
        $values = $this->value;

        foreach ($this->value as $key => $value) {
            if ($rules->stopAt($key)) {
                break;
            }

            if (!$rules->jump($value) && !$rules->skip($key)) {
                $values[$key] = $callback($key, $value);
            }
        }

        return new static($values);
    }

    /**
     * Creates a helper for flattening nested arrays into dot paths.
     *
     * @param int $target Maximum nesting depth to expand.
     * @return object Level helper exposing `prefix()`, `noPrefix()`, and `yield()`.
     */
    public function level(int $target = PHP_INT_MAX): object
    {
        return new class($this->value, $target) {
            /** Prefix prepended to flattened paths. */
            private string $prefix = '';
            /** Whether leading numeric path segments should be removed. */
            private bool $stripNumericPrefix = false;

            /**
             * Creates a flattening helper.
             *
             * @param array<int|string, mixed> $value Source array.
             * @param int $target Maximum depth to expand.
             */
            public function __construct(private array $value, private int $target)
            {
            }

            /**
             * Sets a prefix for generated paths.
             *
             * @param string $prefix Path prefix.
             * @return static This level helper.
             */
            public function prefix(string $prefix): static
            {
                $this->prefix = $prefix;

                return $this;
            }

            /**
             * Enables removal of leading numeric path segments.
             *
             * @return static This level helper.
             */
            public function noPrefix(): static
            {
                $this->stripNumericPrefix = true;

                return $this;
            }

            /**
             * Flattens the configured array.
             *
             * @return array<string, mixed> Dot-path keys and leaf values.
             */
            public function yield(): array
            {
                $result = [];
                /**
                 * Recursively copies leaf values into dot-separated paths.
                 *
                 * @param array<int|string, mixed> $items Current nested values.
                 * @param int $depth Current nesting depth.
                 * @param string $prefix Current path prefix.
                 * @return void
                 */
                $flatten = function (array $items, int $depth, string $prefix) use (&$flatten, &$result): void {
                    foreach ($items as $key => $value) {
                        $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;
                        if (is_array($value) && $depth < $this->target) {
                            $flatten($value, $depth + 1, $path);
                        } else {
                            $result[$path] = $value;
                        }
                    }
                };

                $flatten($this->value, 1, $this->prefix);

                if ($this->stripNumericPrefix) {
                    $result = array_combine(
                        array_map(static fn (string $key): string => preg_replace('/^\d+\./', '', $key), array_keys($result)),
                        array_values($result),
                    ) ?: [];
                }

                return $result;
            }
        };
    }

    /**
     * Creates predicates for the types of every entry.
     *
     * @return object Type helper exposing `strings()`, `numbers()`, and
     *                `booleans()`.
     */
    public function isof(): object
    {
        return new class($this->value) {
            /**
             * Creates a type predicate helper.
             *
             * @param array<int|string, mixed> $value Values to inspect.
             */
            public function __construct(private array $value)
            {
            }

            /**
             * Checks whether every value is a string.
             *
             * @return bool True when all entries pass `is_string()`.
             */
            public function strings(): bool
            {
                return $this->all('is_string');
            }

            /**
             * Checks whether every value is numeric.
             *
             * @return bool True when all entries pass `is_numeric()`.
             */
            public function numbers(): bool
            {
                return $this->all('is_numeric');
            }

            /**
             * Checks whether every value is boolean.
             *
             * @return bool True when all entries pass `is_bool()`.
             */
            public function booleans(): bool
            {
                return $this->all('is_bool');
            }

            /**
             * Applies a scalar type predicate to every value.
             *
             * @param string $function Name of the predicate function.
             * @return bool True when every value passes it.
             */
            private function all(string $function): bool
            {
                foreach ($this->value as $value) {
                    if (!$function($value)) {
                        return false;
                    }
                }

                return true;
            }
        };
    }

    /**
     * Creates predicates that must hold for every entry.
     *
     * @return object Universal predicate helper exposing `all()`.
     */
    public function are(): object
    {
        return new class($this->value) {
            /**
             * Creates a universal predicate helper.
             *
             * @param array<int|string, mixed> $value Values to inspect.
             */
            public function __construct(private array $value)
            {
            }

            /**
             * Checks all values against a target or returns nested predicates.
             *
             * @param mixed|null $what Optional loose-equality target.
             * @return object|bool Predicate helper, or direct equality result.
             */
            public function all(mixed $what = null): object|bool
            {
                $predicate = new class($this->value, $what) {
                    /**
                     * Creates a universal value predicate.
                     *
                     * @param array<int|string, mixed> $value Values to inspect.
                     * @param mixed|null $what Optional equality target.
                     */
                    public function __construct(private array $value, private mixed $what)
                    {
                    }

                    /**
                     * Checks whether every value is `null`.
                     *
                     * @return bool True when all entries are null.
                     */
                    public function null(): bool
                    {
                        return array_all($this->value, static fn (mixed $value): bool => $value === null);
                    }

                    /**
                     * Checks whether every value equals the configured target.
                     *
                     * @return bool True when every entry matches loosely.
                     */
                    public function assert(): bool
                    {
                        return array_all($this->value, fn (mixed $value): bool => $value == $this->what);
                    }

                    /**
                     * Checks whether every value is empty under PHP rules.
                     *
                     * @return bool True when every entry is empty.
                     */
                    public function empty(): bool
                    {
                        return array_all($this->value, static fn (mixed $value): bool => empty($value));
                    }
                };

                return $what !== null ? $predicate->assert() : $predicate;
            }
        };
    }

    /**
     * Creates predicates for array shape and selected values.
     *
     * @return object Shape predicate helper exposing `any()`, `map()`, and
     *                `nested()`.
     */
    public function is(): object
    {
        return new class($this->value) {
            /**
             * Creates a shape predicate helper.
             *
             * @param array<int|string, mixed> $value Values to inspect.
             */
            public function __construct(private array $value)
            {
            }

            /**
             * Creates predicates that match at least one value.
             *
             * @return object Helper exposing `empty()` and `null()`.
             */
            public function any(): object
            {
                return new class($this->value) {
                    /**
                     * Creates an any-value predicate helper.
                     *
                     * @param array<int|string, mixed> $value Values to inspect.
                     */
                    public function __construct(private array $value)
                    {
                    }

                    /**
                     * Checks whether any value is empty.
                     *
                     * @return bool True when at least one entry is empty.
                     */
                    public function empty(): bool
                    {
                        foreach ($this->value as $value) {
                            if (empty($value)) {
                                return true;
                            }
                        }

                        return false;
                    }

                    /**
                     * Checks whether any value is `null`.
                     *
                     * @return bool True when at least one entry is null.
                     */
                    public function null(): bool
                    {
                        return in_array(null, $this->value, true);
                    }
                };
            }

            /**
             * Checks whether the array is a non-empty associative map.
             *
             * @return bool True when the array is map-shaped.
             */
            public function map(): bool
            {
                return $this->value !== [] && !array_is_list($this->value);
            }

            /**
             * Checks whether at least one entry is an array.
             *
             * @return bool True when the array contains nested arrays.
             */
            public function nested(): bool
            {
                foreach ($this->value as $value) {
                    if (is_array($value)) {
                        return true;
                    }
                }

                return false;
            }
        };
    }

    /**
     * Serializes selected associative entries into token syntax.
     *
     * @param array<int, int|string>|null $keys Keys to include, or `null` for all.
     * @return string `key:value|key:value` representation.
     * @throws \InvalidArgumentException When the array is not a map or contains objects.
     */
    public function tokenize(?array $keys = null): string
    {
        if (!$this->is()->map()) {
            throw new \InvalidArgumentException('Tokenization requires an associative array.');
        }

        foreach ($this->value as $value) {
            if (is_object($value)) {
                throw new \InvalidArgumentException('Token values cannot be objects.');
            }
        }

        $keys ??= $this->keys();
        $parts = [];

        foreach ($this->value as $key => $value) {
            if (!in_array($key, $keys, true)) {
                continue;
            }

            $parts[] = $key . ':' . (is_array($value) ? implode(',', $value) : (string) $value);
        }

        return implode('|', $parts);
    }

    /**
     * Creates a helper for sorting values or keys.
     *
     * @return object Sort helper exposing `asc()` and `desc()`.
     */
    public function sort(): object
    {
        return new class($this->value) {
            /**
             * Creates a sort helper.
             *
             * @param array<int|string, mixed> $value Values to sort.
             */
            public function __construct(private array $value)
            {
            }

            /**
             * Sorts ascending by value or key.
             *
             * @param bool $byKey Sort by keys when true; values otherwise.
             * @return array<int|string, mixed> Sorted values.
             */
            public function asc(bool $byKey = false): array
            {
                $byKey ? ksort($this->value) : asort($this->value);

                return $this->value;
            }

            /**
             * Sorts descending by value or key.
             *
             * @param bool $byKey Sort by keys when true; values otherwise.
             * @return array<int|string, mixed> Sorted values.
             */
            public function desc(bool $byKey = false): array
            {
                $byKey ? krsort($this->value) : arsort($this->value);

                return $this->value;
            }
        };
    }

    /**
     * Creates a multi-column sorter for nested rows.
     *
     * @return object Ordering helper exposing `asc()`, `desc()`, and `yield()`.
     * @throws \InvalidArgumentException When the wrapped value has no nested rows.
     */
    public function order(): object
    {
        if (!$this->is()->nested()) {
            throw new \InvalidArgumentException('Ordering requires an array of rows.');
        }

        return new class($this->value) {
            /** @var list<array{column:int|string,direction:int}> */
            private array $criteria = [];

            /**
             * Creates an ordering helper.
             *
             * @param array<int|string, mixed> $value Rows to sort.
             */
            public function __construct(private array $value)
            {
            }

            /**
             * Adds an ascending column criterion.
             *
             * @param int|string $column Row key to sort by.
             * @return static This ordering helper.
             */
            public function asc(int|string $column): static
            {
                $this->criteria[] = ['column' => $column, 'direction' => 1];

                return $this;
            }

            /**
             * Adds a descending column criterion.
             *
             * @param int|string $column Row key to sort by.
             * @return static This ordering helper.
             */
            public function desc(int|string $column): static
            {
                $this->criteria[] = ['column' => $column, 'direction' => -1];

                return $this;
            }

            /**
             * Applies all configured criteria and returns sorted rows.
             *
             * @return array<int, mixed> Sorted rows.
             */
            public function yield(): array
            {
                $rows = [];
                foreach (array_values($this->value) as $index => $row) {
                    $rows[] = [$index, $row];
                }

                /**
                 * Compares two decorated rows using the configured criteria.
                 *
                 * @param array{0: int, 1: mixed} $left First decorated row.
                 * @param array{0: int, 1: mixed} $right Second decorated row.
                 * @return int Sort comparison result.
                 */
                usort($rows, function (array $left, array $right): int {
                    foreach ($this->criteria as $criterion) {
                        $column = $criterion['column'];
                        $a = is_array($left[1]) ? ($left[1][$column] ?? null) : null;
                        $b = is_array($right[1]) ? ($right[1][$column] ?? null) : null;

                        $comparison = $this->compareValues($a, $b);
                        if ($comparison !== 0) {
                            return $comparison * $criterion['direction'];
                        }
                    }

                    return $left[0] <=> $right[0];
                });

                return array_column($rows, 1);
            }

            /**
             * Compares two cell values for the row sorter.
             *
             * @param mixed $left First cell value.
             * @param mixed $right Second cell value.
             * @return int Negative, zero, or positive comparison result.
             */
            private function compareValues(mixed $left, mixed $right): int
            {
                if ($left === $right) {
                    return 0;
                }

                if ($left === null) {
                    return -1;
                }

                if ($right === null) {
                    return 1;
                }

                if (is_numeric($left) && is_numeric($right)) {
                    return ((float) $left) <=> ((float) $right);
                }

                return (string) $left <=> (string) $right;
            }
        };
    }

    /**
     * Ensures aggregate arithmetic can safely operate on this array.
     *
     * @return void
     * @throws \InvalidArgumentException When values are nested or incompatible.
     */
    private function assertFlatNumeric(): void
    {
        if ($this->is()->nested() || (!$this->isof()->numbers() && !$this->isof()->booleans())) {
            throw new \InvalidArgumentException('Incompatible array.');
        }
    }

    /**
     * Creates a key/value comparison helper.
     *
     * @param array<int|string, mixed> $other Comparison array.
     * @param bool $intersection Select shared entries when true, differences otherwise.
     * @return object Comparison helper exposing `keys()`, `values()`, and `map()`.
     */
    private function comparison(array $other, bool $intersection): object
    {
        $source = $this->value;

        return new class($source, $other, $intersection) {
            /**
             * Creates a comparison helper.
             *
             * @param array<int|string, mixed> $source Source values.
             * @param array<int|string, mixed> $other Comparison values.
             * @param bool $intersection Whether to return shared entries.
             */
            public function __construct(
                private array $source,
                private array $other,
                private bool $intersection,
            ) {
            }

            /**
             * Compares entries by key.
             *
             * @return Arr Key-based intersection or difference.
             */
            public function keys(): Arr
            {
                $values = $this->intersection
                    ? array_intersect_key($this->source, $this->other)
                    : array_diff_key($this->source, $this->other);

                return Arr::from($values);
            }

            /**
             * Compares entries by value.
             *
             * @return Arr Value-based intersection or difference.
             */
            public function values(): Arr
            {
                $values = [];
                foreach ($this->source as $key => $value) {
                    $found = in_array($value, $this->other);
                    if ($found === $this->intersection) {
                        $values[$key] = $value;
                    }
                }

                return Arr::from($values);
            }

            /**
             * Compares entries by matching key and loosely equal value.
             *
             * @return Arr Key/value intersection or difference.
             */
            public function map(): Arr
            {
                $values = [];
                foreach ($this->source as $key => $value) {
                    $matches = array_key_exists($key, $this->other) && $value == $this->other[$key];
                    if ($matches === $this->intersection) {
                        $values[$key] = $value;
                    }
                }

                return Arr::from($values);
            }
        };
    }
}

/** PHP 8.2-compatible equivalent of PHP 8.4's array_all(). */
if (!function_exists('array_all')) {
    /**
     * Returns true when a callback accepts every array value.
     *
     * @param array<int|string, mixed> $array Values to inspect.
     * @param callable(mixed): bool $callback Predicate to apply.
     * @return bool True when every value passes the callback.
     */
    function array_all(array $array, callable $callback): bool
    {
        foreach ($array as $value) {
            if (!$callback($value)) {
                return false;
            }
        }

        return true;
    }
}
