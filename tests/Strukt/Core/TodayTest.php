<?php

use Strukt\Core\Today;
use Strukt\Type\DateTime as XDateTime;

class TodayTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		Today::makePeriod(new DateTime("1900-01-01"), new DateTime("1963-12-31"));
		Today::reset(new DateTime("1960-03-23"));

		$this->today = new Today();
	}

	public function testIsAnyDateTheDefault(){

		$today = $this->today->format("Y-m-d");
		$xNow = new XDateTime();
		$xNow = $xNow->format("Y-m-d");
		$now = new DateTime();
		$now = $now->format("Y-m-d");

		$this->assertEquals($today, $xNow);
		$this->assertNotEquals($today, $now);
	}

	public function testIsBtwnPeriod(){

		$this->assertTrue(Today::hasPeriod());

		$past = new DateTime("1959-04-01");
		$this->assertTrue($this->today->withDate($past)->isValid());

		$now = new DateTime();
		$this->assertFalse($this->today->withDate($now)->isValid());
	}

	public function tearDown():void{

		Today::reset();		
	}
}