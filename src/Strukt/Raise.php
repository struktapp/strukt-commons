<?php

namespace Strukt;

class Raise extends \Strukt\Message{

	public function __construct($error, $code = 500){

		parent::__construct($error);

		throw new \Exception($error, $code);
	}
}