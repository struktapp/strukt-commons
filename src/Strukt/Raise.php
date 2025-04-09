<?php

namespace Strukt;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Raise extends \Strukt\Heap{

	/**
	 * @param string $error
	 * @param integer $code
	 */
	public function __construct(string $error, int $code = 500){

		parent::__construct($error);

		throw new \Exception($error, $code);
	}
}