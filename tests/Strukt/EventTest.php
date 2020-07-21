<?php

use Strukt\Event;

class EventTest extends PHPUnit\Framework\TestCase{

	public function testExec(){

		$adele = "Hello World";

		$helloWorld = Event::create(function() use($adele){

			return $adele;
		});

		$this->assertEquals($adele, $helloWorld->exec());
	}

	public function testParamType(){

		$forEach = Event::create(function(Array $list){

			//
		});

		$person = Event::create(function(int $id, string $name){

			//
		});

		$this->assertTrue($forEach->expects("array"));
		$this->assertTrue($person->expects("int"));
		$this->assertTrue($person->expects("string"));
	}

	public function testApplyInput(){

		$credentials = array("admin", "p@55w0rd");

		$login = Event::create(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		list($username, $password) = $credentials;

		$this->assertTrue($login->apply($username, $password)->exec());
	}

	public function testApplyArgsSequentialInput(){

		$credentials = array("admin", "p@55w0rd");

		$login = Event::create(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		$this->assertTrue($login->applyArgs($credentials)->exec());
	}

	public function testApplyArgsAssocInput(){

		$credentials = array("password"=>"p@55w0rd", "username"=>"admin");

		$login = Event::create(function($username, $password) use ($credentials){

			return $username == $credentials["username"] && $password == $credentials["password"];
		});

		$this->assertTrue($login->applyArgs($credentials)->exec());
	}

	public function testReflectedMethodClosure(){

		$r = new ReflectionClass(Fixture\Person::class);
		$m = $r->getMethod("getId");
		$c = $m->getClosure($r->newInstance());

		$this->assertTrue(is_object(new Event($c)));
	}
}