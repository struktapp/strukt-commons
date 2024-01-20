<?php

namespace Strukt\Core;

use Strukt\Exception\KeyOverlapException;
use Strukt\Exception\KeyNotFoundException;
use Strukt\Exception\InvalidKeyException;
use Strukt\Builder\Collection as CollectionBuilder;

/**
* Collection class
*
* Allows collection and access of unstructured data sets
* 
* @author Moderator <pitsolu@gmail.com>
*/
class Collection implements \Strukt\Contract\CollectionInterface{

	/**
	* Raw collection
	*
	* @var array
	*/
	private $collection = null;

	/**
	* Collection name
	*
	* @var string
	*/
	private $name = null;

	/**
	* Constructor generates hash as name if no name is given
	*
	* @param string $name
	*/
	public function __construct(string $name = null){

		if(!is_null($name))
			$this->name = $name;
		
		if(is_null($this->name))
			$this->name = md5(rand());

		$this->collection = array();
	}

	/**
	* Getter for collection name
	*
	* @return string
	*/
	public function getName(){

		return $this->name;
	}

	/**
	* Getter for collection keys
	* 
	* @return array
	*/
	public function keys():array{

		return array_keys($this->collection);
	}

	/**
	* Setter for collection items
	*
	* @param string $key
	* @param string $val
	*
	* @return void
	*/
	public function set(string $key, $val):void{

		if (strpos($key, '.') !== false)
			throw new InvalidKeyException($key);

		if($this->exists($key))
			if(!empty($this->get($key)))
				throw new KeyOverlapException($key);

		$this->collection[$key] = $val;
	}

	/**
	* Remove collection item
	*
	* @param string $key
	* @param string $val
	*
	* @return void
	*/
	public function remove(string $key):void{

		unset($this->collection[$key]);
	}

	/**
	* Getter for collection item
	*
	* Uses dot notation e.g $collection->get("user.role.decr")
	*
	* @param string $key
	*
	* @throws \Exception
	*
	* @return mixed
	*/
	public function get(string $key){

		$keyList = explode(".", $key);

		if(count($keyList)-1){

			$obj = $this->getCollection(array_shift($keyList));

			if($obj instanceof Collection)
				$obj = $obj->get(implode(".", $keyList));

			return $obj; 
		}

		return $this->getCollection($key);
			
	}

	/**
	* Getter for items in current collection
	*
	* @param string $key
	*
	* @throws \Exception
	*
	* @return mixed
	*/
	private function getCollection($key){

		if(key_exists($key, $this->collection))
			return $this->collection[$key];

		throw new KeyNotFoundException($key);	
	}

	/**
	* Key exists
	*
	* Uses dot notation e.g $collection->exists("user.role.decr")
	*
	* @param string $key
	*
	* @return boolean
	*/
	public function exists(string $key):bool{

		try{

			$this->get($key);

			return true;
		}
		catch(\Exception $e){

			return false;
		}	
	}
}