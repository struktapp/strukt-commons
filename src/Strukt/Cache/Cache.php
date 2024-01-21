<?php

namespace Strukt\Cache;

class Cache{

	private $cache;

	public function __construct(string $filename, string $driver = Driver\Fs::class){

		if(config("cache.disable"))
			$driver = Driver\Memory::class;

		$this->cache = new $driver($filename);
	}

	public static function make(string $filename){

		return new self($filename);
	}

	public function exists(string $key):bool{

		return $this->cache->exists($key);
	}

	public function put(string $key, string|array $val){

		$this->cache->put($key, $val);

		return $this;
	}

	public function get(string $key){

		return $this->cache->get($key);
	}

	public function remove(string $key){

		$this->cache->remove($key);

		return $this;
	}

	public function empty():bool{

		return $this->cache->empty();
	}

	public function save(){

		$this->cache->save();
	}
}