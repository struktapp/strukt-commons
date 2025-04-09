<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface JsonAssert{
	
	public function assert(string $key, ?callable $fn = null):mixed;
}