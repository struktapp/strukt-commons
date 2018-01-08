<?php

class ExecutorTest extends PHPUnit_Framework_TestCase{

	public function testExec(){

		$sHelloWorld = "Hello World";

		$helloWorld = new Strukt\Event\Executor(function() use($sHelloWorld){

			return $sHelloWorld;
		});

		$this->assertEquals($sHelloWorld, $helloWorld->exec());
	}

	public function testParamType(){

		$forEach = new Strukt\Event\Executor(function(Array $list){

			//
		});

		$person = new Strukt\Event\Executor(function(int $id, string $name){


		});

		$this->assertTrue($forEach->expects("array"));
		$this->assertTrue($person->expects("int"));
		$this->assertTrue($person->expects("string"));

		// $params = $forEach->getParams();

		// foreach($person->getParams() as $param)
			// print_r("\n".$param->getType()."\n");

		// print_r($params
		// print_r($params[0]->getClass()->getName());
		// print_r($params[0]->getType());
	}

	public function testApplyInput(){

		$credentials = array("admin", "p@55w0rd");

		$isLoginSuccess = new Strukt\Event\Executor(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		list($username, $password) = $credentials;

		$this->assertTrue($isLoginSuccess->apply($username, $password)->exec());
	}

	public function testApplyArgsSequentialInput(){

		$credentials = array("admin", "p@55w0rd");

		$isLoginSuccess = new Strukt\Event\Executor(function($username, $password) use ($credentials){

			return $username == reset($credentials) && $password == end($credentials);
		});

		$this->assertTrue($isLoginSuccess->applyArgs($credentials)->exec());
	}

	public function testApplyArgsAssocInput(){

		$credentials = array("password"=>"p@55w0rd", "username"=>"admin");

		$isLoginSuccess = new Strukt\Event\Executor(function($username, $password) use ($credentials){

			return $username == $credentials["username"] && $password == $credentials["password"];
		});

		$this->assertTrue($isLoginSuccess->applyArgs($credentials)->exec());
	}

	public function testReflectedMethodClosure(){

		$r = new ReflectionClass(Fixture\Person::class);
		$m = $r->getMethod("getId");
		$c = $m->getClosure($r->newInstance());

		// var_dump($m);
		// var_dump($c);

		$this->assertTrue(is_object(new Strukt\Event\Executor($c)));
	}
}