<?php

namespace Strukt\Contract;

abstract class Message{

	public function __construct(string $message){

		$this->addMessage($message);
	}

	public function addMessage($message){

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $message;
	}

	public static function setLimit(int $limit){

		static::$limit = $limit;
	}

	public static function getMessages(){

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