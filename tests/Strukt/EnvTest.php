<?php

use Strukt\Env;

class EnvTest extends PHPUnit\Framework\TestCase{

	public function testEnvGetSet(){

		$framework = "Strukt";

		Env::set("framework", $framework);

		$this->assertEquals($framework, Env::get("framework"));
	}
}