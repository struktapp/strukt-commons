<?php

namespace Strukt\Contract;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
abstract class DateCompare extends \DateTime{

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function gte(\DateTime $to){

		return $this->getTimestamp() >= $to->getTimestamp();
	}

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function gt(\DateTime $to){

		return $this->getTimestamp() > $to->getTimestamp();
	}

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function lte(\DateTime $to){

		return $this->getTimestamp() <= $to->getTimestamp();
	}

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function lt(\DateTime $to){

		return $this->getTimestamp() < $to->getTimestamp();
	}

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function equals(\DateTime $to){

		return $this->getTimestamp() == $to->getTimestamp();
	}

	/**
	 * @param \DateTime $to
	 * 
	 * @return bool
	 */
	public function same(\DateTime $to){

		$date = $this->format("Y-m-d");
		$toDate = $to->format("Y-m-d");

		return $date == $toDate;
	}
}