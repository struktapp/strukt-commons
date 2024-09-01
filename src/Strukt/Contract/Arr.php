<?php

namespace Strukt\Contract;

use Strukt\Contract\ValueObject as ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Event;
use Strukt\Raise;

abstract class Arr extends ValueObject{

	private $stop_at = null;
	private $ignore = false;

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

	public function isOfStr(){

		if(array_sum(array_map('is_string', $this->val)) != count($this->val) || $this->empty())
			return false;

		return true;
	}

	public function isOfNum(){

		if(array_sum(array_map('is_numeric', $this->val)) != count($this->val) || $this->empty())
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

	public function empty(){

		return $this->length() == 0;
	}

	public function length(){

		return count($this->val);
	}

	public function count(){

		return $this->length();
	}

	public function distinct(){

		return arr(array_count_values($this->val));
	}

	public function slice(int $offset, int $length = null){

		if(!is_null($length))
			return arr(array_slice($this->val, $offset, $length));

		return arr(array_slice($this->val, $offset));
	}

	public function only(array $haystack){

		return array_filter($this->val, function($needle) use($haystack){

			return in_array($needle, $haystack);

		}, ARRAY_FILTER_USE_KEY);
	}

	public function reset():void{

		reset($this->val);
	}

	public function first(){

		$this->reset();

		return $this->current();
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

	public function stop(string $which, string $key){

		if(negate($this->ignore))
			$this->ignore = ($which == $key);

		return $this->ignore;
	}

	public function each(\Closure $func){

		foreach($this->val as $key=>$val)
			$this->val[$key] = negate($this->ignore)?Event::create($func->bindTo($this))->apply($key, $val)->exec():$val;

		return new $this($this->val);
	}

	public function filter(\Closure $func = null){

		if(notnull($func))
			return new $this(array_filter($this->val, $func, ARRAY_FILTER_USE_BOTH));

		return new $this(array_filter($this->val));
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

	public function nested(){

		return array_sum(array_map("is_array", $this->val)) == count($this->val);
	}

	public function sort(bool $asc = true, bool $ksort = false){

		if(!$ksort){

			if($asc)
				asort($this->val);

			if(negate($asc))
				arsort($this->val);
		}

		if($ksort){
			
			if($asc)
				ksort($this->val);

			if(negate($asc))
				krsort($this->val);
		}

		return new $this($this->val);
	}

	public function product(){

		return array_product($this->val);
	}


	public function has(mixed $val){

		$vals = array_filter(array_map(function($piece){
		    return serialize($piece);
		}, $this->val));

		$val = serialize($val);

		return in_array($val, $vals);
	}

	public function contains(string $key){

		return array_key_exists($key, $this->val);
	}

	public function values(){

		return new $this(array_values($this->val));
	}

	public function merge(array $arr){

		return new $this(array_merge($this->val, $arr));
	}

	public function uniq(){

		return new $this(array_unique($this->val));
	}

	public function reverse(){

		return new $this(array_reverse($this->val));
	}

	public function order(){

		if(negate(arr($this->val)->nested()))
			raise("Array must be of nested values!");

		return new class($this->val){

			private $by;
			private $val;

			public function __construct(array $val){

				$this->val = $val;
				$this->by = [];
			}

			public function asc(string $column){

				$column = arr($this->val)->column($column);
				$this->by = array_merge($this->by, [$column, SORT_ASC]);
				if(arr($column)->isOfNum())
					$this->by = array_merge($this->by, [SORT_NUMERIC]);

				return $this;
			}

			public function desc(string $column){

				$column = arr($this->val)->column($column);
				$this->by = array_merge($this->by, [$column, SORT_DESC]);
				if(arr($column)->isOfNum())
					$this->by = array_merge($this->by, [SORT_NUMERIC]);

				return $this;
			}

			public function yield(){

				$this->by[] = &$this->val;

				array_multisort(...$this->by);

				return $this->val;
			}
		};
	}
}