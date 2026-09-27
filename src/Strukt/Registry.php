<?php

namespace Strukt;

use Strukt\Contract\CollectionInterface;

/** Process-wide registry backed by a dot-path collection. */
class Registry implements CollectionInterface
{
    /** Singleton registry instance for the current process. */
    private static ?self $registry = null;
    /** Collection containing registered values. */
    private Collection $register;

    /** Creates the backing collection and its default `today` value. */
    private function __construct()
    {
        $this->register = new Collection();
        $this->register->set('today', (new Today())->format('Y-m-d'));
    }

    /**
     * Returns the process-wide registry instance.
     *
     * @return static Shared registry instance.
     */
    public static function getInstance(): static
    {
        return static::$registry ??= new static();
    }

    /**
     * Clears the singleton so the next access creates a fresh registry.
     *
     * @return void
     */
    public static function resetInstance(): void
    {
        static::$registry = null;
    }

    /**
     * Reads a value by dot-separated key.
     *
     * @param string $key Registry path to read.
     * @return mixed Registered value, or `null` when absent.
     */
    public function get(string $key): mixed
    {
        return $this->register->get($key);
    }

    /**
     * Returns a nested registry map as a collection.
     *
     * @param string $key Registry path to a map.
     * @return Collection|null Child collection, or `null` for another value type.
     */
    public function ask(string $key): ?Collection
    {
        return $this->register->ask($key);
    }

    /**
     * Stores a value under a dot-separated registry path.
     *
     * @param string $key Registry path to write.
     * @param mixed $value Value to store.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $this->register->set($key, $value);
    }

    /**
     * Removes and returns a registry value.
     *
     * @param string $key Registry path to remove.
     * @return mixed Removed value, or `null` when absent.
     */
    public function remove(string $key): mixed
    {
        return $this->register->remove($key);
    }

    /**
     * Checks whether a registry path exists.
     *
     * @param string $key Registry path to check.
     * @return bool True when the path exists, including a `null` value.
     */
    public function exists(string $key): bool
    {
        return $this->register->exists($key);
    }

    /**
     * Lists root keys or keys below a nested registry path.
     *
     * @param string|null $key Optional registry path.
     * @return array<int|string> Keys at the selected level.
     */
    public function keys(?string $key = null): array
    {
        return $this->register->keys($key);
    }

    /**
     * Lists keys at the registry root.
     *
     * @return array<int|string> Root registry keys.
     */
    public function ls(): array
    {
        return $this->keys();
    }
}
