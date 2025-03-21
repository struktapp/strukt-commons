<?php

namespace Strukt\Exception;

/**
 * @author Moderator <pitsolu@gmail.com>
*/
class KeyOverlapException extends \Exception{

	public function __construct($key, 
	 								$message="", 
	 								$code = 0, 
	 								?Exception $previous = null) {

	 	if(empty($message))
	 		$message = sprintf("Key [%s] alreay exists!", $key);

        parent::__construct($message, $code, $previous);
    }
}