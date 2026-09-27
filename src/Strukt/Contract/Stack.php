<?php

namespace Strukt\Contract;

use Strukt\Arr;

/** Maintains a bounded, process-wide stack of messages. */
abstract class Stack
{
    /** @var list<mixed> */
    protected static array $messages = [];

    /** Maximum number of messages retained by the stack. */
    protected static int $limit = 10;

    /**
     * Adds an initial message to the shared stack.
     *
     * @param string|int $message Message to record.
     */
    public function __construct(string|int $message)
    {
        $this->add($message);
    }

    /**
     * Appends a message and removes the oldest entries beyond the limit.
     *
     * @param mixed $message Value to add to the process-local stack.
     * @return void
     */
    public function add(mixed $message): void
    {
        static::$messages[] = $message;

        if (count(static::$messages) > static::$limit) {
            static::$messages = array_slice(static::$messages, -static::$limit);
        }
    }

    /**
     * Sets the maximum number of retained messages.
     *
     * @param int $limit Maximum number of messages; values below one become one.
     * @return void
     */
    public static function limit(int $limit): void
    {
        static::$limit = max(1, $limit);
        static::$messages = array_slice(static::$messages, -static::$limit);
    }

    /**
     * Returns retained messages, optionally filtered by a regular expression.
     *
     * @param string|null $pattern Regular-expression pattern for filtering.
     * @return Arr Array wrapper containing the selected messages.
     * @throws \InvalidArgumentException When `$pattern` is invalid.
     */
    public static function get(?string $pattern = null): Arr
    {
        $messages = static::$messages;

        if ($pattern !== null) {
            $filtered = @preg_grep($pattern, $messages);
            if ($filtered === false) {
                throw new \InvalidArgumentException('The stack pattern is not a valid regular expression.');
            }

            $messages = array_values($filtered);
        }

        return Arr::from($messages);
    }

    /**
     * Removes every message from the shared stack.
     *
     * @return void
     */
    public static function clear(): void
    {
        static::$messages = [];
    }
}
