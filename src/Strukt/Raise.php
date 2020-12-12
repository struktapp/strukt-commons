<?php

namespace Strukt;

class Raise extends \Strukt\Message\Error{

	public function __construct($error, $code = null){

		parent::__construct($error);

		throw new \Exception($error, $code);
	}
}