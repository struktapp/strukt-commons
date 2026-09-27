<?php

namespace Strukt\Contract;

/** Defines mutable dot-path access to an array-backed collection. */
interface CollectionInterface
{
    /**
     * Lists keys at the root or at a nested path.
     *
     * @param string|null $key Optional dot-separated path.
     * @return array<int|string> Keys at the selected level.
     */
    public function keys(?string $key = null): array;

    /**
     * Stores a value at a dot-separated path.
     *
     * @param string $key Path to write.
     * @param mixed $value Value to store.
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Reads a value at a dot-separated path.
     *
     * @param string $key Path to read.
     * @return mixed Stored value, or `null` when the path is absent.
     */
    public function get(string $key): mixed;

    /**
     * Removes and returns a value at a dot-separated path.
     *
     * @param string $key Path to remove.
     * @return mixed Removed value, or `null` when the path is absent.
     */
    public function remove(string $key): mixed;

    /**
     * Checks whether every segment of a path exists.
     *
     * @param string $key Path to check.
     * @return bool True when the path exists, including a stored `null` value.
     */
    public function exists(string $key): bool;
}
