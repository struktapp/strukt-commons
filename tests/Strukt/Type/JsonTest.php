<?php

use Strukt\Type\Json;

class JsonTest extends PHPUnit\Framework\TestCase{

	protected $encCred;
	protected $decCred;

	public function setUp():void{

		$this->encCred = sprintf('{"username":"adm","password":"%s"}', sha1("p@55w0rd"));

		$this->decCred = array(

			"username"=>"adm",
			"password"=>sha1("p@55w0rd")
		);
	}

	public function testEncode(){

		$encCred = Json::encode($this->decCred);

		$this->assertEquals($encCred, $this->encCred);
	}

	public function testDecode(){

		$decCred = Json::decode($this->encCred);

		$this->assertEquals($decCred, $this->decCred);
	}
}