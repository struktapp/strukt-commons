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
			
		$tmp = $collection;
		$keys = arr(explode(".", $key));
		foreach($keys->yield() as $keyPart){

			$last = $keys->last()->equals($keyPart);
			if($last){

				$tmp->set($keyPart, $val);
				continue;
			}

			if(!$last){

				if(!$tmp->exists($keyPart)){

					$newTmp = collect([]);
					$tmp->set($keyPart, $newTmp);
					$tmp = $newTmp;
				}

				if($tmp->exists($keyPart))
					$tmp = $tmp->get($keyPart);
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