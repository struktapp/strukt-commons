<?php

namespace Strukt\Contract;

interface CacheDriverInterface{

	public function put(string $key, string|array $val):self;
	public function get(string $key):mixed;
	public function remove(string $key):self;
	public function save():void;
}