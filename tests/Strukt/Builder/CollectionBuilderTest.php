<?php

class CollectionBuilderTest extends PHPUnit_Framework_TestCase{

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

		$x = Strukt\Builder\CollectionBuilder::getInstance()->fromAssoc($s);

		$c = new \Strukt\Core\Collection("config");
		$c->set("username", "root");
		$c->set("password", "_root!");

		$this->assertEquals($x->get("user.db.config"), $c);
		$this->assertEquals($x->get("user.mobile_numbers"), array(

			"777111222",
			"770234567"
		));
	}
}