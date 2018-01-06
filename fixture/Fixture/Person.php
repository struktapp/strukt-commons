<?php

namespace Fixture;

class Person{

	private $id;

	public function __construct(){

		$this->id = random_int(1, 10000);
	}

	public function getId(){

		return $this->id;
	}
}