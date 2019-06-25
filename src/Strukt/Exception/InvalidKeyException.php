<?php

namespace Strukt\Exception;

class InvalidKeyException extends \Exception{

	public function __construct($key, 
	 								$message="", 
	 								$code = 0, 
	 								Exception $previous = null) {

	 	if(empty($message))
	 		$message = sprintf("Invalid key name [%s]!", $key);

        parent::__construct($message, $code, $previous);
    }
}