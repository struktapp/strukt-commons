<?php

$start = when();
$end = when("+30 days");

test("when[random_date_is_in_btwn]", function() use($start, $end){

	$rand = $start->rand($end);
	expect($rand->gte($start) && $rand->lte($end))->toBeTrue();
});

test("when[clone]", function() use($start){

	$clone = $start->clone();
	expect($start->equals($clone))->toBeTrue();

	$clone = $start->clone("+1 day");
	$same = $start->modify("+1 day");
	expect($clone == $same)->toBeTrue();
});

test("when[inequalities]", function() use($start, $end){

	$clone = $start->clone("+10 days");
	expect($start->lt($clone) && $end->gt($clone))->toBeTrue();
});

test("when[reset_time]", function() use($start, $end){

	$clone = $start->clone();
	$start->reset();
	expect($start->lt($clone))->toBeTrue();

	$endClone = $end->clone();
	$end->last();
	expect($end->gt($endClone))->toBeTrue();
});

test("when.btwn", function(){

	$date = when("1998-12-25");
	expect($date->btwn(new \DateTime("1998-01-01"), new \DateTime("1999-01-01")))->toBeTrue();
	expect($date->btwn(new \DateTime("1998-12-31"), new \DateTime("1999-01-01")))->toBeFalse();
})->skip();
