<?php

use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Core\Collection;
use Strukt\Core\Today;
use Strukt\Contract\AbstractArr;
use Strukt\Type\Arr;
use Strukt\Type\Json;
use Strukt\Env;

if(!function_exists("collect")){

	function collect(array $assoc){

		return CollectionBuilder::create(new Collection())->fromAssoc($assoc);
	}
}

if(!function_exists("arr")){

	function arr(array $bundle){

		return new class($bundle) extends \Strukt\Contract\AbstractArrOps{

			protected $val;

			public function __construct(array $bundle){

				$this->val = $bundle;
			}

			/**
			* Is array fully associative
			*/
			public function isMap(){

				return AbstractArr::isMap($this->val);
			}


			public function level(){

				return Arr::level($this->val);
			}
		};
	}
}

if(!function_exists("token")){

	function token(string $token){

		return new \Strukt\Core\TokenQuery($token);
	}
}

if(!function_exists("str")){

	function str(string $str){

		return new \Strukt\Type\Str($str);
	}
}

if(!function_exists("when")){

	function when(string $str = "now"){

		return new \Strukt\Type\DateTime($str);
	}
}

if(!function_exists("period")){

	function period(){

		return new class(){

			function create(\DateTime $start, \DateTime $end){

				Today::makePeriod($start, $end);

				return $this->reset($start);
			}

			function reset(\DateTime $reset = null){

				Today::reset($reset);

				return $this;
			}
		};
	}
}

if(!function_exists("today")){

	function today(){

		return new Today();
	}
}

if(!function_exists("env")){

	function env(string $key, string|array|int $val = null){

		if(!empty($val) && !is_null($val))
			Env::set($key, $val);

		return Env::get($key);
	}
}

if(!function_exists("reg")){

	function reg(string $key = null){

		$reg = Strukt\Core\Registry::getSingleton();
		if(!is_null($key))
			return $reg->get($key);

		return $reg;
	}
}

if(!function_exists("json")){

	function json(string|array $obj){

		return new class($obj){

			private $obj;

			public function __construct($obj){

				if(is_array($obj))
					$obj = Json::encode($obj);

				$this->obj = $obj;
			}

			public function pp(){

				return Json::pp($this->obj);
			}

			public function decode(){

				return Json::decode($this->obj);
			}

			public function encode(){

				return $this->obj;
			}

			public function valid(){

				return Json::isJson($this->obj);
			}
		};
	}
}