<?php

namespace Strukt\Type;

use Strukt\Raise;
use Strukt\Contract\ValueObject;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Str extends ValueObject{

	protected $val;

	/**
	 * @param string $str
	 */
	public function __construct($str){

		if(!is_string($str))
			raise(sprintf("\Strukt\Type\Str object accepts strings only![%s] given.", (string)$str));

		parent::__construct($str);
	}

	/**
	 * @param string $str
	 * 
	 * @return static
	 */
	public static function create($str):static{

		return new self($str);
	}

	/**
	 * @param string str
	 * 
	 * @return static
	 */
	public function prepend(string $str):static{

		return new Str(sprintf("%s%s", $str, $this->val));
	}

	/**
	 * @param string $str
	 * 
	 * @return static
	 */
	public function concat(string $str):static{

		return new Str(sprintf("%s%s", $this->val, $str));
	}

	/**
	 * @return integer
	 */
	public function len():int{

		return strlen($this->val);
	}

	/**
	* Count the number of substring occurrences
	* 
	* @param string $needle
	* 
	* @return integer
	*/
	public function count(string $needle){

		return substr_count($this->val, $needle);
	}

	/**
	* Explode string
	* 
	* @param string $delimiter
	*/
	public function split(string $delimiter){

		return explode($delimiter, $this->val);
	}

	/**
	* Extracts parts of a string and returns the extracted parts in a new string
	* 
	* @param integer $start
	* @param integer $length
	* 
	* @return static
	*/
	public function slice(int $start, int|null $length = null):static{

		if(is_null($length))
			$length = $this->len();

		return $this->part($start, $length);
	}

	/**
	 * @return static
	 */
	public function toUpper():static{

		return new Str(strtoupper($this->val));
	}

	/**
	 * @return static
	 */
	public function toLower():static{

		return new Str(strtolower($this->val));
	}

	/**
	* @link https://goo.gl/N4NsF5
	* 
	* @return static
	*/
	public function toSnake():static{

		$pattern = "/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/";

		$str = new Str(preg_replace($pattern, "_", $this->val));
	
    	return $str->toLower();
	}

	/**
	 * @return static|string
	 */
	public function toCamel():static|string{

		$str = implode("", array_map(function($part){

			return ucfirst($part);

		}, preg_split("/[_ ]/", $this->val)));

		return new Str($str);
	}

	/**
	* @link https://goo.gl/fj7W89
	* 
	* @param string $needle
	* 
	* @return boolean
	*/
	public function startsWith(string $needle):bool{

		$oNeedle = Str::create($needle);

	    $length = $oNeedle->len();

	    return $this->slice(0, $length)->equals($needle);
	}

	/**
	 * @param string $needle
	 * 
	 * @return boolean
	 */
	public function endsWith(string $needle):bool{

		$oNeedle = Str::create($needle);

	    $length = $oNeedle->len();

	    if($length == 0)
	        return true;

	    return $this->slice(-$length)->equals($needle);
	}

	/**
	 * @param string $needle
	 * 
	 * @return boolean
	 */
	public function contains(string $needle):bool{

		return strpos($this->val, $needle) !== false;
	}

	/**
	 * @param static|string $str
	 * 
	 * @return boolean
	 */
	public function equals($str):bool{

		$str = (string)$str;

		return $this->val === $str;
	}

	/**
	 * @param string str
	 * 
	 * @return boolean
	 */
	public function notEquals(self|string $str):bool{

		return !$this->equals($str);
	}

	/**
	* Find the position of the first occurrence of a substring in a string
	* 
	* @param string $needle
	* @param $offset
	* 
	* @return integer|bool
	*/
	public function at(string $needle, ?int $offset = null):int|bool{

		if(is_null($offset))
			return strpos($this->val, $needle);

		return strpos($this->val, $needle, $offset);
	}

	/**
	* Opposite of (Strukt\Util\Str::at) method
	* 
	* @param string $needle
	* @param integer $offset
	* 
	* @return integer|bool
	*/
	public function startBackwardFindAt(string $needle, ?int $offset = null):int|bool{

		if(is_null($offset))
			return strrpos($this->val, $needle);

		return strrpos($this->val, $needle, $offset);
	}

	/**
	* Extract a substring between two characters in a string
	*
	* @link https://goo.gl/2rBv3y
	* 
	* @param string $from
	* @param string $to
	* 
	* @return static
	*/
	public function btwn(string $from, string $to):static{

		$sub = $this->slice($this->at($from) + Str::create($from)->len(), $this->len());

		return $sub->slice(0, $sub->at($to));
	}

	/**
	 * @param array|string $search
	 * @param array|string $replace
	 * 
	 * @return static
	 */
	public function replace(array|string $search, array|string $replace):static{

		return new Str(str_replace($search, $replace, $this->val));
	}

	/**
	* Replaces a copy of string delimited by the start and (optionally) 
	* length parameters with the string given in replacement
	* 
	* @param array|string $replace
	* @param array|string $start
	* @param integer $length
	*  
	* @return static
	*/
	public function replaceAt(array|string $replace, array|string $start, ?int $length = null):static{

		if(is_null($length))
			return new Str(substr_replace($this->val, $replace, $start));

		return new Str(substr_replace($this->val, $replace, $start, $length));
	}

	/**
	* Replace first occurence
	* 
	* @param array|string $search
	* @param array|string $replace
	* 
	* @return static
	*/
	public function replaceFirst(array|string $search, array|string $replace):static{

    	return new Str(preg_replace("/".$search."/", $replace, $this->val, 1));
	}

	/**
	* Replace last occurence
	*
	* @link https://goo.gl/68KiQt
	* 
	* @param array|string $search
	* @param array|string $replace
	* 
	* @return static
	*/
	public function replaceLast(array|string $search, array|string $replace):static{

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
	* 
	* @param integer $length
	* 
	* @return static
	*/
	public function first(int $length):static{

		return $this->slice(0, $length);
	}

	/**
	* Return part of string from end
	* 
	* @param integer $length
	* 
	* @return static
	*/
	public function last(int $length):static{

		return $this->slice($this->len()-$length, $this->len());
	}

	/**
	* Return part of a string
	* 
	* @param integer $start
	* @param integer $end
	* 
	* @return static
	*/
	public function part(int $start, int $end):static{

		return new Str(substr($this->val, $start, $end));
	}

	/**
	 * @return boolean
	 */
	public function empty():bool{

		return strlen($this->val) === substr_count($this->val, ' ') ? true : false;
	}

	/**
	 * @param string $string
	 * 
	 * @return boolean
	 */
	public function isRegEx(string $string):bool{

		return @preg_match($string, '') !== FALSE;
	}

	public function __toString(){

		return $this->val;
	}
}