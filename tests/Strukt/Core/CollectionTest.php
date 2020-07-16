<?php

use Strukt\Core\Collection;
use Strukt\Exception\KeyOverlapException;
use Strukt\Exception\KeyNotFoundException;

class CollectionTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->collection = new Collection("user");
		$this->collection->set("firstname", "Gene");
		$this->collection->set("surname", "Wilder");
		$this->collection->set("username", "genewilder");
	}

	function testGetValue(){

		$this->assertEquals($this->collection->get("firstname"), "Gene");
	}

	function testGetNestedValue(){

		$collection = new Collection("contacts");
		$collection->set("mobile", "+2540770123456");
		$collection->set("work-phone", "+2540202345678");

		$this->collection->set("contacts", $collection);

		$this->assertEquals($this->collection->get("contacts.mobile"), $collection->get("mobile"));
	}

	function testRemoveValue(){

		$this->collection->remove("surname");
		
		$this->assertFalse($this->collection->exists("surname"));
	}

	function testExpectValueOnValueException(){

		$this->expectException(KeyOverlapException::class);

		$this->collection->set("firstname", "_Gene_");
	}

	function testExpectNonExistentKeyException(){

		$this->expectException(KeyNotFoundException::class);

		$this->collection->get("middlename");
	}
}