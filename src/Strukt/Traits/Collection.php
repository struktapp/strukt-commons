<?php

namespace Strukt\Traits;

use Strukt\Contract\CollectionInterface;
use Strukt\Exception\KeyOverlapException;

/**
 * @author Moderator <pitsolu@gmail.com>
*/
trait Collection{

	/**
	* Marshal dot-notationed key into a \Strukt\Contract\CollectionInterface
	*
	* @param string $key
	* @param mixed $val
	* @param \Strukt\Contract\CollectionInterface $collection
	* 
	* @return void
	*/
	protected static function assemble(string $key, mixed $val, CollectionInterface $collection):void{

		if($collection->exists($key))
			if(!empty($collection->get($key)))
				throw new KeyOverlapException($key);

		$keyChain = arr(explode(".", $key));

		if(number($keyChain->length())->equals(1) && !is_array($val)){

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
	* @param \Strukt\Contract\CollectionInterface $collection
	*
	* @return array
	*/
	protected function disassemble(CollectionInterface $collection):array{

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