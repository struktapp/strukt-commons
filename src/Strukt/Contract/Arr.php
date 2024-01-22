<?php

namespace Strukt\Contract;

use Strukt\Contract\ValueObject as ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Event;
use Strukt\Raise;

abstract class Arr extends ValueObject{

	use \Strukt\Helper\Arr{

		isMap as protected _isMap;
	}

	/**
	* Append element to array
	*/
	public function push($item, string $key = null){

		if(is_null($key))
			array_push($this->val, $item);

		if(!is_null($key))
			$this->val[$key] = $item;

		$this->last();

		return $this;
	}

	/**
	* Remove element at end of array
	*/
	public function pop(){

		return array_pop($this->val);
	}

	/**
	* Remove element at beginning of array
	*/
	public function dequeue(){

		return array_shift($this->val);
	}

	/**
	* Arr.push alias
	*/
	public function enqueue($element, $key = null){

		return $this->push($element, $key);
	}

	/**
	* Arr.push batch
	*/
	public function enqueueBatch($element){

		return array_push($this->val, ...$element);
	}

	/**
	* Add element at beginning of array. Allows adding by key
	*/
	public function prequeue($element, $key = null){

		if(!is_null($key))
			$this->val = array_merge(array($key=>$element), $this->val);

		if(is_null($key))
			array_unshift($this->val, $element);

		$this->reset();

		return $this;
	}

	public function column(string $key){

		$column = array_column($this->val, $key);

		return $column;
	}

	public function concat($delimiter){

		if(!empty(array_filter($this->val, "is_object")))
			new Raise("Array items must be at least alphanumeric!");

		return implode($delimiter, $this->val);
	}

	/**
	* Is array fully assosicative
	* 
	*/
	public function isMap(){

		return $this->_isMap($this->val);		
	}

	public function isStr(){

		if(array_sum(array_map('is_string', $this->val)) != count($this->val) || $this->empty())
			return false;

		return true;
	}

	public function tokenize(array $keys = null){

		if(!$this->isMap() || !empty(array_filter($this->val, "is_object")))
			new Raise("Array [Values & Keys] must be at least alphanumeric!");

		if(is_null($keys))
			$keys = array_keys($this->val);

		$token = [];
		foreach($this->val as $key=>$val)
			if(in_array($key, $keys))
				$token[] = sprintf("%s:%s", $key, is_array($val)?implode(",", $val):$val);

		return implode("|", $token);
	}

	public function has($val){

		return in_array($val, $this->val);
	}

	public function empty(){

		return $this->length() == 0;
	}

	public function length(){

		return count($this->val);
	}

	public function only(array $haystack){

		return array_filter($this->val, function($needle) use($haystack){

			return in_array($needle, $haystack);

		}, ARRAY_FILTER_USE_KEY);
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

	public function remove($key){

		if(!is_callable($key))
			unset($this->val[$key]);

		if(is_callable($key)){

			$func = $key->bindTo($this);

			$each = new Event($func);
			foreach($this->val as $key=>$val)
				if($each->apply($key, $val)->exec())
					unset($this->val[$key]);
		}

		return $this;
	}

	public function each(\Closure $func){

		$each = new Event($func->bindTo($this));

		foreach($this->val as $key=>$val)
			$this->val[$key] = $each->apply($key, $val)->exec();

		return $this;
	}

	public function recur(\Closure $func){

		$each = new Event($func->bindTo($this));

		foreach($this->val as $key=>$val){

			if(is_array($val))
				$val = \Strukt\Type\Arr::create($val)->each($func)->yield();

			$this->val[$key] = $each->apply($key, $val)->exec();
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
}