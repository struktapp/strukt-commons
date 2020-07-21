<?php

namespace Strukt\Type;

use Strukt\Raise;

class Str extends \Strukt\Contract\ValueObject{

	public function __construct($str){

		if(!is_string($str))
			new Raise(sprintf("%s requires string!", static::class));

		parent::__construct($str);
	}

	public static function create(string $str){

		return new self($str);
	}

	public function prepend(string $str){

		return new Str(sprintf("%s%s", $str, $this->val));
	}

	public function concat(string $str){

		return new Str(sprintf("%s%s", $this->val, $str));
	}

	public function len(){

		return strlen($this->val);
	}

	/**
	* Count the number of substring occurrences
	*/
	public function count(string $needle){

		return substr_count($this->val, $needle);
	}

	/**
	* Explode string
	*/
	public function split(string $delimiter){

		return explode($delimiter, $this->val);
	}

	/**
	* Extracts parts of a string and returns the extracted parts in a new string
	*/
	public function slice(int $start, int $length = null){

		if(is_null($length))
			$length = $this->len();

		return $this->part($start, $length);
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

		$str = new Str(preg_replace($pattern, "_", $this->val));
	
    	return $str->toLower();
	}

	public function toCamel(){

		$str = implode("", array_map(function($part){

			return ucfirst($part);

		}, preg_split("/[_ ]/", $this->val)));

		return new Str($str);
	}

	/**
	* @link https://goo.gl/fj7W89
	*/
	public function startsWith(string $needle){

		$oNeedle = Str::create($needle);

	    $length = $oNeedle->len();

	    return $this->slice(0, $length)->equals($needle);
	}

	public function endsWith(string $needle){

		$oNeedle = Str::create($needle);

	    $length = $oNeedle->len();

	    if($length == 0)
	        return true;

	    return $this->slice(-$length)->equals($needle);
	}

	public function contains(string $needle){

		return strpos($this->val, $needle) !== false;
	}

	public function equals(string $str){

		$str = (string)$str;

		return $this->val === $str;
	}

	/**
	* Find the position of the first occurrence of a substring in a string
	*/
	public function at(string $needle, $offset = null){

		if(is_null($offset))
			return strpos($this->val, $needle);

		return strpos($this->val, $needle, $offset);
	}

	/**
	* Opposite of (Strukt\Util\Str::at) method
	*/
	public function startBackwardFindAt(string $needle, $offset = null){

		if(is_null($offset))
			return strrpos($this->val, $needle);

		return strrpos($this->val, $needle, $offset);
	}

	/**
	* Extract a substring between two characters in a string
	*
	* @link https://goo.gl/2rBv3y
	*/
	public function btwn(string $from, string $to){

		$sub = $this->slice($this->at($from) + Str::create($from)->len(), $this->len());

		return $sub->slice(0, $sub->at($to));
	}

	public function replace($search, $replace){

		return new Str(str_replace($search, $replace, $this->val));
	}

	/**
	* Replaces a copy of string delimited by the start and (optionally) 
	* length parameters with the string given in replacement
	*/
	public function replaceAt($replace, $start, $length = null){

		if(is_null($length))
			return new Str(substr_replace($this->val, $replace, $start));

		return new Str(substr_replace($this->val, $replace, $start, $length));
	}

	/**
	* Replace first occurence
	*/
	public function replaceFirst($search, $replace){

    	return new Str(preg_replace("/".$search."/", $replace, $this->val, 1));
	}

	/**
	* Replace last occurence
	*
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

	/**
	* Return part of string from beginning
	*/
	public function first(int $length){

		return $this->slice(0, $length);
	}

	/**
	* Return part of string from end
	*/
	public function last(int $length){

		return $this->slice($this->len()-$length, $this->len());
	}

	/**
	* Return part of a string
	*/
	public function part(int $start, int $end){

		return new Str(substr($this->val, $start, $end));
	}

	public function isEmpty(){

		return empty($this->val);
	}

	public function isRegEx(string $string) {

		return @preg_match($string, '') !== FALSE;
	}

	public function __toString(){

		return $this->val;
	}
}