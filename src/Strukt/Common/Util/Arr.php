<?php

namespace Strukt\Common\Util;

trait Arr{

	/**
	* Is Array Assoc source. Source: StackOverflow see link
	* @link https://goo.gl/k7L4eA
	*
	* @param array $arr
	*
	* @return bool
	*/
	public function isAssoc(array $arr){

	    if (array() === $arr) 
	    	return false;

	    return array_keys($arr) !== range(0, count($arr) - 1);
	}
}