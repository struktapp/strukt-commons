<?php

beforeEach(function(){

	$this->start = when("1900-01-01");
	$this->end = when("1963-12-31");
	$this->fake = when("1960-03-23");

	$period = period($this->start, $this->end);
	$period->reset($this->fake); //create fake today
	$this->ftoday = today();

});//->skip();

test("period[fake]", function(){

	expect($this->ftoday->same(new \DateTime))->toBeFalse();
	expect($this->ftoday->same(when()))->toBeTrue();
	expect($this->ftoday->hasPeriod())->toBeTrue();
	expect($this->ftoday->withDate(when("1959-04-01"))->isValid())->toBeTrue();
})->skip();

test("period[state]", function(){

	expect($this->ftoday->getState("period.start") == $this->start)->toBeTrue();
	expect($this->ftoday->getState("period.end") == $this->end)->toBeTrue();
	$stoday = $this->ftoday->format("Y-m-d");
	$sfake = $this->fake->format("Y-m-d");
	expect($stoday == $sfake)->toBeTrue();
	$this->ftoday->reset();	
	expect($stoday)->not->toBe(today()->format("Y-m-d"));
});//->skip();