<?php

namespace Strukt;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class NoteList extends \Strukt\Contract\NoteList{

	protected $message;
	protected static $messages = [];
	protected static $limit = 100;

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