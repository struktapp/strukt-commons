<?php

use Strukt\Env;

class EnvTest extends PHPUnit\Framework\TestCase{

	public function testFromFile(){

		Env::withFile("fixture/.env");
		$this->assertFalse(Env::get("allow_admin"));
		$this->assertEquals("p@55w0rd", Env::get("password"));
	}

	public function testForComment(){

		$this->assertFalse(Env::has("allow_ssl"));
	}

	public function testEnvGetSet(){

		$framework = "Strukt";

		Env::set("framework", $framework);

		$this->assertEquals($framework, Env::get("framework"));
	}
}