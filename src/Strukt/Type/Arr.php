<?php

namespace Strukt\Type;

use Strukt\Contract\ValueObject; 
use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Contract\Arr as ArrConrtract;
use Strukt\Raise;
use Strukt\Event;

/**
 * @author Moderator <pitsolu@gmail.com>
*/
class Arr extends ArrConrtract{

	/**
	 * @param array $arr
	 */
	public function __construct(array $arr){

		$this->val = $arr;
	}

	/**
	 * @param array $arr
	 * 
	 * @return static
	 */
	public static function create($arr):static{

		return new self($arr);
	}

	/**
	* Flatten array
	* 
	* @param array $arr
	* 
	* @return array
	*/
	public static function level(array $arr):array{

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