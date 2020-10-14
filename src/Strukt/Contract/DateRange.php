<?php

namespace Strukt\Contract;

abstract class DateRange extends DateCompare{

	public function rand(\DateTime $end){

		return $this->fromTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
	}

	public function btwn(\DateTime $start, \DateTime $end){

		return $this->gte($start) && $this->lte($end);
	}
}