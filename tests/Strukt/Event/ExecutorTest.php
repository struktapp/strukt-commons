<?php

class ExecutorTest extends PHPUnit_Framework_TestCase{

	public function testExec(){

		$sHelloWorld = "Hello World";

		$helloWorld = new Strukt\Event\Executor(function() use($sHelloWorld){

			return $sHelloWorld;
		});

		$this->assertEquals($sHelloWorld, $helloWorld->exec());
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
}