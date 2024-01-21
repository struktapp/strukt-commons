<?php

namespace Strukt\Traits;

use Strukt\Contract\CollectionInterface;
use Strukt\Exception\KeyOverlapException;

trait Collection{

	/**
	* Marshal dot-notationed key into a Strukt\Contract\CollectionInterface
	*
	* @param string $key
	* @param mixed $val
	* @param Strukt\Contract\CollectionInterface $collection
	*/
	public static function assemble($key, $val, CollectionInterface $collection){

		if($collection->exists($key))
			if(!empty($collection->get($key)))
				throw new KeyOverlapException($key);

		$keyChain = explode(".", $key);

		$lastKey = array_pop($keyChain);

		foreach($keyChain as $keyPart){

			if($collection->exists($keyPart)){

				$val = $collection->get($keyPart);
				if($val instanceof CollectionInterface)
					$collection = $val;

				continue;
			}

			$tmp = collect([]);
			$collection->set($keyPart, $tmp);
			$collection = $tmp;
		}

		if(is_array($val))
			if(is_array(reset($val)))
				$val = collect($val);

		$collection->set($lastKey, $val);
	}

	/**
	* Disassemble \Strukt\Core\Collection into an array
	*
	* @return []
	*/
	protected function disassemble(CollectionInterface $collection){

		$buffer = [];
		$keys = $collection->keys();
		foreach($keys as $key){

			$val = $collection->get($key);
			if($val instanceof CollectionInterface)
				$buffer[$key] = $this->disassemble($val);

			if(!$val instanceof CollectionInterface)
				$buffer[$key] = $val;
		}		

		return $buffer;
	}
}