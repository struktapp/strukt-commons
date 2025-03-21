<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class NoteList{

	/**
	 * @param string $message
	 */
	public function __construct(string $message){

		$this->add($message);
	}

	/**
	 * @return void
	 */
	public function add($message){

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $message;
	}

	/**
	 * @param integer $limit
	 */
	public static function withLimit(int $limit){

		static::$limit = $limit;
	}

	/**
	 * @return \Strukt\Type\Arr
	 */
	public static function get(){

		return new class(static::$messages) extends \Strukt\Type\Arr{

			public function __construct(&$messages){

				parent::__construct($messages);
			}
		};
	}

	/**
	 * @return void
	 */
	public static function clear(){

		static::$messages = [];
	}
}