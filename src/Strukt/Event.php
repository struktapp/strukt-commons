<?php

namespace Strukt;

/**
* Event Executor class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Event{

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
	private $refEvent = null;

	/**
	* List of reflected parameters
	*
	* @var Array
	*/
	private $refParams = null;

	/**
	* Constructor
	*
	* @param $event \Closure
	*/
	public function __construct(\Closure $event){

		$this->event = $event;

		$this->refEvent = new \ReflectionFunction($this->event);

		$this->refParams = $this->refEvent->getParameters();
	}

	/**
	* Static constructor
	*
	* @param $event \Closure
	*/
	public static function create(\Closure $event){

		return new self($event);
	}

	/**
	* Apply arguments to event
	*
	* @param mixed ...
	*
	* @return \Strukt\Event
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
	* @return \Strukt\Event
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
		foreach($this->refParams as $refParam){

			$params[(string)$refParam->getName()] = (string)$refParam->getType();
		}

		return $params;
	}

	public function expects($type){

		foreach($this->refParams as $refParam)
			if($refParam->hasType())
				if($type == $refParam->getType())
					return true;

		return false;
	}

	/**
	* Execute event
	*
	* @return mixed
	*/
	public function exec(){

		if(is_null($this->args))
			return call_user_func($this->event);

		if(!is_null($this->refEvent)){
			
			$isNotAssoc = is_numeric(key($this->args));

			$args=null;
			if(!$isNotAssoc)
				foreach($this->refParams as $refParam)
					$args[] = $this->args[$refParam->getName()];

			if(!is_null($args))
				$this->args = $args;

			return $this->refEvent->invokeArgs($this->args);
		}

		return call_user_func_array($this->event, $this->args);				
	}
}