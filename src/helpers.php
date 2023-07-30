<?php

use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Core\Collection;
use Strukt\Core\Today;

if(!function_exists("bunch")){

	function bunch(array $assoc){

		return CollectionBuilder::create(new Collection())->fromAssoc($assoc);
	}
}

if(!function_exists("band")){

	function band(array $bundle){

		return new \Strukt\Type\Arr($bundle);
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