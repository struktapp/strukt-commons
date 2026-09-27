<?php

namespace Strukt;

use Strukt\Contract\CollectionInterface;

/** Mutable access to an array through dot-separated paths. */
class Collection implements CollectionInterface
{
    /** @var array<int|string, mixed> Values held by this collection. */
    private array $value;

    /**
     * Creates a collection from an initial array.
     *
     * @param array<int|string, mixed> $value Initial collection values.
     */
    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    /**
     * Stores a value at a dot-separated path, creating parents as needed.
     *
     * @param string $key Path to write.
     * @param mixed $value Value to store.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        self::attach($key, $this->value, $value);
    }

    /**
     * Reads a value from a dot-separated path.
     *
     * @param string $key Path to read.
     * @return mixed Stored value, or `null` when the path is absent.
     */
    public function get(string $key): mixed
    {
        return self::dot($key, $this->value);
    }

    /**
     * Returns a child collection when a path contains a non-empty map.
     *
     * @param string $key Path to a nested map.
     * @return self|null Child collection, or `null` for another value type.
     */
    public function ask(string $key): ?self
    {
        $value = $this->get($key);

        return is_array($value) && self::isMap($value) ? new self($value) : null;
    }

    /**
     * Removes and returns a value from a dot-separated path.
     *
     * @param string $key Path to remove.
     * @return mixed Removed value, or `null` when the path is absent.
     */
    public function remove(string $key): mixed
    {
        return self::detach($key, $this->value);
    }

    /**
     * Lists keys at the root or below a nested array path.
     *
     * @param string|null $key Optional path whose keys should be listed.
     * @return array<int|string> Keys at the selected level.
     */
    public function keys(?string $key = null): array
    {
        $value = $key === null ? $this->value : $this->get($key);

        return is_array($value) ? array_keys($value) : [];
    }

    /**
     * Checks whether a complete path exists, including a `null` value.
     *
     * @param string $key Path to check.
     * @return bool True when every path segment exists.
     */
    public function exists(string $key): bool
    {
        $value = $this->value;

        foreach (explode('.', $key) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return false;
            }

            $value = $value[$part];
        }

        return true;
    }

    /** @return array */
    /**
     * Returns all values currently held by the collection.
     *
     * @return array<int|string, mixed> Collection values.
     */
    public function all(): array
    {
        return $this->value;
    }

    /**
     * Returns the raw collection array.
     *
     * @return array<int|string, mixed> Collection values.
     */
    public function yield(): array
    {
        return $this->all();
    }

    /**
     * Reads a dot-separated path from an array.
     *
     * @param string $path Path to read.
     * @param array<int|string, mixed> $map Array to inspect.
     * @return mixed Value at the path, or `null` when it is missing.
     */
    public static function dot(string $path, array &$map): mixed
    {
        $value = $map;

        foreach (explode('.', $path) as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return null;
            }

            $value = $value[$part];
        }

        return $value;
    }

    /**
     * Writes a value at a dot-separated path, creating intermediate arrays.
     *
     * @param string $path Path to write.
     * @param array<int|string, mixed> $map Array to modify by reference.
     * @param mixed $value Value to assign.
     * @return void
     */
    public static function attach(string $path, array &$map, mixed $value): void
    {
        $parts = explode('.', $path);
        $key = array_pop($parts);
        $target =& $map;

        foreach ($parts as $part) {
            if (!isset($target[$part]) || !is_array($target[$part])) {
                $target[$part] = [];
            }

            $target =& $target[$part];
        }

        $target[$key] = $value;
    }

    /**
     * Removes and returns a value at a dot-separated path.
     *
     * @param string $path Path to remove.
     * @param array<int|string, mixed> $map Array to modify by reference.
     * @return mixed Removed value, or `null` when the path is missing.
     */
    public static function detach(string $path, array &$map): mixed
    {
        $parts = explode('.', $path);
        $key = array_pop($parts);
        $target =& $map;

        foreach ($parts as $part) {
            if (!is_array($target) || !array_key_exists($part, $target)) {
                return null;
            }

            $target =& $target[$part];
        }

        if (!is_array($target) || !array_key_exists($key, $target)) {
            return null;
        }

        $result = $target[$key];
        unset($target[$key]);

        return $result;
    }

    /**
     * Determines whether an array is a non-empty associative map.
     *
     * @param array<int|string, mixed> $value Array to inspect.
     * @return bool True when the array is non-empty and not a list.
     */
    private static function isMap(array $value): bool
    {
        return $value !== [] && !array_is_list($value);
    }
}
