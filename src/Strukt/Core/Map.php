<?php

namespace Strukt\Core;

/**
* Map class
*
* Seamlessly allows access to nested {@link Strukt\Core\Collection} 
* objects, uses dot notation e.g $map->get("user.role.decr") for
* all methods
* 
* @author Moderator <pitsolu@gmail.com>
*/
class Map implements \Strukt\Contract\CollectionInterface{

	use \Strukt\Traits\Collection;

	/**
	* List of items
	*
	* @var array
	*/
	private $collection = null;

	/**
	* Constructor create collection if one is not passed in.
	*
	* @param Strukt\Core\Collection $collection
	*/
	public function __construct(Collection $collection = null){

		if(!is_null($collection))
			$this->collection = $collection;
		
		if(is_null($this->collection))
			$this->collection = new Collection();
	}

	/**
	* Getter for collection keys
	* 
	* @return array
	*/
	public function keys():array{

		return $this->collection->keys();
	}

	/**
	* Remove items from map
	*
	* @param string $hashKey
	*
	* @return void
	*/
	public function remove(string $hashKey):void{

		$keyList = explode(".", $hashKey);
		$collection = $this->collection;

		$lastKey = array_pop($keyList);
		
		foreach($keyList as $seqKey=>$key)
			if($collection->exists($key)){

				$val = $collection->get($key);
				if($val instanceof Collection)
					$collection = $val;
			}	

		$collection->remove($lastKey);	
	}

	/**
	* Set map item
	*
	* @param string $key
	* @param string $val
	*
	* @return void
	*/
	public function set(string $key, $val):void{

		$this->assemble($key, $val, $this->collection);
	}

	/**
	* Getter for map items
	*
	* @return mixed
	*/
	public function get(string $key){

		return $this->collection->get($key);
	}

	/**
	* Check if item exists in map
	*
	* @return boolean
	*/
	public function exists(string $key):bool{

		return $this->collection->exists($key);
	}
}