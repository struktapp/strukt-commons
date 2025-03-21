<?php

namespace Strukt\Builder;

use Strukt\Core\Collection as BaseCollection;

/**
* CollectionBuilder class
*
* Build Strukt\Core\Collection from array
* 
* @author Moderator <pitsolu@gmail.com>
*/
class Collection{

	/**
	* collection
	*
	* @var Strukt\Core\Collection
	*/
	private $collection = null;

	/**
	* Constructor
	*
	* @param \Strukt\Core\Collection $collection
	*/
	public function __construct(?BaseCollection $collection = null){

		if(is_null($collection))
			$collection = new BaseCollection();
		
		$this->collection = $collection;
	}

	/**
	* Static constructor
	*
	* @return \Strukt\Builder\Collection
	*/
	public static function create(?BaseCollection $collection = null):static{

		return new self($collection);
	}

	/**
	* Create collection from associatve array
	*
	* @param array $array
	*
	* @return Strukt\Core\Collection
	*/
	public function fromAssoc(array $array):BaseCollection{

		foreach($array as $key=>$val){

			if(is_array($val))
				if(!empty(array_filter(array_keys($val), "is_string")))
					$val = Collection::create(new BaseCollection($key))->fromAssoc($val);

			$this->collection->set($key, $val);
		}

		return $this->collection;
	}
}