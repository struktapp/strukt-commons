<?php

namespace Strukt;

use Strukt\Event;
use Strukt\Type\Arr;

class Monad{

	private $result;
	private $params;
	private $params_assoc;

	public function __construct(array $params){

		$this->params_assoc = Arr::onlyAssoc($params);

		$this->params = $params;
	}

	public static function create(array $params){

		return new self($params);
	}

	private function withAssocParams(\Closure $step){

		$evt = Event::create($step);

		if(!empty($this->result)){

			$evtParams = $evt->getParams();

			$paramKey = array_key_first($evtParams);

			$this->params[$paramKey] = $this->result;
		}

		$this->result = $evt->applyArgs($this->params)->exec();
	}

	private function withNoAssocParams(\Closure $step){

		$evt = Event::create($step);

		$evtParams = $evt->getParams();

		$this->result = $evt->applyArgs($this->params)->exec();

		$this->params = array_slice($this->params, count($evtParams));

		array_unshift($this->params, $this->result);		
	}

	public function next(\Closure $step){

		if($this->params_assoc)
			$this->withAssocParams($step);
		else
			$this->withNoAssocParams($step);			

		return $this;
	}

	public function yield(){

		return $this->result;
	}
}