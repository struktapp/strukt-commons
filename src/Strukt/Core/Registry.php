<?php

namespace Strukt\Core;

use Strukt\Core\Collection;
use Strukt\Core\Map;

/**
* Strukt Application Core Registry
*
* @author Moderator <pitsolu@gmail.com>
*/
class Registry implements \Strukt\Contract\CollectionInterface{

	/**
	* Singleton Registry Instance
	*
	* @var Strukt\Core\Map $map
	*/
	private static $registry = null;

	/**
	* Raw registry
	*
	* @var Strukt\Core\Map $map
	*/
	private $register = null;

	/**
	* Constructor initialize Strukt global register
	*/
	private function __construct(){

		$this->register = new Map(new Collection("strukt-registry"));

		if(!$this->register->exists("today"))
			$this->register->set("today", new Today);
	}

	/**
	* Getter for Singleton registry instance
	*
	* @return \Strukt\Framework\Registry
	*/
	public static function getSingleton(){

		if(is_null(static::$registry))
			static::$registry = new self;

		return static::$registry;
	}

	/**
	* Getter registry value
	*
	* @param string $key
	*
	* @return mixed
	*/
	public function get(string $key){

		return $this->register->get($key);
	}

	/**
	* Setter for registry value
	*
	* @param string $key
	* @param string $val
	*
	* @return void
	*/
	public function set(string $key, $val):void{

		$this->register->set($key, $val);
	}

	/**
	* Remove registry value
	*
	* @param string $key
	*
	* @return void
	*/
	public function remove(string $key):void{

		$this->register->remove($key);
	}

	/**
	* Check existence of registry value
	*
	* @param string $key
	*
	* @return boolean
	*/
	public function exists(string $key):bool{

		return $this->register->exists($key);
	}

	/**
	* List registry keys
	*
	* @return array
	*/
	public function keys():array{

		return $this->register->keys();
	}

	/**
	* fn keys alias
	*
	* @return array
	*/
	public function ls():array{

		return $this->keys();
	}
}