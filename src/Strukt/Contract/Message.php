<?php

namespace Strukt\Contract;

abstract class Message{

	public function __construct(string $message){

		$this->add($message);
	}

	public function add($message){

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $message;
	}

	public static function withLimit(int $limit){

		static::$limit = $limit;
	}

	public static function get(){

		return new class(static::$messages) extends \Strukt\Type\Arr{

			public function __construct(&$messages){

				parent::__construct($messages);
			}
		};
	}

	public static function clear(){

		static::$messages = [];
	}
}