<?php

namespace Strukt;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Stack extends \Strukt\Contract\Stack{

	/**
	 * @param $message
	 */
	public function __construct(string $message){

		$this->message = $message;

		parent::__construct($message);
	}

	public function __toString(){

		return $this->message;
	}
}