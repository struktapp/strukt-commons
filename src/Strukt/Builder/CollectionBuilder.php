<?php

namespace Strukt\Builder;

use Strukt\Core\Collection;

/**
* CollectionBuilder class
*
* Build Strukt\Core\Collection from array
* 
* @author Moderator <pitsolu@gmail.com>
*/
class CollectionBuilder{

	/**
	* collection
	*
	* @var Strukt\Core\Collection
	*/
	private $collection = null;

	/**
	* Constructor
	*
	* @param Strukt\Core\Collection $collection
	*/
	public function __construct(Collection $collection = null){

		if(is_null($collection))
			$collection = new Collection();
		
		$this->collection = $collection;
	}

	/**
	* Static constructor
	*
	* @return Strukt\Builder\CollectionBuilder
	*/
	public function getInstance(Collection $collection = null){

		return new self($collection);
	}

	/**
	* Create collection from associatve array
	*
	* @param array $array
	*
	* @return Strukt\Core\Collection
	*/
	public function fromAssoc(Array $array){

		foreach($array as $key=>$val){

			if(is_array($val))
				if(!empty(array_filter(array_keys($val), "is_string")))
					$val = CollectionBuilder::getInstance(new Collection($key))->fromAssoc($val);

			$this->collection->set($key, $val);
		}

		return $this->collection;
	}
}