<?php

namespace Strukt;

use Strukt\ValueObject;
use Strukt\Ref;

abstract class Arr extends ValueObject{

	private $stop_at = null;
	private $skip = [];
	private $jump = [];

	protected $value = [];

	public function __construct($value){

		parent::__construct($value);
		$this->first();
	}

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

	public function unset(?string $key = null):void{
	
		unset($this->value[$key??$this->key()]);
	}

	public function remove(string $key):static{

		$values = $this->value;
		unset($values[$key]);

		return new $this($values);
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

		$exists = !!next($this->value) || !!key($this->value);

		return $exists;
	}

	public function sibling(){

		return new class($this, $this->value){

			protected $parent;

			public function __construct($parent, $value){

				$this->parent = $parent;
				$this->value = $value;
			}

			public function next(){

				$this->parent->next();

				return $this->parent->current();
			}
		};
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

	public function has(mixed $key):bool{

		return array_key_exists($key, $this->value);
	}

	public function contains(mixed $value):bool{

		$values = $this->map(function($k, $v){

		    return serialize($v);

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
	public function skip(string|array $key):static{

		if(is_array($key))
			$this->skip = $key;

		if(negate(is_array($key)))
			$this->skip[] = $key;

		return $this;
	}

	public function jump(string|array $val):static{

		if(is_array($val))
			$this->jump = $val;

		if(negate(is_array($val)))
			$this->jump[] = $val;

		return $this;
	}

	public function stopAt(mixed $key = null):static{

		$this->stop_at = $key;

		return $this;
	}

	public function will(){

		$avoids = [

			"jumps"=>$this->jump, 
			"skips"=>$this->skip, 
			"stop_at"=>$this->stop_at
		];

		$this->jump = [];
		$this->skip = []; 
		$this->stop_at = null;

		return new class($avoids){

			protected $avoids;

			public function __construct(array $avoids){

				$this->avoids = $avoids;
			}

			public function jump(mixed $val){

			  	return in_array($val, $this->avoids["jumps"]);
			}

			public function skip(mixed $key){

				return in_array($key, $this->avoids["skips"]);
			}

			public function stopAt(mixed $key){

				$stop_at = $this->avoids["stop_at"];
				if(notnull($stop_at) && negate(empty($stop_at)))
					return $stop_at == $key;

				return false;
			}

			public function get(string $name){

				return $this->avoids[$name];
			}
		};
	}

	protected function from(string|int $key){

		if($this->key() != $key)
			if($this->next())
				return $this->from($key);

		return $this;
	}

	public function product(){

		if($this->is()->nested() || 
			(negate($this->isof()->numbers()) && negate($this->isof()->booleans())))
				raise("Incompatible array!");

		return array_product($this->value);
	}

	public function sum(){

		if($this->is()->nested() ||
			(negate($this->isof()->numbers()) && negate($this->isof()->booleans())))
				raise("Incompatible array!");

		return array_sum($this->value);
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

		if(negate(is_map($element))){

			array_push($this->value, ...$element);
			$values = $this->value;

			return new $this($values);
		}

		$values = array_merge($this->value, $element);

		return new $this($values);
	}

	public function join(string $delimiter):string{

		if(negate($this->isof()->strings()) || $this->is()->nested())
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

	/**
	 * array_diff
	 */
	public function diff(array $to_diff){

		return new class($this->value, $to_diff){

			private $arr;
			private $to_diff;

			public function __construct(array $arr, array $to_diff){

				$this->arr = $arr;
				$this->to_diff = $to_diff;
			}

			public function keys(){

				return arr(array_diff_key($this->arr, $this->to_diff));
			}

			public function values(){

				return arr(array_diff($this->arr, $this->to_diff));
			}

			public function map(){

				return arr(array_diff_assoc($this->arr, $this->to_diff));
			}
		};
	}

	/**
	 * array_intersect
	 */ 
	public function cross(array $subject){

		return new class($this->value, $subject){

			private $arr;
			private $subject;

			public function __construct(array $arr, array $subject){

				$this->arr = $arr;
				$this->subject = $subject;
			}

			public function keys(){

				return arr(array_intersect_key($this->arr, $this->subject));
			}

			public function values(){

				return arr(array_intersect($this->arr, $this->subject));
			}

			public function map(){

				return arr(array_intersect_assoc($this->arr, $this->subject));
			}
		};
	}

	public function only(array $haystack):static{

		return $this->filter(function($needle) use($haystack){

			return in_array($needle, $haystack);
		});
	}

	public function map(callable $func):static{

		$values = $this->value;
		return new $this(array_map(function($key) use ($func, $values){
	        return $func($key, $values[$key]);
	    }, array_keys($this->value)));
	}

	public function filter(?callable $func = null):static{

		if(is_null($func))
			$func = is_map($this->value)?fn($k,$v)=>(empty($k) || empty($v)):fn($k, $v)=>empty($v);

		$values = $this->value;
		if($func($this->key(), $this->current()))
			unset($values[$this->key()]);

		if($this->next())
			return (new static($values))
					->stopAt($this->stop_at)
					->from($this->key())->filter($func);

		return new static($values);
	}

	public function each(callable $func){

		$func = $func->bindTo($this);
		$will = $this->will();

		$values = $this->value;
		if($will->stopAt($this->key())) 
			return arr($values);

		if(negate($will->jump($this->current())) && negate($will->skip($this->key())))
			$values[$this->key()] = $func($this->key(), $this->current());

		if($this->next())
			return arr($values)
					->skip($will->get("skips"))
					->jump($will->get("jumps"))
					->stopAt($will->get("stop_at"))
					->from($this->key())->each($func);
	
		return arr($values);
	}

	/**
	* Flatten array
	* 
	* @param array $arr
	* 
	* @return array
	*/
	public function level(int $target = 999999999){ 

	   	return new class($this->value, $target){

	    	protected $value;
	    	protected $current = 1;
	    	protected $target = 999999999;
	    	protected $prefix = "";
	    	protected $noPrefix = false;

	    	public function __construct(array $value, int $target){

	    		$this->value = $value;
	    		$this->target = $target;
	    	}

	    	public function prefix(string $prefix){

	    		$this->prefix = $prefix;

	    		return $this;
	    	}

	    	public function noPrefix(){

	    		$this->noPrefix = true;

	    		return $this;
	    	}

	    	public function yield(){

	    		$leveled = level($this->value, $this->target, $this->current, $this->prefix);

	    		if($this->noPrefix){

	    			$leveled_prefixless = [];
					foreach($leveled as $k=>$v)
						$leveled_prefixless[preg_replace("/^\d+\./", "", $k)] = $v;

					return $leveled_prefixless;
	    		}

	    		return $leveled;
	    	}
	    };
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

	public function are(){

		return new class($this->value){

			protected $value;
			public function __construct($value){

				$this->value = $value;
			}

			public function all(mixed $what = null){

				$all = new class($this->value, $what){

					protected $value;
					protected $what;
					public function __construct($value, $what){

						$this->what = $what;
						$this->value = $value;
					}

					public function null(){

						return negate((bool)arr($this->value)->map(fn($k,$v)=>!is_null($v))->sum());
					}

					public function assert(){

						$what = $this->what;
						return (bool)arr($this->value)->map(fn($k,$v)=>$what == $v)->product();
					}

					public function empty(){

						return (bool)arr($this->value)->map(fn($k,$v)=>empty($v))->product();
					}
				};

				if(notnull($what))
					return $all->assert();

				return $all;
			}
		};
	}

	public function is(){

		return new class($this->value){

			protected $value;
			public function __construct($value){

				$this->value = $value;
			}

			public function any(){

				return new class($this->value){

					private $value;

					public function __construct($value){

						$this->value = $value;
					}

					public function empty(){

						return (bool)arr($this->value)->each(fn($k, $v)=>(int)empty($v))->sum();
					}

					public function null(){

						return (bool)arr($this->value)->each(fn($k, $v)=>(int)is_null($v))->sum();
					}
				};
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

	public function __destruct(){

		// reset($this->value);
	}
}