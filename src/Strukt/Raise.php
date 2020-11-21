<?php

namespace Strukt;

class Raise{

	private static $messages = [];
	private static $limit = 9;

	public function __construct($error, $code = null){

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $error;

		throw new \Exception($error, $code);
	}

	public static function setStoreLimit($limit){

		static::$limit = $limit;
	}

	public static function getMessages(){

		return new class(static::$messages) extends Type\Arr{

			public function __construct(&$messages){

				parent::__construct($messages);
			}
		};
	}
}