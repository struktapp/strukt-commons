<?php

namespace Strukt\Util;

class Number{

	private $number;

	public function __construct($number = 0){

		if(!is_numeric($number))
			throw new \Exception(sprintf("Must use numeral, %s given!", gettype($number)));
			
		$this->number = $number;
	}

	private static function objectify($number){

		if(!Number::valid($number))
			$number = new Number($number);

		return $number;
	}

	private static function deject($number){

		if(Number::valid($number))
			$number = $number->yield();

		return $number;
	}

	private static function valid($number){

		return $number instanceof Number;
	}

	public function add($number){

		$number = Number::deject($number);
	
		return new Number($this->number + $number);
	}

	public function subtract($number){

		$number = Number::objectify($number);

		$number = $number->negate();

		return $this->add($number);
	}

	public function multiply($number){

		$number = Number::deject($number);

		return new Number($number*$this->number);
	}

	public function parts($number){

		$number = Number::deject($number);

		return new Number($this->number/$number);
	}

	public function mod($number){

		$number = Number::deject($number);

		return new Number($this->number%$number);
	}

	public function raise($number){

		$number = Number::deject($number);

		return new Number(pow($this->number, $number));
	}

	public function ratio(){

		$dividend = array_sum(func_get_args());

		$divisor = $this->parts($dividend);

		$parts = [];

		foreach (func_get_args() as $ratio){

			$ratio = new Number($ratio);

			$parts[] =  $ratio->multiply($divisor)->yield();
		}

		return $parts; 
	}

	public function negate(){

		return new Number(-1*$this->number);
	}

	public function equals($number){

		$number = Number::deject($number);

		return $this->number == $number;
	}

	public function gt($number){

		$number = Number::deject($number);

		return $this->number > $number;
	}

	public function lt($number){

		$number = Number::deject($number);

		return $this->number < $number;
	}

	public function lte($number){

		return $this->lt($number) || $this->equals($number);
	}

	public function gte($number){

		return $this->gt($number) || $this->equals($number);
	}

	public function type(){

		return gettype($this->number);
	}

	public function yield(){

		return (int)$this->number;
	}

	public function __toString(){

		return (string) $this->number;
	}

	public static function random($qty, $min=null, $max=null){

		$i=0;
		while($i<=$qty){

			if(!is_null($min) && !is_null($max))
				$numbers[] = rand($min, $max);
			else
				$numbers[] = rand();

			if($i==$qty)
				break;
			
			$i++;
		}

		return $numbers;
	}
}