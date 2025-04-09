<?php

namespace Strukt\Contract;

use Strukt\Type\Arr;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class Heap{

	/**
	 * @param string $message
	 */
	public function __construct(string $message){

		$this->add($message);
	}

	/**
	 * @return void
	 */
	public function add($message):void{

		if(count(static::$messages) > static::$limit)
			array_shift(static::$messages);

		static::$messages[] = $message;
	}

	/**
	 * @param integer $limit
	 */
	public static function withLimit(int $limit):void{

		static::$limit = $limit;
	}

	/**
	 * @param ?string $pattern
	 * 
	 * @return \Strukt\Type\Arr
	 */
	public static function get(?string $pattern = null):Arr{

		$messages = static::$messages;
		if(negate(is_null($pattern)))
			$messages = preg_grep("/$pattern/", static::$messages);

		return new class($messages) extends Arr{

			public function __construct(&$messages){

				parent::__construct($messages);
			}
		};
	}

	/**
	 * @return void
	 */
	public static function clear():void{

		static::$messages = [];
	}
}