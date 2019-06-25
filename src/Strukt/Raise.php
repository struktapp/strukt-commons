<?php

namespace Strukt;

class Raise{

	public function __construct($error, $code = null){

		throw new \Exception($error, $code);
	}
}