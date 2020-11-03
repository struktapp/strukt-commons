<?php

namespace Strukt\Type;

class Number extends \Strukt\Contract\ValueObject{

	public function __construct($number = 0){

		if(!is_numeric($number))
			throw new \Exception(sprintf("Must use numeral, %s given!", gettype($number)));
			
		$this->val = $number;
	}

	public static function create($number = 0){

		return new self($number);
	}

	public function reset(){

		$this->val = 0;
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
	
		return new Number($this->val + $number);
	}

	public function subtract($number){

		$number = Number::objectify($number);

		$number = $number->negate();

		return $this->add($number);
	}

	public function times($number){

		$number = Number::deject($number);

		return new Number($number*$this->val);
	}

	public function parts($number){

		$number = Number::deject($number);

		return new Number($this->val/$number);
	}

	public function mod($number){

		$number = Number::deject($number);

		return new Number($this->val%$number);
	}

	public function raise($number){

		$number = Number::deject($number);

		return new Number(pow($this->val, $number));
	}

	public function ratio(){

		$dividend = array_sum(func_get_args());

		$divisor = $this->parts($dividend);

		$parts = [];

		foreach (func_get_args() as $ratio){

			$ratio = new Number($ratio);

			$parts[] =  $ratio->times($divisor)->yield();
		}

		return $parts; 
	}

	public function negate(){

		return new Number(-1*$this->val);
	}

	public function equals($number){

		$number = Number::deject($number);

		return $this->val == $number;
	}

	public function gt($number){

		$number = Number::deject($number);

		return $this->val > $number;
	}

	public function lt($number){

		$number = Number::deject($number);

		return $this->val < $number;
	}

	public function lte($number){

		return $this->lt($number) || $this->equals($number);
	}

	public function gte($number){

		return $this->gt($number) || $this->equals($number);
	}

	public function type(){

		return gettype($this->val);
	}

	public function yield(){

		if(!is_numeric($this->val))
			new \Strukt\Raise("NaN");

		return $this->val;
	}

	public function __toString(){

		return (string) $this->val;
	}

	public static function random($qty, $min=null, $max=null){

		$i=0;
		while($i<=$qty-1){

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