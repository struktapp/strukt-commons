<?php

namespace Strukt\Type;

class Json{

	public static function pp($json){

		if(is_string($json))
			$json = self::decode($json);
			
		return self::encode($json, true);
	}

	public static function decode(string $json, $isAssoc = true, $replaceQuotes = true){

		if($replaceQuotes)
			$arrObj = json_decode(str_replace("'","\"", $json), $isAssoc);

		if(!$replaceQuotes)
			$arrObj = json_decode($json, $isAssoc);

		if(json_last_error() != JSON_ERROR_NONE)
			throw new \Exception(sprintf("JSON Error: %s", json_last_error_msg()));
			
		return $arrObj;
	}

	public static function encode(Array $arrObj, $prettyPrint = false){

		if($prettyPrint)
			$json = json_encode($arrObj, JSON_PRETTY_PRINT);
		else
			$json = json_encode($arrObj);

		if(json_last_error() != JSON_ERROR_NONE)
			throw new \Exception(sprintf("JSON Error: %s", json_last_error_msg()));

		return $json;
	}

	public static function isJson($args) {

	    json_decode($args, true);

	    return (json_last_error()===JSON_ERROR_NONE);
	}
}