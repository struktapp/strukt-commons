<?php

class EventTest extends PHPUnit\Framework\TestCase{

	public function testExec(){

		$sHelloWorld = "Hello World";

		$helloWorld = new Strukt\Event\Event(function() use($sHelloWorld){

			return $sHelloWorld;
		});

		$this->assertEquals($sHelloWorld, $helloWorld->exec());
	}

	public function testParamType(){

		$forEach = new Strukt\Event\Event(function(Array $list){

			//
		});

		$person = new Strukt\Event\Event(function(int $id, string $name){

			//
		});

		$this->assertTrue($forEach->expects("array"));
		$this->assertTrue($person->expects("int"));
		$this->assertTrue($person->expects("string"));
	}

	public function testApplyInput(){

		$credentials = array("admin", "p@55w0rd");

		$isLoginSuccess = new Strukt\Event\Event(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		list($username, $password) = $credentials;

		$this->assertTrue($isLoginSuccess->apply($username, $password)->exec());
	}

	public function testApplyArgsSequentialInput(){

		$credentials = array("admin", "p@55w0rd");

		$isLoginSuccess = new Strukt\Event\Event(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		$this->assertTrue($isLoginSuccess->applyArgs($credentials)->exec());
	}

	public function testApplyArgsAssocInput(){

		$credentials = array("password"=>"p@55w0rd", "username"=>"admin");

		$isLoginSuccess = new Strukt\Event\Event(function($username, $password) use ($credentials){

			return $username == $credentials["username"] && $password == $credentials["password"];
		});

		$this->assertTrue($isLoginSuccess->applyArgs($credentials)->exec());
	}

	public function testReflectedMethodClosure(){

		$r = new ReflectionClass(Fixture\Person::class);
		$m = $r->getMethod("getId");
		$c = $m->getClosure($r->newInstance());

		$this->assertTrue(is_object(new Strukt\Event\Event($c)));
	}
}