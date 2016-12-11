<?php

namespace Strukt\Common\Util;

trait Str{

	/**
	* @link https://goo.gl/fj7W89
	*/
	public function startsWith($haystack, $needle){

	     $length = strlen($needle);

	     return (substr($haystack, 0, $length) === $needle);
	}

	public function endsWith($haystack, $needle){

	    $length = strlen($needle);

	    if ($length == 0) {
	        return true;
	    }

	    return (substr($haystack, -$length) === $needle);
	}

	public function contains($hastack, $needle){

		return strpos($hastack, $needle) !== false;
	}
}