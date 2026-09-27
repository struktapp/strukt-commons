<?php

namespace Strukt;

/**
 * Exception used by the legacy Strukt helper API.
 *
 * The class remains intentionally small: callers receive a normal exception
 * while older code can continue to call `raise(...)` through the global
 * helper file.
 */
class Raise extends \RuntimeException
{
    /** @var list<string> Errors raised during this PHP process. */
    protected static array $errors = [];

    /**
     * Creates and records a Strukt runtime exception.
     *
     * @param string $message Human-readable error message.
     * @param int $code Application-specific exception code.
     * @param \Throwable|null $previous Optional exception that caused the error.
     */
    public function __construct(string $message, int $code = 500, ?\Throwable $previous = null)
    {
        static::$errors[] = $message;
        parent::__construct($message, $code, $previous);
    }

    /** @return list<string> */
    /**
     * Returns messages raised during the current PHP process.
     *
     * @return list<string> Recorded error messages.
     */
    public static function errors(): array
    {
        return static::$errors;
    }

    /**
     * Clears the process-local error history.
     *
     * @return void
     */
    public static function clear(): void
    {
        static::$errors = [];
    }
}
