<?php

namespace Strukt\Type;

use Strukt\Core\Today;
use Strukt\Contract\DateRange;
use Strukt\Raise;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class DateTime extends DateRange{

	private $format;

	/**
	 * @param $datetime
	 * @param string $format
	 */
	public function __construct(\DateTime|string $datetime="", string $format=""){

		$now = new Today();
		if(empty($datetime))
			$datetime = $now;

		if(!empty($format) && is_string($datetime))
			$datetime = \DateTime::createFromFormat($format, $datetime);

		if(Today::hasPeriod()){

			extract(Today::getState());

			if(is_string($datetime))
				$datetime = new \DateTime($datetime);

			if(!$now->withDate($datetime)->isValid())
				new Raise(sprintf("Date [%s] not within [%s:range]",
									$datetime->format("Y-m-d H:i:s"),
									json_encode(array(

										"today"=>$today->format("Y-m-d H:i:s"),
										"start_date"=>$start_date->format("Y-m-d H:i:s"),
										"end_date"=>$end_date->format("Y-m-d H:i:s"),
									))));
		}

		if(is_object($datetime))
			$datetime = $datetime->format("Y-m-d H:i:s.u");

		$this->format = $format;

		parent::__construct($datetime);
	}

	/**
	 * @param string $datetime
	 * @param string $format 
	 */
	public static function create(string $datetime, string $format="Y-m-d"){

		return new self(\DateTime::createFromFormat($format, $datetime));
	}

	/**
	 * @param integer $timestamp
	 * 
	 * @return static
	 */
	public static function fromTimestamp(int $timestamp):static{

		return (new self)->setTimestamp($timestamp);
	}

	/**
	 * @param int $timestamp
	 * 
	 * @return boolean
	 */
	public static function isTimestamp(int $timestamp):bool{

	    try {

	        new \DateTime(sprintf('@%d', $timestamp));
	    } 
	    catch(Exception $e) {

	        return false;
	    }

	    return true;
	}

	/**
	 * @param $full
	 * 
	 * @link https://github.com/ramphor/date-human-readable
	 *  
	 * use Ramphor\Date\HumanReadable;

	 * HumanReadable::parse(new DateTime('now'));         // Moments ago
	 * HumanReadable::parse(new DateTime('+ 59 second')); // Seconds from now
	 * HumanReadable::parse(new DateTime('+ 1 minute'));  // In 1 minute
	 * HumanReadable::parse(new DateTime('- 59 minute')); // 59 minutes ago

	 * // You can supply a secondary argument to provide an alternate reference
	 * // DateTime. The default is the current DateTime, ie: DateTime('now'). In
	 * // addition, it takes into account the day of each DateTime. So in the next
	 * // two examples, even though they're only a second apart, 'Yesterday' and
	 * // 'Tomorrow' will be displayed

	 * $now = new DateTime('1991-05-18 00:00:00 UTC');
	 * $dateTime = new DateTime('1991-05-17 23:59:59 UTC');
	 * HumanReadable::parse($dateTime, $now); // Yesterday

	 * $now = new DateTime('1991-05-17 23:59:59 UTC');
	 * $dateTime = new DateTime('1991-05-18 00:00:00 UTC');
	 * HumanReadable::parse($dateTime, $now) // Tomorrow
	 */
	public function when($full = null){

		$now = new \DateTime();
		if(!is_null($full))
			$now = new \DateTime($full);

		return \Ramphor\Date\HumanReadable::parse($this, $now);
	}

	/**
	 * @return void
	 */
	public function reset():void{

		$this->setTime(00,00,00,000000);
	}

	/**
	 * @return void
	 */
	public function last():void{

		$this->setTime(23,59,59,000000);
	}

	/**
	 * @param $how
	 */
	public function clone($how = null):static{

		$modified = clone $this;

		if(!is_null($how))
	 		$modified->modify($how);

	 	return $modified;
	}

	public function __toString(){

		return $this->format($this->format);
	}
}