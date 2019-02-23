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

	public function toMakeRand(\DateTime $end){

		$this->setTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
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

	public function beginDay(){

		$this->setTime(00,00,00,000000);
	}

	public function endDay(){

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