<?php

namespace Strukt\Core;

use Strukt\Contract\DateRange;
use Strukt\Raise;

class Today extends DateRange{

	private static $start;
	private static $end;
	private static $today;

	public function __construct(){
 
		if(is_null(static::$today))
			static::$today = new \DateTime();

		parent::__construct(static::$today->format("Y-m-d H:i:s.u"));

		if(static::hasRange()){

			if(!$this->btwn(static::$start, static::$end))	
				new Raise(sprintf("Invalid date [%s] btwn [%s to %s]!", 
							static::$today->format("Y-m-d H:i:s"),
							static::$start->format("Y-m-d H:i:s"),
							static::$end->format("Y-m-d H:i:s")));
		}
	}

	public static function getState(){

		return array("today"=>static::$today,
						"start_date"=>static::$start,
						"end_date"=>static::$end);
	}

	public static function reset(\DateTime $date = null){

		if(is_null($date)){

			static::$start = null;
			static::$end = null;
		}		
		
		static::$today = $date;
	}

	public static function validBtwn(\DateTime $start, \DateTime $end){

		static::$start = new class($start) extends DateRange{

			public function __construct(\DateTime $start_date){

				parent::__construct($start_date->format("Y-m-d H:i:s.u"));
			}
		};

		if(!static::$start->lte($end))
			new Raise(sprintf("end_date [%s] must be later than start_date[%s]!", 
								$start->format("Y-m-d H:i:s"),
								$end->format("Y-m-d H:i:s")));

		static::$end = $end;
	}

	public static function hasRange(){

		return !empty(static::$start) && !empty(static::$end);
	}

	public function withDate(\DateTime $date){

		if(!static::hasRange())
			new Raise("Period not set!");

		return new class($date, static::$start, static::$end) extends DateRange{

			private $date;
			private $start;
			private $end;
			private $valid;

			public function __construct(\DateTime $date, \DateTime $start, \DateTime $end){

				$this->date = $date;
				$this->start = $start;
				$this->end = $end;
				$this->valid = null;

				parent::__construct($date->format("Y-m-d H:i:s.u"));
			}

			public function useRange(){

				$this->valid = $this->btwn($this->start, $this->end);

				return $this;
			}

			public function isValid(){

				if(is_null($this->valid))
					new Raise("Must priorly call @anonymous::useRange in chain!");

				return $this->valid;
			}

			public function __destruct(){

				$this->valid = null;
			}
		};
	}
}