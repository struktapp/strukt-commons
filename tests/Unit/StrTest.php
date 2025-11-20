<?php

$str = str("Strukt Framework");
$blah = str("Blah blah blah!");
$markup = str("{bold}Black Beauty{/bold}");
$camel = str("thisIsCamelCase");

test("str.slice", function() use($str){

	expect($str->startsWith("Strukt"))->toBeTrue();
	expect($str->endsWith("Framework"))->toBeTrue();
	expect((string)$str->first(3))->toBe("Str");
	expect((string)$str->last(4))->toBe("work");
	expect($str->contains("Frame"))->toBeTrue();
	expect($str->slice(7,5)->equals("Frame"))->toBeTrue();
	expect($str->notEquals("Sanjay"))->toBeTrue();
});

test("str.replace", function() use($str, $blah){

	expect($str->replace("work", "play")->equals("Strukt Frameplay"))->toBeTrue();
	expect((string)$blah->replaceFirst("blah", "yaba daba"))->toBe("Blah yaba daba blah!");
	expect((string)$blah->replaceLast("blah", "doo"))->toBe("Blah blah doo!");
	expect((string)$str->replaceAt("ing", 3, 3))->toBe("String Framework");
});

test("str.between", function() use($markup){

	expect($markup->btwn("{bold}", "{/bold}")->equals("Black Beauty"))->toBeTrue();
});

test("str[case]", function() use($str, $camel){

	expect($str->toUpper()->equals("STRUKT FRAMEWORK"))->toBeTrue();
	expect($str->toLower()->equals("strukt framework"))->toBeTrue();
	expect($camel->toSnake()->equals("this_is_camel_case"))->toBeTrue();
	expect($camel->toSnake()->toCamel()->equals("ThisIsCamelCase"))->toBeTrue();
});

test("str.at", function() use($str){

	expect($str->at("F"))->toBe(strpos($str, "F"));
});

test("str.prepend", function() use($str){

	expect($str->prepend("-- ")->equals("-- Strukt Framework"))->toBeTrue();
});

test("str.append", function() use($str){

	expect($str->concat(" Dev Master")->equals("Strukt Framework Dev Master"))->toBeTrue();
});

test("str.split", function() use($str){

	expect($str->split(" "))->toBe(array("Strukt","Framework"));
});

test("str.count", function() use($blah){

	expect($blah->count("blah"))->toBe(2);
});

test("str.empty", function(){

	expect(str(" ")->empty())->toBe(true);
	expect(str("")->empty())->toBe(true);
	expect(str("1")->empty())->toBe(false);
});
