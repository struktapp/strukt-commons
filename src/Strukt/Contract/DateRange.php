<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class DateRange extends DateCompare{

	/**
	 * @param \DateTime $end
	 */
	public function rand(\DateTime $end):static{

		return $this->fromTimestamp(rand($this->getTimestamp(), $end->getTimestamp()));
	}

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * 
	 * @return boolean
	 */
	public function btwn(\DateTime $start, \DateTime $end):bool{

		return $this->gte($start) && $this->lte($end);
	}
}