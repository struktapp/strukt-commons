<?php

namespace Strukt\Type;

use Strukt\Contract\ValueObject as ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Contract\AbstractArrOps;
use Strukt\Contract\Arr as ContractArr;
use Strukt\Raise;
use Strukt\Event;

class Arr extends ContractArr{

	public function __construct(array $arr){

		$this->val = $arr;
	}

	public static function create($arr){

		if(!is_array($arr))
			new Raise(sprintf("%s::create requires an array!", static::class));

		return new self($arr);
	}

	/**
	* Flatten array
	*/
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