<?php

namespace Strukt\Message;

class Info extends \Strukt\Contract\Message{

	private static $messages = [];
	private static $limit = 9;

	public function __construct(string $message){

		parent::__construct($message);
	}
}