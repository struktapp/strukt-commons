<?php

namespace Strukt;

use Strukt\Contract\CollectionInterface;
use Strukt\Collection;

/**
* Strukt Application Core Registry
*
* @author Moderator <pitsolu@gmail.com>
*/
class Registry implements CollectionInterface{

	/**
	* Singleton Registry Instance
	*/
	private static $registry = null;

	/**
	* Raw registry
	*/
	private $register = null;

	/**
	* Constructor initialize Strukt global register
	*/
	private function __construct(){

		$this->register = new Collection([]);

		$today = new Today;
		if(!$this->register->exists("today"))
			$this->register->set("today", $today->format("Y-m-d"));
	}

	/**
	* Getter for Singleton registry instance
	*
	* @return static
	*/
	public static function getInstance():static{

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
	public function get(string $key):mixed{

		return $this->register->get($key);
	}

	/**
	* Getter registry value but as collection
	*
	* @param string $key
	*
	* @return mixed
	*/
	public function ask(string $key):mixed{

		return $this->register->ask($key);
	}

	/**
	* Setter for registry value
	*
	* @param string $key
	* @param mixed $val
	*
	* @return void
	*/
	public function set(string $key, mixed $val):void{

		$this->register->set($key, $val);
	}

	/**
	* Remove registry value
	*
	* @param string $key
	*
	* @return void
	*/
	public function remove(string $key){

		return $this->register->remove($key);
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
	public function keys(?string $key = null):array{

		return $this->register->keys($key);
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