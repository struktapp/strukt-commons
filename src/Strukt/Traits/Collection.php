<?php

namespace Strukt\Traits;

use Strukt\Contract\CollectionInterface;

trait Collection{

	/**
	* Marshal dot-notationed key into a \Strukt\Core\Collection
	*
	* @param string $key
	* @param mixed $val
	* @param \Strukt\Core\Collection $collection
	*/
	protected function assemble(string $key, $val, CollectionInterface $collection){

		if($collection->exists($key))
			if(!empty($collection->get($key)))
				throw new \Strukt\Exception\KeyOverlapException($key);
			
		$keys = arr(explode(".", $key));

		while($key = $keys->dequeue()){

			if(!$keys->empty()){

				if($collection->exists($key)){

					$collection = $collection->get($key);
					continue;
				}

				if(!$collection->exists($key)){

					$tmp = collect([]);
					$collection->set($key, $tmp);
					$collection = $tmp;
				}
			}

			if($keys->empty()){

				if(is_array($val))
					if(arr($val)->isMap())
						$collection->set($key, collect($val));

				if(!is_array($val))
					$collection->set($key, $val);
			}
		}
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