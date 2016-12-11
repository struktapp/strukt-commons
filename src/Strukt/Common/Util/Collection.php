<?php

namespace Strukt\Common\Util;

trait Collection {

	/**
	* Marshal dot-notationed key into a \Strukt\Core\Collection
	*
	* @param string $hashKey
	* @param mixed $val
	* @param \Strukt\Core\Collection $collection
	*/
	public function doMarshal($hashKey, $newVal, \Strukt\Core\Collection $collection){

		if($collection->exists($hashKey))
			if(!empty($collection->get($hashKey)))
				throw new \Exception("ValueOnValueException!");

		$keyList = explode(".", $hashKey);

		$lastKey = array_pop($keyList);

		foreach($keyList as $key){

			if($collection->exists($key)){

				$val = $collection->get($key);
				if($val instanceof \Strukt\Core\Collection)
					$collection = $val;

				continue;
			}

			$obj = new \Strukt\Core\Collection();
			$collection->set($key, $obj);
			$collection = $obj;
		}

		if(is_array($newVal))
			if(is_array(reset($newVal)))
				$newVal = \Strukt\Builder\CollectionBuilder::getInstance()->fromAssoc($newVal);

		$collection->set($lastKey, $newVal);
	}
}