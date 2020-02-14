<?php

namespace Strukt\Util;

class DateTime extends \DateTime{

	private $format;

	public function __construct($datetime="", $format="Y-m-d H:i:s.u"){

		$this->format = $format;

		if($datetime instanceof \DateTime)
			$datetime = $datetime->format($format);

		parent::__construct($datetime);
	}

	public function rand(\DateTime $end){

		$this->setTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
	}

	/**
	* http://bit.ly/2Ssd0RB
	*/
	public function when($full = null){

		$today = new DateTime();
	    $diff = $this->diff($today);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(

			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
	     
	    foreach ($string as $k => &$v){

	    	if($diff->$k){

	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } 
	        else{
	            
	            unset($string[$k]);
	        }
	    }

	    $prefix = substr($full, 0, 7);

	    if(is_null($full) || $prefix != "clearer"){

	     	$string = array_slice($string, 0, 1);
	    }
	    elseif($prefix == "clearer"){

	    	$len = strlen($full);
	    	$others = substr($full, 5, $len);

	    	$string = array_slice($string, 0, strlen(strchr($full, "r")));
		}

	    if($this->gt($today))
	     	return sprintf("in %s", implode(', ', $string));
	    else
	     	return sprintf("%s ago", implode(', ', $string));
	}

	public function gte(\DateTime $to){

		return $this->getTimestamp() >= $to->getTimestamp();
	}

	public function gt(\DateTime $to){

		return $this->getTimestamp() > $to->getTimestamp();
	}

	public function lte(\DateTime $to){

		return $this->getTimestamp() <= $to->getTimestamp();
	}

	public function lt(\DateTime $to){

		return $this->getTimestamp() < $to->getTimestamp();
	}

	public function equals(\DateTime $to){

		return $this->getTimestamp() == $to->getTimestamp();
	}

	public function reset(){

		$this->setTime(00,00,00,000000);
	}

	public function last(){

		$this->setTime(23,59,59,1000000);
	}

	public function clone($how = null) {

		$modified = clone $this;

		if(!is_null($how))
	 		$modified->modify($how);

	 	return $modified;
	}

	public function __toString(){

		return $this->format($this->format);
	}
}