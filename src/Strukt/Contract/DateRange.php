<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class DateRange extends DateCompare{

	/**
	 * @param \DateTime $end
	 */
	public function rand(\DateTime $end){

		return $this->fromTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
	}

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * 
	 * @return boolean
	 */
	public function btwn(\DateTime $start, \DateTime $end){

		return $this->gte($start) && $this->lte($end);
	}
}