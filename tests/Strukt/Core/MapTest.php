<?php

use Strukt\Core\Map;
use Strukt\Core\Collection;

class MapTest extends PHPUnit\Framework\TestCase{

	function setUp(){

		$this->map = new Map(new Collection());
		$this->map->set("session.user.username", "genewilder");
		$this->map->set("session.user.firstname", "Gene");
		$this->map->set("session.user.surname", "Wilder");
		$this->map->set("db.config.username", "root");
		$this->map->set("db.config.password", "_root!");
	}

	function testGetValue(){

		$dbConfig = $this->map->get("db.config");

		$collection = new Collection($dbConfig->getName());
		$collection->set("username", "root");
		$collection->set("password", "_root!");

		$this->assertEquals($dbConfig, $collection);
	}

	function testRemoveValue(){

		$this->map->remove("session.user.username");

		$this->assertFalse($this->map->exists("session.user.username"));
	}

	/**
	* @expectedException Exception
	* @expectedExceptionMessage ValueOnValueException
	*/
	function testExpectValueOnValueException(){

		$this->map->set("db", "_Gene_");
	}
}