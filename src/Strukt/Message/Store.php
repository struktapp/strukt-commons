<?php

namespace Strukt\Message;

class Store{

	private static $store = null;
	private static $messages = [];
	private static $limit = 9;

	private function __construct(){

		//
	}

	public static function create(){

		if(is_null(static::$store))
			static::$store = new Self();

		return static::$store;
	}

	public static function setLimit($limit){

		static::$limit = $limit;
	}

	public function addMessage($message){

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $message;
	}

	public function getMessages(){

		return new class(static::$messages) extends \Strukt\Type\Arr{

			public function __construct(&$messages){

				parent::__construct($messages);
			}
		};
	}
}