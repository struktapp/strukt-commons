<?php

namespace Strukt\Util;

class Arr{

	private $arr;

	public function __construct(array $arr){

		$this->arr = $arr;
	}

	public function getNew(array $arr){

		return new self($arr);
	}

	public function empty(){

		return $this->only(0);
	}

	public function only(int $num){

		return count($this->arr) == $num;
	}

	public function last(){

		$last_elem = end($this->arr);

		return $last_elem;
	}

	public function map(array $maps){

		$builder = \Strukt\Builder\CollectionBuilder::getInstance();
		$collection = $builder->fromAssoc($this->arr);

		foreach($maps as $key=>$name){

			if($collection->exists($name))
				$arr[$key] = $collection->get($name);
		}

		return $arr;
	}

	public static function flat($arr){

		$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));

		foreach ($it as $key => $value){

			if(empty($key)) 
				$key = rand();
				
			$result[$key] = $value;
		}

		return $result;
	}

	public function yield(){

		return $this->arr; 
	}
}