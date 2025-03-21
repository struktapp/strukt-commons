<?php

namespace Strukt\Type;

use Laminas\Json\Json as LJson;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Json{

	/**
	 * @param string|array $json
	 * 
	 * @return string
	 */
	public static function pp(string|array $json):string{

		if(is_array($json))
			$json = LJson::encode($json);
			
		$result = LJson::prettyPrint($json);

		return $result;
	}

	/**
	 * @param sting $json
	 * 
	 * @return array
	 */
	public static function decode(string $json):array{

		$result = LJson::decode($json, true);

		return $result;
	}

	/**
	 * @param array $object
	 * 
	 * @return string
	 */
	public static function encode(array $object):string{

		$result = LJson::encode($object);

		return $result;
	}

	/**
	 * @param $args
	 * 
	 * @return boolean
	 */
	public static function isJson($args):bool{

	    json_decode($args, true);

	    return (json_last_error()===JSON_ERROR_NONE);
	}
}