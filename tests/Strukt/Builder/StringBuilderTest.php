<?php

use Strukt\Builder\StringBuilder;

class StringBuilderTest extends PHPUnit\Framework\TestCase{

	public function testBuilder(){

		$s = new StringBuilder("-");
		$s->add("S");
		$s->add("A");
		$s->add("M");

		$this->assertEquals($s, "S-A-M");
	}

	public function testBuilderInstance(){

		$filter = array("name"=>"user_");

		$sql = StringBuilder::getInstance()
			->add("SELECT p FROM Permission p")
			->add(function() use($filter){

				if(in_array("name", array_keys($filter)))
					return "WHERE p.name = :name";
			})
			->add("ORDER BY p.id DESC");

		$this->assertEquals(implode(" ", array(

			"SELECT p FROM Permission p",
			"WHERE p.name = :name",
			"ORDER BY p.id DESC"
			
		)), (string)$sql);
	}
}