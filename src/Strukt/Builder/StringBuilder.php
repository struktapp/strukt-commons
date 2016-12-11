<?php

namespace Strukt\Builder;

/**
* StringBuilder class
*
* Helps in organizing strings when dealing with 
* numerous string manipulation operations
* 
* @author Moderator <pitsolu@gmail.com>
*/
class StringBuilder{

	/**
	* Delimiter for separation of string items
	*
	* @var string
	*/
	private $delimiter;

	/**
	* String items to be concatenated
	*
	* @var array
	*/
	private $items;

	/**
	* Constructor
	*
	* @param string $delimeter
	*/
	public function __construct($delimiter=" "){

		$this->delimiter = $delimiter;
	}

	/**
	* Static constructor
	*
	* @param string $delimeter
	*/
	public function getInstance($delimiter=" "){

		return new self($delimiter);
	}

	/**
	* Add item to builder
	*
	* @param string $item
	*
	* @return void
	*/
	public function add($item){

		if(is_callable($item))
			$this->items[] = call_user_func($item);
		else
			$this->items[] = (string)$item;

		return $this;
	}

	/**
	* Execute builder i.e build string
	*
	* @return string
	*/
	public function __toString(){

		return implode($this->delimiter, $this->items);
	}
}