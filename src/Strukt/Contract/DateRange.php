<?php

namespace Strukt\Contract;

abstract class DateRange extends \DateTime{

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

	public function rand(\DateTime $end){

		return $this->fromTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
	}

	public function btwn(\DateTime $start, \DateTime $end){

		return $this->gte($start) && $this->lte($end);
	}
}