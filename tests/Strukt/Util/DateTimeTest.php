<?php

class DateTimeTest extends PHPUnit\Framework\TestCase{

	public function setUp(){

		$this->start = new \Strukt\Util\DateTime();
		$this->end = new \Strukt\Util\DateTime("+30 days");
	}

	public function testRandDateIsInBtwn(){

		$strStart = $this->start->format("Y-m-d H:i:s");

		$randDate = new \Strukt\Util\DateTime($strStart);

		$this->assertEquals($randDate->format("Y-m-d H:i:s"), $strStart);

		$randDate->toMakeRand($this->end);

		$this->assertTrue($randDate->gte($this->start) && $randDate->lte($this->end));
	}

	public function testClone(){

		$newStartA = $this->start->clone();

		$this->assertTrue($this->start->equals($newStartA));

		$newStartB = $this->start->clone("+1 day");

		$this->assertEquals($newStartB, $this->start->modify("+1 day"));
	}

	public function testInequalities(){

		$newStart = $this->start->clone("+10 days");

		$this->assertTrue($this->start->lt($newStart) && $this->end->gt($newStart));
	}

	public function testResetTime(){

		$startDate = $this->start->clone();

		$this->start->beginDay();

		$this->assertTrue($this->start->lt($startDate));

		$endDate = $this->end->clone();

		$this->end->endDay();

		$this->assertTrue($this->end->gt($endDate));
	}
}