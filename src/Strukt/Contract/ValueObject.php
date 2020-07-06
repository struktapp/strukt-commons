<?php

namespace Strukt\Contract;

class ValueObject{

	protected $val;

	public function __construct($val){

		$this->val = $val;
	}

	public static function create($val){

		return new self($val);
	}

	public function yield(){

		return $this->val;
	}

	public function equals($val){

		return $this->val == $val;
	}
}