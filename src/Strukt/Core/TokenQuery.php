<?php

namespace Strukt\Core;

use Strukt\Type\Str;
use Strukt\Raise;

/**
 * @author Moderator <pitsolu@gmail.com>
*/
class TokenQuery{

	protected $token;
	protected $parts;

	/**
	 * @param string $token
	 */
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

	/**
	 * @param string $key
	 */
	public function has(string $key){

		return array_key_exists($key, $this->parts);
	}

	/**
	 * @param string $key
	 * 
	 * @return array|string|null
	 */
	public function get(string $key):array|string|null{

		if($this->has($key))
			return $this->parts[$key];

		return null;
	}

	/**
	 * @param string $key
	 * 
	 * @return static
	 */
	public function remove(string $key):static{

		unset($this->parts[$key]);

		return $this;
	}

	/**
	 * @param string $key
	 * @param string|array|int|float $val
	 * 
	 * @return static
	 */
	public function set(string $key, string|array|int|float $val):static{

		if(is_array($val))
			if(!arr($val)->isOfStr())
				new Raise("Array must be of strings");

		$this->parts[$key] = $val;

		return $this;
	}	

	/**
	 * @return array
	 */
	public function keys():array{

		return array_keys($this->parts);
	}

	/**
	 * @return string
	 */
	public function token():string{

		return $this->token;
	}

	/**
	 * @return string
	 */
	public function yield():string{

		foreach($this->parts as $key=>$val){

			if(is_array($val))
				$val = implode(",", $val);

			$map[] = Str::create($key)->concat(":")->concat($val)->yield();
		}

		return implode("|", $map);
	}
}