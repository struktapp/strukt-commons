<?php

namespace Strukt\Contract;

use Strukt\Contract\ValueObject as ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Event;
use Strukt\Raise;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class Arr extends ValueObject{

	private $stop_at = null;
	private $skip = [];
	private $jump = [];

	use \Strukt\Traits\Arr{

		isMap as protected is_map;
	}

	/**
	* Append element to array
	* 
	* @param mixed $item
	* @param string $key = null
	* 
	* @return static
	*/
	public function push(mixed $item, ?string $key = null):static{

		if(is_null($key))
			array_push($this->val, $item);

		if(!is_null($key))
			$this->val[$key] = $item;

		$this->last();

		return $this;
	}

	/**
	* Remove element at end of array
	* 
	* @return mixed
	*/
	public function pop():mixed{

		return array_pop($this->val);
	}

	/**
	* Remove element at beginning of array
	* 
	* @return mixed
	*/
	public function dequeue():mixed{

		return array_shift($this->val);
	}

	/**
	* Arr.push alias
	* 
	* @param mixed $element
	* @param mixed $key = null
	* 
	* @return mixed
	*/
	public function enqueue(mixed $element, mixed $key = null):mixed{

		return $this->push($element, $key);
	}

	/**
	 * @deprecated enqueueAll
	 */
	public function enqueueBatch(array $element):mixed{

		return $this->enqueueAll($element);
	}

	/**
	* Arr.push batch
	* 
	* @param array $element
	* 
	* @return mixed
	*/
	public function enqueueAll(array $element):mixed{

		if(!arr($element)->isMap())
			return array_push($this->val, ...$element);

		return array_merge($this->val, $element);
	}

	/**
	* Add element at beginning of array. Allows adding by key
	* 
	* @param mixed $element
	* @param mixed $key = null
	* 
	* @return static
	*/
	public function prequeue(mixed $element, mixed $key = null):static{

		if(!is_null($key))
			$this->val = array_merge(array($key=>$element), $this->val);

		if(is_null($key))
			array_unshift($this->val, $element);

		$this->reset();

		return $this;
	}

	/**
	 * Get column of multidimensional array
	 * 
	 * @param string $key
	 * 
	 * @return array
	 */
	public function column(string $key):array{

		$column = array_column($this->val, $key);

		return $column;
	}

	/**
	 * Concatenate array of strings by delimiter
	 * 
	 * @param string $delimiter
	 * 
	 * @return string
	 */
	public function concat(string $delimiter):string{

		if(!empty(array_filter($this->val, "is_object")))
			new Raise("Array items must be at least alphanumeric!");

		return implode($delimiter, $this->val);
	}

	/**
	* Is array fully assosicative
	* 
	* @deprecated is("map")
	* 
	* @return bool
	*/
	public function isMap():bool{

		return $this->is_map($this->val);		
	}

	/**
	 * Is array of strings
	 * 
	 * @deprecated isof("strings")
	 * 
	 * @return bool
	 */
	public function isOfStr():bool{

		if(array_sum(array_map('is_string', $this->val)) != count($this->val) || $this->empty())
			return false;

		return true;
	}

	/**
	 * Is array of numbers
	 * 
	 * @deprecated isof("numbers")
	 * 
	 * @return bool
	 */
	public function isOfNum():bool{

		if(array_sum(array_map('is_numeric', $this->val)) != count($this->val) || $this->empty())
			return false;

		return true;
	}

	/**
	 * @param string $type
	 * 
	 * @return bool|null
	 */
	public function is(string $type):bool|null{

		if(in_array($type, ["map", "assoc"]))
			return $this->is_map($this->val);

		return $this->isof($type);
	}

	/**
	 * @param string $type
	 * 
	 * @return bool|null
	 */
	public function isof(string $type):bool|null{

		$count = count($this->val) || $this->empty();

		if(in_array($type, ["strings","string"]))
			return negate(array_sum(array_map('is_string', $this->val)) != $count);

		if(in_array($type, ["numbers", "number"]))
			return negate(array_sum(array_map('is_numeric', $this->val)) != $count);

		return null;
	}

	/**
	* Tokenize array string items
	* 
	* @param array $keys = null
	* 
	* @return string
	*/
	public function tokenize(?array $keys = null):string{

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

	/**
	* Is array empty
	* 
	* @return bool
	*/
	public function empty():bool{

		return $this->length() == 0;
	}

	/**
	* Count array items
	* 
	* @return int
	*/
	public function length():int{

		return count($this->val);
	}

	/**
	* Count array items
	* 
	* @return int
	*/
	public function count():int{

		return $this->length();
	}

	/**
	 * Get only distinct items in array
	 * 
	 * @return static
	 */
	public function distinct():static{

		return arr(array_count_values($this->val));
	}

	/**
	 * Slice array by offset and length
	 * 
	 * @param int $offset
	 * @param int $length
	 * 
	 * @return static
	 */
	public function slice(int $offset, ?int $length = null):static{

		if(!is_null($length))
			return arr(array_slice($this->val, $offset, $length));

		return arr(array_slice($this->val, $offset));
	}

	/**
	 * Get only values by keys
	 * 
	 * @param array $haystack
	 * 
	 * @return static
	 */
	public function only(array $haystack):static{

		return $this->filter(function($needle) use($haystack){

			return in_array($needle, $haystack);

		});
	}

	/**
	 * Reset cursor in array
	 */
	public function reset():void{

		reset($this->val);
	}

	/**
	 * Get first array element
	 * 
	 * @return mixed
	 */
	public function first():mixed{

		$this->reset();

		return $this->current();
	}

	/**
	 * Get key at element on array cursor
	 * 
	 * @return mixed
	 */
	public function key():mixed{

		return key($this->val);
	}

	/**
	 * Get current array item at cursor - same as Arr.valid
	 * 
	 * @return Strukt\Contract\ValueObject
	 */
	public function current():ValueObject{

		$curr_elem = current($this->val);

		return new ValueObject($curr_elem);
	}

	/**
	 * Get current array item at cursor - as Arr.current
	 * 
	 * @return mixed
	 */
	public function valid():mixed{

		return $this->current()->yield();
	}

	/**
	 * Get next array item
	 * 
	 * @return mixed
	 */
	public function next():mixed{

		$elem_exists = !!next($this->val);

		return $elem_exists;
	}

	/**
	 * Get last array item
	 * 
	 * @return Strukt\Contract\ValueObject
	 */
	public function last():ValueObject{

		$last_elem = end($this->val);

		return new ValueObject($last_elem);
	}

	/**
	 * Remove array item by key
	 * 
	 * @param mixed $key 
	 * 
	 * @return static
	 */
	public function remove($key):static{

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

	/**
	 * Skip key
	 * 
	 * @param string $key
	 * 
	 * @return static 
	 */
	public function skip(string $key):static{

		$this->skip[] = $key;

		return $this;
	}

	/**
	 * Jump value
	 * 
	 * @param string $val
	 * 
	 * @return static
	 */
	public function jump(string $val):static{

		$this->jump[] = $val;

		return $this;
	}

	/**
	 * Halt loop at specific key
	 * 
	 * @param string $key
	 * 
	 * @return static
	 */
	public function stop(string $key):static{

		$this->stop_at = $key;

		return $this;
	}

	/**
	 * Loop over array
	 *
	 * @param Closure $func
	 * 
	 * @return static
	 */
	public function each(callable $func):static{

		$evt = Event::create($func->bindTo($this));

		$vals = [];
		foreach($this->val as $key=>$val)
		 if(negate(array_key_exists($this->stop_at, $vals)))
			if(negate(in_array($key, $this->skip)))
				if(negate(in_array($val, $this->jump)))
					$vals[$key] = $evt->apply($key, $val)->exec();

		return new $this($vals);
	}

	/**
	 * Array filter
	 * 
	 * @param callable $func = null
	 * 
	 * @return static
	 */
	public function filter(?callable $func = null):static{

		if(is_null($func))
			$func = fn($k, $v)=>negate(empty($v)) || negate(empty($v));

		$vals = [];
		foreach($this->val as $k=>$v)
			if(negate(array_key_exists($this->stop_at, $vals)))
				if($func($k, $v) || (in_array($k, $this->skip) || in_array($v, $this->jump)))
					$vals[$k] = $v;

		return new $this($vals);
	}

	/**
	 * Iterate over a deep nested array recursively
	 * 
	 * @param callable $func
	 * 
	 * @return static
	 */
	public function recur(callable $func):static{

		$each = new Event($func->bindTo($this));

		foreach($this->val as $key=>$val){

			if(is_array($val))
				$val = \Strukt\Type\Arr::create($val)->each($func)->yield();

			$this->val[$key] = $each->apply($key, $val)->exec();
		}

		return $this;
	}

	/**
	 * Map array values in collection to new keys
	 * 
	 * @param array $maps
	 * 
	 * @return array
	 */
	public function map(array $maps):array{

		$collection = CollectionBuilder::create()->fromAssoc($this->val);

		foreach($maps as $key=>$name)
			if($collection->exists($name))
				$arr[$key] = $collection->get($name);

		return $arr;
	}

	/**
	 * Check if array is nested
	 * 
	 * @return bool
	 */
	public function nested():bool{

		return array_sum(array_map("is_array", $this->val)) == count($this->val);
	}

	/**
	 * Sort linear array 
	 * 
	 * @param bool $asc - sort ascending default:true
	 * @param bool $ksort - sort by keys default:false
	 * 
	 * @return static
	 */
	public function sort(bool $asc = true, bool $ksort = false):static{

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

	/**
	 * Array multiply array values
	 * 
	 * @return number
	 */
	public function product(){

		return array_product($this->val);
	}

	/**
	 * Array contains value
	 * 
	 * @return bool
	 */
	public function has(mixed $val):bool{

		$vals = array_filter(array_map(function($piece){

		    return serialize($piece);

		}, $this->val));

		$val = serialize($val);

		return in_array($val, $vals);
	}

	/**
	 * Array contains key
	 * 
	 * @return bool
	 */
	public function contains(string $key):bool{

		return array_key_exists($key, $this->val);
	}

	/**
	* Get array values only
	*   
	* @return static
	*/
	public function values():static{

		return new $this(array_values($this->val));
	}

	/**
	 * Get array keys
	 * 
	 * @return mixed
	 */
	public function keys():mixed{

		return array_keys($this->val);
	}

	/**
	 * Merge to array
	 * 
	 * @param Array $arr
	 * 
	 * @return static
	 */
	public function merge(array $arr):static{

		return new $this(array_merge($this->val, $arr));
	}

	/**
	 * Array unique values
	 * 
	 * @return static
	 */
	public function uniq():static{

		return new $this(array_unique($this->val));
	}

	/**
	 * Array reverse
	 * 
	 * @return static
	 */
	public function reverse():static{

		return new $this(array_reverse($this->val));
	}

	/**
	 * Array flip
	 * 
	 * @return static
	 */
	public function flip():static{

		return new $this(@array_flip($this->val));
	}

	/**
	 * Rehash keys
	 * 
	 * @return static
	 */
	public function rehash():static{

		return $this->reverse()->reverse();
	}

	/**
	 * Order multidimesional array
	 * 
	 * @return object
	 */
	public function order():object{

		if(negate(arr($this->val)->nested()))
			raise("Array must be of nested values!");

		return new class($this->val){

			private $by;
			private $val;

			/**
			 * @param array $val
			 */
			public function __construct(array $val){

				$this->val = $val;
				$this->by = [];
			}

			/**
			 * @param string $column
			 * 
			 * @return static
			 */
			public function asc(string $column):static{

				$column = arr($this->val)->column($column);
				$this->by = array_merge($this->by, [$column, SORT_ASC]);
				if(arr($column)->isOfNum())
					$this->by = array_merge($this->by, [SORT_NUMERIC]);

				return $this;
			}

			/**
			 * @param string $column
			 * 
			 * @return static
			 */
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