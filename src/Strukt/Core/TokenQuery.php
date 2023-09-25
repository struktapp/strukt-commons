<?php

namespace Strukt\Core;

use Strukt\Type\Str;
use Strukt\Raise;

class TokenQuery{

	protected $token;
	protected $parts;

	public function __construct(string $token){

		$this->token = $token; 

		$map = Str::create($token)->split("|");

		foreach($map as $item){

			list($key, $val) = explode(":", $item, limit:2);
			if(preg_match("/,/", $val))
				$val = explode(",", $val);

			$this->parts[$key] = $val;
		}
	}

	public function has(string $key){

		return array_key_exists($key, $this->parts);
	}

	public function get($key){

		if($this->has($key))
			return $this->parts[$key];

		return null;
	}

	public function remove($key){

		unset($this->parts[$key]);
	}

	public function set(string $key, $val){

		if(!is_array($val) && !is_string($val))
			new Raise("Value must be string or array");

		if(is_array($val))
			if(array_sum(array_map('is_string', $val)) != count($val))
				new Raise("Array must be of strings");

		$this->parts[$key] = $val;
	}	

	public function keys(){

		return array_keys($this->parts);
	}

	public function token(){

		return $this->token;
	}

	public function reMake(){

		foreach($this->parts as $key=>$val){

			if(is_array($val))
				$val = implode(",", $val);

			$map[] = Str::create($key)->concat(":")->concat($val)->yield();
		}

		return implode("|", $map);
	}
}