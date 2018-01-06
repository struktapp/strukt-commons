<?php

namespace Strukt\Event;

/**
* Single class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Single{

	/**
	* Event arguments
	*
	* @var Array
	*/
	private $args = null;

	/**
	* Event
	*
	* @var \Closure
	*/
	private $event = null;

	/**
	* Constructor
	*
	* @param $event \Closure
	*/
	public function __construct(\Closure $event){

		// $this->event = \Closure::bind($event, $this);

		$this->event = $event;
	}

	/**
	* Static constructor
	*
	* @param $event \Closure
	*
	* @return \Strukt\Event\Single
	*/
	public function newEvent(\Closure $event){

		return new self($event);
	}

	/**
	* Get event executor
	*
	* @return Strukt\Event\Executor
	*/
	public function getEvent(){

		// print_r(get_class($this->event));exit;

		return new Executor($this->event);
	}

	/**
	* Get event as closure
	*
	* @return \Closure
	*/
	public function getClosure(){

		return $this->event;
	}

	/**
	* Wrapper for exec method
	*
	* @param mixed ...
	*
	* @return mixed
	*/
	public function exec(){

		$args = func_get_args();

		if(!empty($args))
			return $this->getEvent()->applyArgs($args)->exec();

		return $this->getEvent()->exec();
	}
}