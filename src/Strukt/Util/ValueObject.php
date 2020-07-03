<?php

namespace Strukt\Util;

class ValueObject{

	private $val;

	public function __construct($val){

		$this->val = $val;
	}

	public static function create($val){

		return new self($val);
	}

	public function get(){

		return $this->val;
	}

	public function equals($val){

		return $this->val == $val;
	}
}