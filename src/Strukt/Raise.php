<?php

namespace Strukt;

class Raise extends \Strukt\NoteList{

	public function __construct($error, $code = 500){

		parent::__construct($error);

		throw new \Exception($error, $code);
	}
}