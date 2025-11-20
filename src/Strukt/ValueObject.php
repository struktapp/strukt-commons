<?php

namespace Strukt;

abstract class ValueObject{

	protected $value;

	public function __construct($value){

		$this->value = $value;
	}

	public static function create($value){

		return new self($value);
	}

	public function yield(){

		return $this->value;
	}

	public function equals($value){

		return $this->value == $value;
	}

	public function clone(){

		return clone $this;
	}
}