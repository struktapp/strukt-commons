<?php

use Strukt\Core\Collection;
use Strukt\Builder\Collection as CollectionBuilder;

class CollectionBuilderTest extends PHPUnit\Framework\TestCase{

	public function testBuilder(){

		$s = array(

			"user"=>array(
					
				"firstname"=>"Gene",
				"surname"=>"Wilder",	
				"db"=>array(

					"config"=>array(

						"username"=>"root",
						"password"=>"_root!"
					)
				),
				"mobile_numbers"=>array(

					"777111222",
					"770234567"
				)
			)
		);

		$x = CollectionBuilder::create()->fromAssoc($s);

		$c = new Collection("config");
		$c->set("username", "root");
		$c->set("password", "_root!");

		$this->assertEquals($x->get("user.db.config"), $c);
		$this->assertEquals($x->get("user.mobile_numbers"), array(

			"777111222",
			"770234567"
		));
	}
}