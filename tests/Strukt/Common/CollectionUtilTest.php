<?php

use Strukt\Core\Collection;

class CollectionUtilTest extends PHPUnit_Framework_TestCase{

	public function testShallowMarshal(){

		$settings = array(

			"pr.Tenant!actionAdd.initialStatus" => "Pending",
			"pr.Tenant!actionAdd.supervisor" => array(

			   "Property Manager"
			),
			"pr.Tenant!status" => array(

			    "Pending",
			    "Active",
			    "Inactive"
			)
		);

		$collection = new \Strukt\Core\Collection();
		foreach($settings as $key=>$val)
			Strukt\Common\Util\Collection::doMarshal($key, $val, $collection);

		$pr = $collection->get("pr");
		$tenantActionAdd = $pr->get("Tenant!actionAdd");

		$this->assertEquals($tenantActionAdd->get("initialStatus"), "Pending");
		$this->assertEquals($tenantActionAdd->get("supervisor"), array(

		   "Property Manager"
		));

		$this->assertEquals($pr->get("Tenant!status"), array(

		   	"Pending",
		    "Active",
		    "Inactive"
		));
	}

	public function testDeepMarshal(){

		$arrObjA = array(

			"types"=>array(

				"Accrual",
				"Cash Basis"
			),
			"default"=>"Accrual",
			"use"=>"Accrual"
		);

		$arrObjB = array(

			"Cash"=>array(

				"Debit"=>"Maintenance Expenses",
				"Credit"=>"Bank"
			),
			"Accrual"=>array(

				"Expectation"=>array(

					"Debit"=>"Maintenance Expenses",
					"Credit"=>"Accounts Payable"
				),
				"Outcome"=>array(

					"Debit"=>"Accounts Payable",
					"Credit"=>"Bank"
				)
			),
			"tests"=>array(

				"test1",
				"test2",
				"test3"
			)
		);

		$col = new \Strukt\Core\Collection();
		Strukt\Common\Util\Collection::doMarshal("ac.Methods", $arrObjA, $col);	
		Strukt\Common\Util\Collection::doMarshal("ac.Tranx.MaintenanceBill", $arrObjB, $col);

		$colA = new Strukt\Core\Collection("Outcome");
		$colA = Strukt\Builder\CollectionBuilder::getInstance($colA)->fromAssoc(array(

			"Debit"=>"Accounts Payable",
			"Credit"=>"Bank"
		));

		$this->assertEquals($col->get("ac.Tranx.MaintenanceBill.Accrual.Outcome"), $colA);

		$this->assertEquals($col->get("ac.Tranx.MaintenanceBill.tests"), array(

			"test1",
			"test2",
			"test3"
		));

		$this->assertEquals($col->get("ac.Methods.types"), array(

			"Accrual",
			"Cash Basis"
		));

		$this->assertEquals($col->get("ac.Methods.default"), "Accrual");
	}
}