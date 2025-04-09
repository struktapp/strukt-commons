<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface CollectionInterface{

	public function keys():array;
	public function set(string $key, $val):void;
	public function get(string $key);
	public function remove(string $key):void;
	public function exists(string $key):bool;
}