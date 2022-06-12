<?php

namespace Strukt\Type;

use Strukt\Core\Today;
use Strukt\Contract\DateRange;
use Strukt\Raise;

class DateTime extends DateRange{

	private $format;

	public function __construct($datetime="", string $format=""){

		$now = new Today();
		if(empty($datetime))
			$datetime = $now;

		if(!empty($format) && is_string($datetime))
			$datetime = \DateTime::createFromFormat($format, $datetime);

		if(Today::hasPeriod()){

			extract(Today::getState());

			if(is_string($datetime))
				$datetime = new \DateTime($datetime);

			if(!$now->withDate($datetime)->isValid())
				new Raise(sprintf("Date [%s] not within [%s:range]",
									$datetime->format("Y-m-d H:i:s"),
									json_encode(array(

										"today"=>$today->format("Y-m-d H:i:s"),
										"start_date"=>$start_date->format("Y-m-d H:i:s"),
										"end_date"=>$end_date->format("Y-m-d H:i:s"),
									))));
		}

		if(is_object($datetime))
			$datetime = $datetime->format("Y-m-d H:i:s.u");

		$this->format = $format;

		parent::__construct($datetime);
	}

	public static function create(string $datetime, string $format="Y-m-d"){

		return new self(\DateTime::createFromFormat($format, $datetime));
	}

	public static function fromTimestamp($timestamp){

		return (new self)->setTimestamp($timestamp);
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
	    
	    return sprintf("%s ago", implode(', ', $string));
	}

	public function reset(){

		$this->setTime(00,00,00,000000);
	}

	public function last(){

		$this->setTime(23,59,59,000000);
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