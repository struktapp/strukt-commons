<?php

namespace Strukt\Util;

use Strukt\Raise;

class Str extends \Strukt\Contract\ValueObject{

	public function __construct($str){

		if(!is_string($str))
			new Raise(sprintf("%s requires string!", static::class));

		parent::__construct($str);
	}

	public static function create($str){

		return new self($str);
	}

	public function prepend($str){

		return new Str(sprintf("%s%s", $str, $this->val));
	}

	public function concat($str){

		return new Str(sprintf("%s%s", $this->val, $str));
	}

	public function len(){

		return strlen($this->val);
	}

	public function count($needle){

		return substr_count($this->val, $needle);
	}

	public function split($delimiter){

		return explode($delimiter, $this->val);
	}

	public function slice($start, $length = null){

		if(is_null($length))
			$length = $this->len();

		return new Str(substr($this->val, $start, $length));
	}

	public function toUpper(){

		return new Str(strtoupper($this->val));
	}

	public function toLower(){

		return new Str(strtolower($this->val));
	}

	/**
	* @link https://goo.gl/N4NsF5
	*/
	public function toSnake(){

		$pattern = "/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/";

		$newStr = new Str(preg_replace($pattern, "_", $this->val));
	
    	return $newStr->toLower();
	}

	public function toCamel($first = false){

		$newStr = implode("", array_map(function($part){

			return ucfirst($part);

		}, preg_split("/[_ ]/", $this->val)));

		return new Str($newStr);
	}

	/**
	* @link https://goo.gl/fj7W89
	*/
	public function startsWith($needle){

		$strNeedle = Str::create($needle);

	    $length = $strNeedle->len();

	    return $this->slice(0, $length)->equals($needle);
	}

	public function endsWith($needle){

		$strNeedle = Str::create($needle);

	    $length = $strNeedle->len();

	    if($length == 0)
	        return true;

	    return $this->slice(-$length)->equals($needle);
	}

	public function contains($needle){

		return strpos($this->val, $needle) !== false;
	}

	public function equals($str){

		$str = (string)$str;

		return $this->val === $str;
	}

	public function at($needle, $offset = null){

		if(is_null($offset))
			return strpos($this->val, $needle);

		return strpos($this->val, $needle, $offset);
	}

	/**
	* Opposite of (at) method
	*/
	public function startBackwardFindAt($needle, $offset = null){

		if(is_null($offset))
			return strrpos($this->val, $needle);

		return strrpos($this->val, $needle, $offset);
	}

	/**
	* @link https://goo.gl/2rBv3y
	*/
	public function btwn($from, $to){

		$sub = $this->slice($this->at($from) + Str::create($from)->len(), $this->len());

		return $sub->slice(0, $sub->at($to));
	}

	public function replace($search, $replace){

		return new Str(str_replace($search, $replace, $this->val));
	}

	public function replaceAt($replace, $start, $length = null){

		if(is_null($length))
			return new Str(substr_replace($this->val, $replace, $start));

		return new Str(substr_replace($this->val, $replace, $start, $length));
	}

	public function replaceFirst($search, $replace){

    	return new Str(preg_replace("/".$search."/", $replace, $this->val, 1));
	}

	/**
	* @link https://goo.gl/68KiQt
	*/
	public function replaceLast($search, $replace){

		/**
		* Opposite of (at) method
		*/
	    $pos = $this->startBackwardFindAt($search);

	    if($pos !== false)
	        return $this->replaceAt($replace, $pos, Str::create($search)->len());

	    return $this;
	}

	public function first($length){

		return $this->slice(0, $length);
	}

	public function last($length){

		return $this->slice($this->len()-$length, $this->len());
	}

	public function isEmpty(){

		return empty($this->val);
	}

	public function isRegEx($string) {

		return @preg_match($string, '') !== FALSE;
	}

	public function __toString(){

		return $this->val;
	}
}