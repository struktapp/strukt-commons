<?php

namespace Strukt\Core;

use Strukt\Contract\DateRange;
use Strukt\Raise;

/**
 * @author Moderator <pitsolu@gmail.com>
*/
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

	/**
	 * @return \DateTime
	 */
	private static function reMake():\DateTime{

		$now = new \DateTime;
		$time = $now->format("H:i:s.u");

		if(is_null(static::$today))
			new Raise("Today must be set!");

		return new \DateTime(sprintf("%s %s", static::$today->format("Y-m-d"), $time));
	}

	/**
	 * @param string $state
	 * 
	 * @return \DateTime|array
	 */
	public static function getState(?string $state = null):\DateTime|array{

		switch ($state) {
			case 'period.start':
					return static::$start;
				break;
			case 'period.end':
					return static::$end;
				break;
			default:
					return array(
			
						"today"=>static::reMake(),
						"start_date"=>static::$start,
						"end_date"=>static::$end
					);
				break;
		};
	}

	/**
	 * @param \DateTime $date
	 * 
	 * @return void
	 */
	public static function reset(?\DateTime $date = null):void{

		if(is_null($date)){

			static::$start = null;
			static::$end = null;
		}		
		
		static::$today = $date;
	}

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * 
	 * @return void
	 */
	public static function makePeriod(\DateTime $start, \DateTime $end):void{

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

	/**
	 * @return bool
	 */
	public static function hasPeriod():bool{

		return !empty(static::$start) && !empty(static::$end);
	}

	/**
	 * @param \DateTime $date
	 * 
	 * @return \Strukt\Contract\DateRange
	 */
	public static function withDate(\DateTime $date):DateRange{

		if(!static::hasPeriod())
			new Raise("Period not set!");

		return new class($date, static::$start, static::$end) extends DateRange{

			private $date;
			private $start;
			private $end;
			private $valid;

			/**
			 * @param \DateTime $date
			 * @param \DateTime $start
			 * @param \DateTime $end
			 */
			public function __construct(\DateTime $date, \DateTime $start, \DateTime $end){

				$this->date = $date;
				$this->start = $start;
				$this->end = $end;
				$this->valid = null;

				parent::__construct($date->format("Y-m-d H:i:s.u"));

				$this->valid = $this->btwn($this->start, $this->end);
			}

			/**
			 * @return bool
			 */
			public function isValid():bool{

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