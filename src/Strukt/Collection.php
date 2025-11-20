<?php

namespace Strukt;

use Strukt\Contract\CollectionInterface;
use Strukt\Raise;

class Collection implements CollectionInterface{

	private $value;

	public function __construct(array $value){

		$this->value = $value;
	}

	public function set(string $key, mixed $value):void{

		attach($key, $this->value, $value);
	}

	public function get(string $key){

		try{
			
			return dot($key, $this->value);
		}
		catch(\Exception $e){

			return null;
		}
	}

	public function ask(string $key){

		$value = $this->get($key);
		if(notnull($value) && is_map($value))
			return collect($value);
	}

	public function remove(string $key){

		return detach($key, $this->value);
	}

	public function keys(?string $key = null):array{

		$value = $this->value;
		if(notnull($key))
			$value = dot($key, $this->value);

		return array_keys($value);
	}

	public function exists(string $key):bool{

		try{

			$value = $this->get($key);
			if(is_null($value))
				new Raise(sprintf("%s not found", $key));

			return true;
		}
		catch(\Exception $e){

			return false;
		}
	}
}