<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
interface CacheDriverInterface{

	public function exists(string $key):bool;
	public function empty():bool;
	public function put(string $key, string|array $val):self;
	public function get(string $key):mixed;
	public function remove(string $key):self;
	public function save():void;
}