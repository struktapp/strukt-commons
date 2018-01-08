<?php

namespace Strukt\Event;

/**
* Event Executor class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Executor{

	/**
	* Event arguments
	*
	* @var Array
	*/
	private $args = null;

	/**
	* Event
	*
	* @var Array
	*/
	private $event = null;

	/**
	* Reflected Event
	*
	* @var \ReflectionFunction
	*/
	private $reflEvent = null;

	/**
	* List of reflected parameters
	*
	* @var Array
	*/
	private $reflParams = null;

	/**
	* Constructor
	*
	* @param $event \Closure
	*/
	public function __construct(\Closure $event){

		$this->event = $event;

		$this->reflEvent = new \ReflectionFunction($this->event);

		$this->reflParams = $this->reflEvent->getParameters();
	}

	/**
	* Static constructor
	*
	* @param $event \Closure
	*/
	public function newEvent(\Closure $event){

		return new self($event);
	}

	/**
	* Apply arguments to event
	*
	* @param mixed ...
	*
	* @return \Strukt\Event\Single
	*/
	public function apply(){

		$this->args = func_get_args();

		return $this;
	}

	/**
	* Apply arguments to event
	*
	* @param $args Array
	*
	* @return \Strukt\Event\Single
	*/
	public function applyArgs(Array $args){

		$this->args = $args;

		return $this;
	}

	/**
	* Get list of reflected parameters
	*
	* @return Array
	*/
	public function getParams(){

		$params = [];
		foreach($this->reflParams as $reflParam)
			$params[(string)$reflParam->getName()] = $reflParam->getType()->getName();

		return $params;
	}

	public function expects($type){

		foreach($this->reflParams as $reflParam)
			if($reflParam->hasType())
				if($type == $reflParam->getType())
					return true;

		return false;
	}

	// public function getReflection(){

	// 	return $this->reflEvent;
	// }

	/**
	* Execute event
	*
	* @return mixed
	*/
	public function exec(){

		if(is_null($this->args))
			return call_user_func($this->event);

		if(!is_null($this->reflEvent)){
			
			$isNotAssoc = is_numeric(key($this->args));

			$args=null;
			if(!$isNotAssoc)
				foreach($this->reflParams as $reflParam)
					$args[] = $this->args[$reflParam->getName()];

			if(!is_null($args))
				$this->args = $args;

			return $this->reflEvent->invokeArgs($this->args);
		}

		return call_user_func_array($this->event, $this->args);				
	}
}