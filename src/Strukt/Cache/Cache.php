<?php

namespace Strukt\Cache;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Cache{

	private $cache;

	/**
	 * @param string $filename
	 * @param string $driver
	 */
	public function __construct(string $filename, string $driver = Driver\Fs::class){

		if(config("cache.disable"))
			$driver = Driver\Memory::class;

		$this->cache = new $driver($filename);
	}

	/**
	 * @param string $filename
	 * 
	 * @return static
	 */
	public static function make(string $filename):static{

		return new self($filename);
	}

	/**
	 * @param string $key
	 * 
	 * @return bool
	 */
	public function exists(string $key):bool{

		return $this->cache->exists($key);
	}

	/**
	 * @param string $key
	 * @param string|array $val
	 * 
	 * @return static
	 */
	public function put(string $key, string|array $val):static{

		$this->cache->put($key, $val);

		return $this;
	}

	/**
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function get(string $key):mixed{

		return $this->cache->get($key);
	}

	/**
	 * @param string $key
	 * 
	 * return static
	 */
	public function remove(string $key):static{

		$this->cache->remove($key);

		return $this;
	}

	/**
	 * @return bool
	 */
	public function empty():bool{

		return $this->cache->empty();
	}

	/**
	 * @return void
	 */
	public function save():void{

		$this->cache->save();
	}
}