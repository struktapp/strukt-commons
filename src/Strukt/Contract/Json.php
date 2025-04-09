<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface Json{
	
	public function decode():array;
	public function encode():string;
}