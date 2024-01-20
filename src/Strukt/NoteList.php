<?php

namespace Strukt;

class NoteList extends \Strukt\Contract\NoteList{

	protected $message;
	protected static $messages = [];
	protected static $limit = 100;

	public function __construct(string $message){

		$this->message = $message;

		parent::__construct($message);
	}

	public function __toString(){

		return $this->message;
	}
}