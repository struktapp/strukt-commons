<?php

namespace Strukt\Contract;

abstract class DateCompare extends \DateTime{

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
}