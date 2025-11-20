<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface CollectionInterface{

	public function keys(?string $key = null):array;
	public function set(string $key, $val):void;
	public function get(string $key);
	public function remove(string $key);
	public function exists(string $key):bool;
}