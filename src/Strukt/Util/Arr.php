<?php

namespace Strukt\Util;

class Arr{

	private $arr;

	public function __construct(array $arr){

		$this->arr = $arr;
	}

	public static function create(array $arr){

		return new self($arr);
	}

	public function empty(){

		return $this->only(0);
	}

	public function length(){

		return count($this->arr);
	}

	public function only(int $num){

		return $this->length() == $num;
	}

	public function reset():void{

		reset($this->arr);
	}

	public function key(){

		return key($this->arr);
	}

	public function current(){

		$curr_elem = current($this->arr);

		return new ValueObject($curr_elem);
	}

	public function next(){

		$curr_elem = $this->current();

		$next_elem = next($this->arr);

		return $curr_elem != $next_elem;
	}

	public function last(){

		$last_elem = end($this->arr);

		return new ValueObject($last_elem);
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

		$result = array();

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