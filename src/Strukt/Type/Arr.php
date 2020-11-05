<?php

namespace Strukt\Type;

use Strukt\Contract\ValueObject as ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Raise;
use Strukt\Event;

class Arr extends ValueObject{

	public function __construct(array $arr){

		$this->val = $arr;
	}

	public static function create($arr){

		if(!is_array($arr))
			new Raise(sprintf("%s::create requires an array!", static::class));

		return new self($arr);
	}

	public function pop(){

		return array_pop($this->val);
	}

	public function dequeque(){

		return array_shift($this->val);
	}

	public function concat($delimiter){

		return implode($delimiter, $this->val);
	}

	public function has($val){

		return in_array($val, $this->val);
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

	public function valid(){

		return $this->current()->yield();
	}

	public function next(){

		$elem_exists = !!next($this->val);

		return $elem_exists;
	}

	public function last(){

		$last_elem = end($this->val);

		return new ValueObject($last_elem);
	}

	public function each(\Closure $func){

		$each = new Event($func);

		foreach($this->val as $key=>$val)
			$this->val[$key] = $each->apply($key, $val)->exec();

		return $this;
	}

	public function recur(\Closure $func){

		$each = new Event($func);

		foreach($this->val as $key=>$val){

			if(!is_array($val))
				$this->val[$key] = $each->apply($key, $val)->exec();
			else
				$this->val[$key] = self::create($val)->each($func)->yield();
		}

		return $this;
	}

	public function map(array $maps){

		$collection = CollectionBuilder::create()->fromAssoc($this->val);

		foreach($maps as $key=>$name){

			if($collection->exists($name))
				$arr[$key] = $collection->get($name);
		}

		return $arr;
	}

	/**
	* Is array fully associative
	*/
	public static function isMap(array $arr){

		return !empty($arr) && 
				array_keys($arr) !== range(0, count($arr) - 1) && 
				empty(array_filter(array_keys($arr), "is_numeric"));
	}

	public static function level(array $arr){

		$result = array();

		$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($arr));

		foreach ($it as $key => $value){

			if(empty($key) || array_key_exists($key, $result)) 
				$key = rand();
				
			$result[$key] = $value;
		}

		return $result;
	}
}