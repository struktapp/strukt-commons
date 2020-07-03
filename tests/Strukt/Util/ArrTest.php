<?php

class ArrTest extends PHPUnit\Framework\TestCase{

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

		$this->arr = new \Strukt\Util\Arr($this->rawarr);
	}

	public function testItr(){

		$this->assertTrue($this->arr->current()->equals($this->rawarr["othernames"]));
		$this->arr->next();
		$this->assertTrue($this->arr->current()->equals($this->rawarr["surname"]));
		$this->arr->last();
		$this->assertTrue($this->arr->current()->equals($this->rawarr["contact"]));
		$this->arr->reset();
		$this->assertTrue($this->arr->current()->equals($this->rawarr["othernames"]));
		$this->assertEquals($this->arr->key(), "othernames");
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

		$this->assertEquals($flattened, \Strukt\Util\Arr::flat($nested));
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
}