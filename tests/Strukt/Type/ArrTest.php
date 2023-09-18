<?php

use Strukt\Type\Arr;

class ArrTest extends PHPUnit\Framework\TestCase{

	use \Strukt\Helper\Arr;

	public function setUp():void{

		$this->rawarr = array(

			"othernames" => "Sander Wellington",
			"surname" => "Johnliver",
			"contact" => array(

				"mobile"=>"+254 712 788 999",
				"address"=>array(

					"home"=>"Westminiser, Long Street, 453, Middlearth",
					"office"=>"Dayriyon, Quadtratic Solusis"
				)
			)
		);

		$this->arr = new Arr($this->rawarr);
	}

	public function testHasValue(){

		$this->assertTrue($this->arr->has("Johnliver"));
	}

	public function testItr(){

		$this->assertTrue($this->arr->current()->equals($this->rawarr["othernames"]));
		$this->assertTrue($this->arr->next());
		$this->assertTrue($this->arr->current()->equals($this->rawarr["surname"]));

		$this->arr->last();

		$this->assertFalse($this->arr->next());

		$this->arr->last();

		$this->assertTrue($this->arr->current()->equals($this->rawarr["contact"]));

		$this->arr->reset();

		$this->assertTrue($this->arr->current()->equals($this->rawarr["othernames"]));
		$this->assertEquals($this->arr->key(), "othernames");

		$this->arr->next();
		$this->arr->next();
		$this->arr->next();
		
		$this->assertFalse($this->arr->next());
		$this->assertFalse($this->arr->valid());
	}

	public function testEach(){

		$val = array(

			"firstname"=>"Peter",
			"second_name"=>"Pan",
			"last_name"=>"Joe"
		);

		$arr = Arr::create($val)->each(function($key, $val){

			if($key == "last_name")
				$val = "Dennis";

			return $val;
		});

		$this->assertTrue($arr->last()->equals("Dennis"));
	}

	public function testRecurItr(){

		$arr = Arr::create($this->rawarr)->recur(function($key, $val){

			if($key == "mobile")
				$val = "N/A";

			return $val;
		});

		$newarr = $arr->yield();

		$this->assertEquals($newarr["contact"]["mobile"], "N/A");
	}

	public function testEmpty(){

		$this->assertFalse($this->arr->empty());
	}

	public function testCount(){

		$this->assertTrue($this->arr->only($this->arr->length()));
	}

	public function testLast(){

		$last = end($this->rawarr);

		$this->assertTrue($this->arr->last()->equals($last));
	}

	public function testFlat(){

		$nested = array(

			array(
				"name" => "pitsolu"
			),
			array(
				array(
					"phone" => "0800-PITSOLU"
				)
			),
			array(
				array(
					array(
						"email" => "pitsolu@gmail.com"
					)
				)
			)
		);

		$flattened = array(

			"name" => "pitsolu",
			"phone" => "0800-PITSOLU",
			"email" => "pitsolu@gmail.com"
		);

		$this->assertEquals($flattened, Arr::level($nested));
	}

	public function testMap(){

		$arr = $this->arr->map(array(

			"contact_mobile"=>"contact.mobile",
			"contact_address_home"=>"contact.address.home"
		));

		$this->assertEquals(array(

			"contact_mobile"=>$this->rawarr["contact"]["mobile"],
			"contact_address_home"=>$this->rawarr["contact"]["address"]["home"]

		), $arr);
	}

	public function testIsAssociative(){

		$this->assertTrue($this->isMap(["firstname"=>"Ludivar", "lastname"=>"Drascos"]));
		$this->assertFalse($this->isMap(["firstname"=>"Peter", "lastname"=>"Parker", 22]));
		$this->assertFalse($this->isMap([1, 2, 3]));
	}

	public function testColumn(){

		$users = array(

			array(
				"username"=>"pitsolu",
				"type"=>"admin"
			),
			array(
				"username"=>"peterparker",
				"type"=>"user"
			),
			array(
				"username"=>"ludivar",
				"type"=>"user"
			)
		);

		$usernames = Arr::create($users)->column("username");

		foreach($users as $user)
			$this->assertTrue(in_array($user["username"], $usernames));
	}

	public function testEnqueue(){

		$this->arr->enqueue("wellsander", "username");//Key is optional

		$username = $this->arr->last()->yield();

		$this->assertEquals($username, "wellsander");
	}

	public function testPrequeue(){

		$this->arr->prequeue("administrator", "type");//Key is optional

		$this->arr->reset();
		$type = $this->arr->current()->yield();

		$this->assertEquals($type, "administrator");
	}

	public function testDequeue(){

		$othernames = $this->arr->dequeue();

		$this->assertEquals($othernames, "Sander Wellington");
		$this->assertFalse($this->arr->has("othernames"));
	}

	public function testPop(){

		$contacts = $this->arr->pop();
		$this->assertTrue(array_key_exists("mobile", $contacts));
		$this->assertFalse($this->arr->has("contacts"));
	}

	public function testPush(){

		$this->arr->push("Active", "status");//Key is optional
		$status = $this->arr->last()->yield();
		$this->assertEquals($status, "Active");
	}
}