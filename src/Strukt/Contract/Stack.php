<?php

namespace Strukt\Contract;

use Strukt\Arr;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class Stack{

	protected $messages;
	protected $limit = 10;

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

		if(count($this->messages) > $this->limit)
			array_shift($this->messages);

		$this->messages[] = $message;
	}

	/**
	 * @param integer $limit
	 */
	public static function limit(int $limit):void{

		$this->limit = $limit;
	}

	/**
	 * @param ?string $pattern
	 * 
	 * @return \Strukt\Arr
	 */
	public static function get(?string $pattern = null):Arr{

		$messages = $this->messages;
		if(negate(is_null($pattern)))
			$messages = preg_grep("/$pattern/", $this->messages);

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

		$this->messages = [];
	}
}