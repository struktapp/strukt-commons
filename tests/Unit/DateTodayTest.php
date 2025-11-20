<?php

use Strukt\Today;

beforeEach(function(){

	Today::makePeriod(new DateTime("1900-01-01"), new DateTime("1963-12-31"));
	Today::reset(new DateTime("1960-03-23"));

	$this->ftoday = new Today();

});//->skip();

test("today[is_fake]", function(){

	//fake today
	$ftoday = $this->ftoday->format("Y-m-d");

	//fake now
	$fnow = format("date", new Strukt\DateTime());
	$now = format("date", new DateTime());

	expect($ftoday)->toBe($fnow);
	expect($ftoday)->not->toBe($now);
});

test("today[is_in_period]", function(){

	expect(Today::hasPeriod())->toBeTrue();

	$past = new DateTime("1959-04-01");
	expect($this->ftoday->withDate($past)->isValid())->toBeTrue();

	$now = new DateTime();
	expect($this->ftoday->withDate($now)->isValid())->toBeFalse();

})->after(function(){

	Today::reset();	
});
