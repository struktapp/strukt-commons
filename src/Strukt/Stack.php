<?php

namespace Strukt;

/** A stack handle that records its initial message globally. */
class Stack extends \Strukt\Contract\Stack
{
    /** Message associated with this stack handle. */
    protected string $message = '';

    /**
     * Creates a stack handle and optionally records its first message.
     *
     * @param string|int|null $message Optional initial message.
     */
    public function __construct(string|int|null $message = null)
    {
        if ($message !== null) {
            $this->message = (string) $message;
            parent::__construct($message);
        }
    }

    /**
     * Returns this handle's initial message.
     *
     * @return string Initial message, or an empty string when none was supplied.
     */
    public function __toString(): string
    {
        return $this->message;
    }
}
