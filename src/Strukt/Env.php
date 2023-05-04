<?php

namespace Strukt;

use Strukt\Core\Registry;
use Strukt\Raise;
use Strukt\Type\Str;

class Env{

	public static function withFile($path=".env"){

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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

	public static function has($key){

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		return $registry->exists($key);
	}

	public static function get($key){

		$key = sprintf("env.%s", $key);

		$registry = Registry::getSingleton();

		if(!$registry->exists($key))
			new Raise(sprintf("Couldn't get [%s], may not be set by %s!", $key, __CLASS__));

		return $registry->get($key);
	}

	public static function set($key, $val){

		if(!is_string($key) && !is_string($val))
			new Raise(sprintf("%s::set(key,val) key and val must be strings!", __CLASS__));
			
		Registry::getSingleton()->set(sprintf("env.%s", $key), $val);
	}
}