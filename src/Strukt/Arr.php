<?php

namespace Strukt;

use Strukt\ValueObject;
use Strukt\Ref;

abstract class Arr extends ValueObject{

	private $stop_at = null;
	private $skip = [];
	private $jump = [];

	protected $value = [];

	public function empty():bool{

		return $this->length() == 0;
	}

	public function length():int{

		return count($this->value);
	}

	public function count():int{

		return $this->length();
	}

	public function pop():mixed{

		return array_pop($this->value);
	}

	public function push(mixed $item, ?string $key = null):static{

		$values = $this->value;

		if(is_null($key))
			array_push($values, $item);

		if(notnull($key))
			$values[$key] = $item;

		end($values);

		return new $this($values);
	}

	public function remove(string $key):static{

		$values = $this->value;
		unset($values[$key]);

		return new $this($value);
	}

	public function dequeue():mixed{

		return array_shift($this->value);
	}

	public function enqueue(mixed $element, mixed $key = null):static{

		return $this->push($element, $key);
	}

	public function prequeue(mixed $element, mixed $key = null):static{

		$values = $this->value;
		if(!is_null($key))
			$values = array_merge(array($key=>$element), $values);

		if(is_null($key))
			array_unshift($values, $element);

		reset($values);

		return new $this($values);
	}

	public function reset():void{

		reset($this->value);
	}

	public function first():mixed{

		$this->reset();

		return $this->current();
	}

	public function current():mixed{

		$current = current($this->value);

		return $current;
	}

	public function valid():mixed{

		return $this->current();
	}

	public function next():mixed{

		$exists = !!next($this->value);

		return $exists;
	}

	public function last():mixed{

		$last = end($this->value);

		return $last;
	}

	public function key():mixed{

		return key($this->value);
	}

	/**
	 * Get array keys
	 * 
	 * @return mixed
	 */
	public function keys():mixed{

		return array_keys($this->value);
	}

	public function values():static{

		return new $this(array_values($this->value));
	}

	public function uniq():static{

		return new $this(array_unique($this->value));
	}

	public function reverse():static{

		return new $this(array_reverse($this->value));
	}

	public function flip():static{

		return new $this(@array_flip($this->value));
	}

	public function rehash():static{

		return $this->reverse()->reverse();
	}

	public function product(){

		if(in_array(false, array_map("is_numeric", array_values($this->value))) ||
			$this->is()->nested())
				raise("Incompatible array!");

		return array_product($this->value);
	}

	public function sum(){

		if(in_array(false, array_map("is_numeric", array_values($this->value))) ||
			$this->is()->nested())
				raise("Incompatible array!");

		return array_sum($this->value);
	}

	public function has(mixed $key):bool{

		return array_key_exists($key, $this->value);
	}

	public function contains(mixed $value):bool{

		$values = $this->map(function($piece){

		    return serialize($piece);

		})->filter()->yield();

		$value = serialize($value);

		return in_array($value, $values);
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

	public function jump(string $val):static{

		$this->jump[] = $val;

		return $this;
	}

	public function stop(string $key):static{

		$this->stop_at = $key;

		return $this;
	}

	public function add(string $key, mixed $item):static{

		$this->value[$key] = $item;

		return $this;
	}

	public function merge(array $arr):static{

		return new $this(array_merge($this->value, $arr));
	}

	/**
	 * Previously enqueueAll
	 */
	public function enjoin(array $element):static{

		if(!is_map($element)){

			array_push($this->value, ...$element);
			$values = $this->value;

			return new $this($values);
		}

		$values = array_merge($this->value, $element);

		return new $this($values);
	}

	public function join(string $delimiter):string{

		if(negate(array_product(array_map(fn($x)=>(int)is_string($x), $this->value))))
			raise("Incompatible array!");

		return implode($delimiter, $this->value);
	}

	public function slice(int $offset, ?int $length = null):static{

		if(!is_null($length))
			return arr(array_slice($this->value, $offset, $length));

		return arr(array_slice($this->value, $offset));
	}

	public function column(string $key):static{

		if(negate($this->is()->nested()))
			raise("Incompatible array!");

		$column = array_column($this->value, $key);

		return new $this($column);
	}

	public function distinct():static{

		if($this->is()->nested())
			raise("Incompatible array!");

		return arr(array_count_values($this->value));
	}

	public function only(array $haystack):static{

		return $this->filter(function($needle) use($haystack){

			return in_array($needle, $haystack);
		});
	}

	public function map(callable $func):static{

		$values = $this->value;
		return new $this(array_map(function($key) use ($func, $values){
	        return $func($values[$key], $key);
	    }, array_keys($this->value)));
	}

	public function filter(?callable $func = null):static{

		if(is_null($func))
			$func = fn($k, $v)=>negate(empty($k)) || negate(empty($v));

		$values = [];
		foreach($this->value as $k=>$v)
			if(negate(array_key_exists($this->stop_at, $values)))
				if($func($k, $v) || (in_array($k, $this->skip) || in_array($v, $this->jump)))
					$values[$k] = $v;

		return new $this($values);
	}

	public function each(callable $func):static{

		$ref = Ref::func($func->bindTo($this));
		$jump = arr($this->jump);
		$skip = arr($this->skip);

		$raw = $this->value;
		foreach($this->value as $key=>$value){

			$raw[$key] = $value;
			
			if($key == $this->stop_at) break;
			if(negate($jump->contains($value)))
				if(negate($skip->contains($key)))
					$raw[$key] = $ref->invoke($key, $value);
		}

		/**reset jump, skip & stop_at**/
		$this->jump = [];
		$this->skip = [];
		$this->stop_at = null;

		return arr($raw);
	}

	/**
	* Flatten array
	* 
	* @param array $arr
	* 
	* @return array
	*/
	public function level():array{

		$result = array();
		$it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->value));

		$i=0;
		foreach ($it as $key => $value){

			if(empty($key) || array_key_exists($key, $result)) $key = rand();
			$result[$key??$i++] = $value;
		}

		return $result;
	}

	public function isof(){

		return new class($this->value){

			protected $value;
			public function __construct($value){

				$this->value = $value;
			}

			protected function count(){

				$count = 0;
				if(!empty($this->value))
					$count = count($this->value);

				return $count;
			}

			public function strings():bool{

				return array_sum(array_map('is_string', $this->value)) == $this->count();
			}

			public function numbers():bool{

				return array_sum(array_map('is_numeric', $this->value)) == $this->count();
			}

			public function booleans():bool{

				return array_sum(array_map('is_bool', $this->value)) == $this->count();
			}
		};
	}

	public function is(){

		return new class($this->value){

			protected $value;
			public function __construct($value){

				$this->value = $value;
			}

			public function map():bool{

				return is_map($this->value);
			}

			public function nested():bool{

				return (bool)array_sum(array_map(fn($x)=>(int)is_array($x), $this->value));
			}
		};
	}

	public function tokenize(?array $keys = null):string{

		if(!$this->is()->map() || negate(empty(array_filter($this->value, "is_object"))))
			raise("Incompatible array!");

		if(is_null($keys))
			$keys = $this->keys();

		$token = [];
		foreach($this->value as $key=>$value)
			if(in_array($key, $keys))
				$token[] = sprintf("%s:%s", $key, is_array($value)?implode(",", $value):$value);

		return implode("|", $token);
	}

	public function sort():static{

		return new class($this->value){

			public function __construct($val){

				$this->value = $val;
			}

			public function asc(bool $ksort = false){

				if(negate($ksort))
					asort($this->value);

				if($ksort)
					ksort($this->value);

				return $this->value;
			}

			public function desc(bool $ksort = false){
			
				if(negate($ksort))
					arsort($this->value);

				if($ksort)
					krsort($this->value);

				return $this->value;
			}
		};
	}

	/**
	 * Order multidimesional array
	 * 
	 * @return object
	 */
	public function order():object{

		if($this->is()->nested())
			raise("Incompatible array!");

		return new class($this->value){

			private $by;
			private $value;

			/**
			 * @param array $value
			 */
			public function __construct(array $value){

				$this->value = $value;
				$this->by = [];
			}

			/**
			 * @param string $column
			 * 
			 * @return static
			 */
			public function asc(string $column):static{

				$column = arr($this->value)->column($column);
				$this->by = array_merge($this->by, [$column, SORT_ASC]);
				if(arr($column)->isof()->numbers())
					$this->by = array_merge($this->by, [SORT_NUMERIC]);

				return $this;
			}

			/**
			 * @param string $column
			 * 
			 * @return static
			 */
			public function desc(string $column){

				$column = arr($this->value)->column($column);
				$this->by = array_merge($this->by, [$column, SORT_DESC]);
				if(arr($column)->isof()->numbers())
					$this->by = array_merge($this->by, [SORT_NUMERIC]);

				return $this;
			}

			public function yield(){

				$this->by[] = &$this->value;

				array_multisort(...$this->by);

				return $this->value;
			}
		};
	}
}