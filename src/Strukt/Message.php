<?php

namespace Strukt;

class Message extends \Strukt\Contract\Message{

	protected $message;
	protected static $messages = [];
	protected static $limit = 9;

	public function __construct(string $message){

		$this->message = $message;

		parent::__construct($message);
	}

	public function __toString(){

		return $this->message;
	}
}