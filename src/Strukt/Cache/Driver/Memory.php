<?php

namespace Strukt\Cache\Driver;

use Strukt\Contract\CacheDriverInterface;

class Memory implements CacheDriverInterface{

	use \Strukt\Traits\Collection;

	// private $fs;
	// private $filename;
	private static $cache = [];
	private $buffer;

	public function __construct(string $file){

		if(!array_key_exists($file, self::$cache))
			self::$cache[$file] = map([]);

		$this->buffer = self::$cache[$file];
	}

	public function exists(string $key):bool{

		return $this->buffer->exists($key);		
	}

	public function empty():bool{

		return empty($this->buffer->keys());
	}

	public function put(string $key, string|array $val):self{

		if(is_array($val))
			if(arr($val)->isMap())
				$val = collect($val);

		if($this->exists($key))
			$this->remove($key);

		$this->buffer->set($key, $val);
		
		return $this;
	}

	public function get(string $key):mixed{

		$val = $this->buffer->get($key);

		if(is_array($val))
			if(arr($val)->isMap())
				$val = collect($val);

		return $val;
	}

	public function remove(string $key):self{

		$this->buffer->remove($key);
		
		return $this;
	}

	public function save():void{

		//
	}
}