<?php

namespace Strukt;

/**
 * Small base class for wrappers around scalar and compound values.
 *
 * Subclasses decide whether an operation mutates the wrapper or returns a new
 * wrapper. Keeping the property type as `mixed` is important: PHP does not
 * allow a child class to narrow an inherited property type.
 */
abstract class ValueObject
{
    protected mixed $value;

    /**
     * Wraps a value in the value object.
     *
     * @param mixed $value Value to store.
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    /**
     * Creates an instance of the called wrapper class.
     *
     * @param mixed $value Value to wrap.
     * @return static New wrapper instance.
     */
    public static function create(mixed $value): static
    {
        return new static($value);
    }

    /**
     * Returns the raw wrapped value.
     *
     * @return mixed Stored value.
     */
    public function yield(): mixed
    {
        return $this->value;
    }

    /**
     * Compares the wrapped value using PHP's loose equality rules.
     *
     * @param mixed $value Value or value object to compare.
     * @return bool True when both values are loosely equal.
     */
    public function equals(mixed $value): bool
    {
        if ($value instanceof self) {
            $value = $value->yield();
        }

        return $this->value == $value;
    }

    /**
     * Creates a shallow clone of this wrapper.
     *
     * @return static Cloned wrapper instance.
     */
    public function clone(): static
    {
        return clone $this;
    }
}
