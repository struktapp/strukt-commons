<?php

namespace Strukt;

use Strukt\Core\Registry;

/**
 * Alias for Strukt\Core\Registry
 * 
 * For faster access to Strukt Registry 
 */
class Reg{

	public static function get($key){

		return Registry::getSingleton()->get($key);
	}

	public static function set($key, $val){

		Registry::getSingleton()->set($key, $val);	
	}

	public static function exists($key){

		return Registry::getSingleton()->exists($key);
	}
}