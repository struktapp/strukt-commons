<?php

use Strukt\Util\Json;

class JsonTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->encCred = '{"username":"adm","password":"ce0b2b771f7d468c0141918daea704e0e5ad45db"}';

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