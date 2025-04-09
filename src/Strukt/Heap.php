<?php

namespace Strukt;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Heap extends \Strukt\Contract\Heap{

	protected $message;
	protected static $messages = [];
	protected static $limit = 3;

	/**
	 * @param $message
	 */
	public function __construct($message){

		$this->message = $message;

		parent::__construct($message);
	}

	public function __toString(){

		return $this->message;
	}
}