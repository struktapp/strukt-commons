<?php

use Strukt\Type\Str;

class StrTest extends PHPUnit\Framework\TestCase{

	public function setUp():void{

		$this->str = new Str("Strukt Framework");
		$this->blah = new Str("Blah blah blah!");
		$this->markup = new Str("{bold}Black Beauty{/bold}");
		$this->camel = new Str("thisIsCamelCase");
	}

	public function testSlicers(){

		$this->assertTrue($this->str->startsWith("Strukt"));
		$this->assertTrue($this->str->endsWith("Framework"));

		$this->assertEquals((string)$this->str->first(3), "Str");
		$this->assertEquals((string)$this->str->last(4), "work");

		$this->assertTrue($this->str->contains("Frame"));
		$this->assertTrue($this->str->slice(7,5)->equals("Frame"));
		$this->assertTrue($this->str->notEquals("Sanjay"));
	}

	public function testReplace(){

		$this->assertTrue($this->str->replace("work", "play")->equals("Strukt Frameplay"));
		$this->assertEquals($this->blah->replaceFirst("blah", "yaba daba"), "Blah yaba daba blah!");
		$this->assertEquals($this->blah->replaceLast("blah", "doo"), "Blah blah doo!");
		$this->assertEquals($this->str->replaceAt("ing", 3, 3), "String Framework");
	}

	public function testBetween(){

		$this->assertTrue($this->markup->btwn("{bold}", "{/bold}")->equals("Black Beauty"));
	}

	public function testCase(){

		$this->assertTrue($this->str->toUpper()->equals("STRUKT FRAMEWORK"));
		$this->assertTrue($this->str->toLower()->equals("strukt framework"));
		$this->assertTrue($this->camel->toSnake()->equals("this_is_camel_case"));
		$this->assertTrue($this->camel->toSnake()->toCamel()->equals("ThisIsCamelCase"));
	}

	public function testAt(){

		$this->assertEquals($this->str->at("F"), strpos($this->str, "F"));
	}

	public function testPrepend(){

		$this->assertTrue($this->str->prepend("-- ")->equals("-- Strukt Framework"));
	}

	public function testAppend(){

		$this->assertTrue($this->str->concat(" Dev Master")->equals("Strukt Framework Dev Master"));
	}

	public function testSplit(){

		$this->assertEquals($this->str->split(" "), array("Strukt","Framework"));
	}

	public function testCount(){

		$this->assertEquals($this->blah->count("blah"), 2);
	}
}