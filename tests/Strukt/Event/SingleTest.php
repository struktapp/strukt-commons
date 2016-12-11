<?php

class SingleTest extends PHPUnit_Framework_TestCase{

	public function testExec(){

		$sHelloWorld = "Hello World";

		$helloWorld = new Strukt\Event\Single(function() use($sHelloWorld){

			return $sHelloWorld;
		});

		$this->assertEquals($sHelloWorld, $helloWorld->exec());
	}

	public function testExecWithArgs(){

		$hello = new Strukt\Event\Single(function($name){

			return sprintf("Hello %s!", $name);
		});

		$this->assertEquals("Hello Gene!", $hello->exec("Gene"));
	}

	public function testApplyArgsAssocInput(){

		$credentials = array("password"=>"p@55w0rd", "username"=>"admin");

		$isLoginSuccess = new Strukt\Event\Single(function($username, $password) use ($credentials){

			return $username == $credentials["username"] && $password == $credentials["password"];
		});

		$this->assertTrue($isLoginSuccess->getEvent()->applyArgs($credentials)->exec());
	}

	public function testRecursion(){

		$entity = array(

			"username"=>"admin",
			"password"=>"p@55w0rd",
			"supervisor"=>array(

				"username"=>"sup",
				"password"=>"5up31v!50r"
			)
		);

		$newVal = \Strukt\Event\Single::newEvent(function($entity){

			foreach($entity as $key=>$val){

				if(is_string($key))
					if($key == "password")
						$entity[$key] = sha1($val);

				if(is_array($val))
					$entity[$key] = $this->getEvent()->apply($val)->exec();
			}
					
			return $entity;

		})->getEvent()->apply($entity)->exec();

		$this->assertEquals(array(

			"username"=>"admin",
			"password"=>sha1("p@55w0rd"),
			"supervisor"=>array(

				"username"=>"sup",
				"password"=>sha1("5up31v!50r")
			)

		), $newVal);
	}
}