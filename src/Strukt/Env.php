<?php

namespace Strukt;

use Strukt\Core\Registry;

class Env{

	public static function get($key){

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		if(!$registry->exists($key))
			throw new \Exception(sprintf("Couldn't get [%s], may not be set by %s!", $key, __CLASS__));

		return $registry->get($key);
	}

	public static function set($key, $val){

		if(!is_string($key) && !is_string($val))
			throw new \Exception(sprintf("%s::set(key,val) key and val must be strings!", 
											get_class($this)));
			
		Registry::getSingleton()->set(sprintf("env.%s", $key), $val);
	}
}