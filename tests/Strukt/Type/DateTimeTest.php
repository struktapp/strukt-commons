<?php

class DateTimeTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->start = new \Strukt\Type\DateTime();
		$this->end = new \Strukt\Type\DateTime("+30 days");
	}

	public function testRandDateIsInBtwn(){

		$rand = $this->start->rand($this->end);

		$this->assertTrue($rand->gte($this->start) && $rand->lte($this->end));
	}

	public function testClone(){

		$clone = $this->start->clone();

		$this->assertTrue($this->start->equals($clone));

		$clonePlusOneDay = $this->start->clone("+1 day");

		$this->assertEquals($clonePlusOneDay, $this->start->modify("+1 day"));
	}

	public function testInequalities(){

		$clonePlusTenDays = $this->start->clone("+10 days");

		$this->assertTrue($this->start->lt($clonePlusTenDays) 
							&& $this->end->gt($clonePlusTenDays));
	}

	public function testResetTime(){

		$startClone = $this->start->clone();

		$this->start->reset();

		$this->assertTrue($this->start->lt($startClone));

		$endClone = $this->end->clone();

		$this->end->last();

		$this->assertTrue($this->end->gt($endClone));
	}
}