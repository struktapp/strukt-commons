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

		static::$today = static::reMake();

		parent::__construct(static::$today->format("Y-m-d H:i:s.u"));

		if(static::hasPeriod()){

			if(!$this->btwn(static::$start, static::$end))	
				new Raise(sprintf("Invalid date [%s] btwn [%s to %s]!", 
							static::$today->format("Y-m-d H:i:s"),
							static::$start->format("Y-m-d H:i:s"),
							static::$end->format("Y-m-d H:i:s")));
		}
	}

	private static function reMake(){

		$now = new \DateTime;
		$time = $now->format("H:i:s.u");

		if(is_null(static::$today))
			new Raise("Today must be set!");

		return new \DateTime(sprintf("%s %s", static::$today->format("Y-m-d"), $time));
	}

	public static function getState(){

		return array(
			
			"today"=>static::reMake(),
			"start_date"=>static::$start,
			"end_date"=>static::$end
		);
	}

	public static function reset(\DateTime $date = null){

		if(is_null($date)){

			static::$start = null;
			static::$end = null;
		}		
		
		static::$today = $date;
	}

	public static function makePeriod(\DateTime $start, \DateTime $end){

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

	public static function hasPeriod(){

		return !empty(static::$start) && !empty(static::$end);
	}

	public static function withDate(\DateTime $date){

		if(!static::hasPeriod())
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

				$this->valid = $this->btwn($this->start, $this->end);
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