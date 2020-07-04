<?php

namespace Strukt\Util;

use Strukt\Contract\ValueObject as ValueObject; 

class Arr extends ValueObject{

	private $arr;

	public function __construct(array $arr){

		$this->val = $arr;
	}

	public static function create(array $arr){

		return new self($arr);
	}

	public function empty(){

		return $this->only(0);
	}

	public function length(){

		return count($this->val);
	}

	public function only(int $num){

		return $this->length() == $num;
	}

	public function reset():void{

		reset($this->val);
	}

	public function key(){

		return key($this->val);
	}

	public function current(){

		$curr_elem = current($this->val);

		return new ValueObject($curr_elem);
	}

	public function next(){

		$elem_exists = !!next($this->val);

		return $elem_exists;
	}

	public function last(){

		$last_elem = end($this->val);

		return new ValueObject($last_elem);
	}

	public function map(array $maps){

		$builder = \Strukt\Builder\CollectionBuilder::getInstance();
		$collection = $builder->fromAssoc($this->val);

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
}