<?php

namespace Strukt\Cache\Driver;

use Strukt\Contract\CacheDriverInterface;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Memory implements CacheDriverInterface{

	use \Strukt\Traits\Collection;

	private static $cache = [];
	private $buffer;

	/**
	 * @param string $file
	 */
	public function __construct(string $file){

		if(!array_key_exists($file, self::$cache))
			self::$cache[$file] = map([]);

		$this->buffer = self::$cache[$file];
	}

	/**
	 * @param string $key
	 */
	public function exists(string $key):bool{

		return $this->buffer->exists($key);		
	}

	/**
	 * @return bool
	 */
	public function empty():bool{

		return empty($this->buffer->keys());
	}

	/**
	 * @param string $key
	 * @param string|array $val
	 * 
	 * @return static
	 */
	public function put(string $key, string|array $val):static{

		if(is_array($val))
			if(arr($val)->isMap())
				$val = collect($val);

		if($this->exists($key))
			$this->remove($key);

		$this->buffer->set($key, $val);
		
		return $this;
	}

	/**
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public function get(string $key):mixed{

		$val = $this->buffer->get($key);

		if(is_array($val))
			if(arr($val)->isMap())
				$val = collect($val);

		return $val;
	}

	/**
	 * @param string $key
	 * 
	 * @return static
	 */
	public function remove(string $key):static{

		$this->buffer->remove($key);
		
		return $this;
	}

	public function save():void{

		//
	}
}