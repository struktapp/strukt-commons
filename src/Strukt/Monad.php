<?php

namespace Strukt;

use Strukt\Event;

class Monad{

	private $result;

	private function __construct(){

		//
	}

	public static function create(){

		return new self;
	}

	public function next(array $args, \Closure $step){

		if(!empty($this->result)){

			if(is_array($this->result))
				array_unshift($args, ...$this->result);
			else
				array_unshift($args, $this->result);

			unset($this->result);
		}

		$this->result = Event::create($step)->applyArgs($args)->exec();

		return $this;
	}

	public function yield(){

		return $this->result;
	}
}