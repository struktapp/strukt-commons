<?php

use Strukt\Core\Today;
use Strukt\Type\DateTime as DateTimeST;

class TodayTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		Today::validBtwn(new DateTime("1900-01-01"), new DateTime("1963-12-31"));
		Today::reset(new DateTime("1960-03-23"));

		$this->today = new Today();
	}

	public function testIsAnyDateTheDefault(){

		$today = $this->today->format("Y-m-d");
		$now_st = new DateTimeST();
		$now_st = $now_st->format("Y-m-d");
		$now_php = new DateTime();
		$now_php = $now_php->format("Y-m-d");

		$this->assertEquals($today, $now_st);
		$this->assertNotEquals($today, $now_php);
	}

	public function testIsBtwnPeriod(){

		$this->assertTrue(Today::hasRange());
		$this->assertTrue($this->today->withDate(new DateTime("1959-04-01"))->useRange()->isValid());
		$this->assertFalse($this->today->withDate(new DateTime())->useRange()->isValid());
	}

	public function tearDown():void{

		Today::reset();		
	}
}