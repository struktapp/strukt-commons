<?php

use Strukt\Core\Today;
use Strukt\Type\DateTime as DateTimeST;

class TokenQueryTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		
	}

	public function testBasicToken(){

		$token = "user:pitsolu|status:active|is_superuser:true";

		$query = new Strukt\Core\TokenQuery($token);

		$this->assertEquals($query->get("user"), "pitsolu");
		$this->assertEquals($query->get("status"), "active");
		$this->assertEquals($query->get("is_superuser"), "true");

		$this->assertEquals($query->has("role"), false);
		$this->assertEquals($query->keys(), [

			"user",
			"status",
			"is_superuser"
		]);

		$query->set("role","admin");
		$token = sprintf("%s|role:admin", $query->token());
		$this->assertEquals($query->yield(), $token);
	}

	public function testComplexToken(){

		$token = "contact:1|is:tenant,landlord,prospect";

		$query = new Strukt\Core\TokenQuery($token);
		
		$this->assertEquals($query->get("is"), [

			"tenant",
			"landlord",
			"prospect"
		]);

		$this->assertEquals($query->yield(), $token);

		$query->set("status", ["active","published"]);

		$this->assertEquals($query->yield(), sprintf("%s|status:active,published", $token));
	}
}