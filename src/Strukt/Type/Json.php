<?php

namespace Strukt\Type;

use Laminas\Json\Json as LJson;

class Json{

	public static function pp(string|array $json){

		if(is_array($json))
			$json = LJson::encode($json);
			
		$result = LJson::prettyPrint($json);

		return $result;
	}

	public static function decode(string $json){

		$result = LJson::decode($json, true);

		return $result;
	}

	public static function encode(array $object){

		$result = LJson::encode($object);

		return $result;
	}

	public static function isJson($args) {

	    json_decode($args, true);

	    return (json_last_error()===JSON_ERROR_NONE);
	}
}