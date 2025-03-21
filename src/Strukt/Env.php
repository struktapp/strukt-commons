<?php

namespace Strukt;

use Strukt\Core\Registry;
use Strukt\Raise;
use Strukt\Type\Str;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Env{

	/**
	 * @param string $path
	 * 
	 * @return void
	 */
	public static function withFile(string $path=".env"):void{

		$lines = file(phar($path)->adapt(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach($lines as $line){

			$currLine = Str::create(trim($line));
			if($currLine->startsWith("#") || $currLine->startsWith("//"))
				continue;

			list($key, $val) = explode("=", $line);

			$val = trim($val);
			$states = ["true"=>true,"false"=>false];
			if(array_key_exists($val, $states))
				$val = $states[$val];

			static::set(trim($key), $val);
		}
	}

	/**
	 * @param string $key
	 * 
	 * @return boolean
	 */
	public static function has(string $key):bool{

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		return $registry->exists($key);
	}

	/**
	 * @param string $key
	 * 
	 * @return mixed
	 */
	public static function get(string $key):mixed{

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		if(!$registry->exists($key))
			new Raise(sprintf("Couldn't get [%s], may not be set by %s!", $key, __CLASS__));

		return $registry->get($key);
	}

	/**
	 * @param string $key
	 * @param string|int|bool $val
	 * 
	 * @return void
	 */
	public static function set(string $key, string|int|bool $val):void{
			
		Registry::getSingleton()->set(sprintf("env.%s", $key), $val);
	}
}