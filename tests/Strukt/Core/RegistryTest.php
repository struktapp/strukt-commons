<?php

use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Core\Registry;
use Strukt\Core\Collection;

class RegistryTest extends PHPUnit\Framework\TestCase{

	public function testRegistry(){

		$r = Registry::getSingleton();
		$r->set("user.firstname", "Donald");
		$r->set("user.surname", "Trump");

		$u = $r->get("user");
		$c = CollectionBuilder::create(new Collection($u->getName()))
			->fromAssoc(array(

				"firstname"=>"Donald",
				"surname"=>"Trump"
			));

		$this->assertEquals($r->get("user.firstname"), "Donald");
		$this->assertEquals($c, $u);
	}

	public function testPersistence(){

		$r = \Strukt\Core\Registry::getSingleton();
		
		$this->assertEquals($r->get("user.surname"), "Trump");
	}
}