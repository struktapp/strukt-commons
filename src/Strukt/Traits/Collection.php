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

		$keyChain = arr(explode(".", $key));

		if(number($keyChain->length())->equals(1)){

			$collection->set($key, $val);

			return;
		}

		while($keyPart = $keyChain->dequeue()){

			if($collection->exists($keyPart)){

				$next = $collection->get($keyPart);
				if($next instanceof CollectionInterface)
					$collection = $next;

				continue;
			}

			if($keyChain->empty()){

				if(is_array($val))
					if(arr($val)->isMap())
						$val = collect($val);
			}

			if(!$collection->exists($keyPart) && !$keyChain->empty()){

				$tmp = collect([]);
				$collection->set($keyPart, $tmp);
				$collection = $tmp;

				continue;
			}

			$collection->set($keyPart, $val);
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