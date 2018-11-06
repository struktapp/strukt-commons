<?php

use Strukt\Core\Collection;

class CollectionTest extends PHPUnit\Framework\TestCase{

	function setUp(){

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

	/**
	* @expectedException Exception
	* @expectedExceptionMessage ValueOnValueException
	*/
	function testExpectValueOnValueException(){

		$this->collection->set("firstname", "_Gene_");
	}

	/**
	* @expectedException Exception
	* @expectedExceptionMessage NonExistentKeyException [middlename]!
	*/
	function testExpectNonExistentKeyException(){

		$this->collection->get("middlename");
	}
}