<?php

use Strukt\Collection;
use Strukt\Registry;
use Strukt\Str;
use Strukt\Arr;
use Strukt\Today;
use Strukt\TokenQuery;
use Strukt\Stack;
use Strukt\Json;

helper("commons");

if(helper_add("dot")){

	function dot(string $ns, array &$map){

		if(!preg_match("/\./", $ns))
			if(!array_key_exists($ns, $map))
				return null;
		
		$parts = explode(".", $ns);
		$name = array_shift($parts);
		if(is_array($map[$name]) && !empty($parts))
			return dot(implode(".", $parts), $map[$name]);

		return $map[$name];
	}
}

if(helper_add("detach")){

	function detach(string $ns, array &$map){

		$parts = explode(".", $ns);
		$name = array_shift($parts);
		
		if(empty($parts)){

			$detach = $map[$name];
			unset($map[$name]);
			return $detach;
		}
		else return detach(implode(".", $parts), $map[$name]);
	}
}

if(helper_add("attach")){

	function attach(string $ns, array &$map, mixed $attach){

		$parts = explode(".", $ns);
		$name = array_shift($parts);
		if(!empty($parts)){

			if(!array_key_exists($name, $map)) $map[$name] = [];
			return attach(implode(".", $parts), $map[$name], $attach);
		}

		$map[$name] = $attach;
	}
}

if(helper_add("collect")){

	/**
	 * @param array $assoc
	 * 
	 * @return \Strukt\Collection
	 */
	function collect(array $assoc):Collection{

		return new Collection($assoc);
	}
}

if(helper_add("reg")){

	/**
	 * Global registry
	 * 
	 * @param string $key
	 * @param mixed $val
	 * 
	 * @return mixed
	 */
	function reg(?string $key = null, mixed $val = null):mixed{

		$reg = Registry::getInstance();
		if(!is_null($key) && !is_null($val))
			$reg->set($key, $val);

		if(!is_null($key) && is_null($val))
			return $reg->get($key);

		return $reg;
	}
}

if(helper_add("str")){

	/**
	 * @param string $str
	 * 
	 * return \Strukt\Str
	 */
	function str(string $str):Str{

		return new Str($str);
	}
}

if(helper_add("arr")){

	/**
	 * @param array $bundle
	 * 
	 * @return \Strukt\Arr
	 */
	function arr(array $bundle):Arr{

		return new class($bundle) extends Arr{

			protected $value;

			/**
			 * @param array $bundle
			 */
			public function __construct(array $bundle){

				$this->value = $bundle;
			}
		};
	}
}

if(helper_add("level")){

	function level(array $array, 
					int $target = 999999999, 
					int $current = 1, 
					string $prefix = ''):array|null{

	    $result = [];
	    foreach ($array as $key => $value){

	        $newKey = $prefix ? $prefix . '.' . $key : $key;
	        if (is_array($value) && $current < $target)
	            $result = array_merge($result, level($value, $target, $current+1, $newKey));
	        else $result[$newKey] = $value;
	    }

	    return $result;
	}
}

if(helper_add("token")){

	/**
	 * Example: $t = token("username:pitsolu|role:admin")->get("role");
	 * 			$t->get("role")
	 * 
	 * See also fn[tokenize]
	 * 
	 * @param string $token
	 * 
	 * @return \Strukt\TokenQuery
	 */
	function token(string $token):TokenQuery{

		return new TokenQuery($token);
	}
}

if(helper_add("tokenize")){

	/**
	 * Example: tokenize(["username"=>"pitsolu", "role"=>"admin"]);
	 * 
	 * @param array $parts
	 * 
	 * @return string
	 */
	function tokenize(array $parts):string{

		return arr($parts)->tokenize();
	}
}

if(helper_add("when")){

	/**
	 * @param string|integer $date
	 * 
	 * @return \DateTime
	 */
	function when(string|int $date = "now"):\DateTime{

		if(is_numeric($date))
			if(Strukt\DateTime::isTimestamp($date))
				return Strukt\DateTime::fromTimestamp($date);

		return new Strukt\DateTime($date);
	}
}

if(helper_add("period")){

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 */
	function period(?\DateTime $start = null, ?\DateTime $end = null){

		return new class($start, $end){

			/**
			 * @param \DateTime $start
	 	     * @param \DateTime $end
			 */
			public function __construct(?\DateTime $start = null, ?\DateTime $end = null){

				if(!is_null($start))
					$this->create($start, $end);
			}

			/**
			 * @param \DateTime $start
	 	     * @param \DateTime $end
	 	     * 
	 	     * @return static
			 */
			function create(\DateTime $start, ?\DateTime $end = null):static{

				if(is_null($end))
					$end = new DateTime("99999/12/31 00:00:00");

				Today::makePeriod($start, $end);

				return $this;
			}

			/**
			 * @param \DateTime $reset
			 * 
			 * @return static
			 */
			function reset(?\DateTime $reset = null):static{

				Today::reset($reset);

				return $this;
			}
		};
	}
}

if(helper_add("today")){

	/**
	 * @return \DateTime
	 */
	function today():\DateTime{

		return new Today();
	}
}

if(helper_add("format")){

	/**
	 * @param string $type
	 * @param mixed $val
	 * 
	 * @return @mixed
	 */
	function format(string $type, \Closure|string $val):mixed{

		if(!is_string($val))
			if(is_callable($val))
				reg(sprintf("format.%s", $type), $val);

		if(!is_callable($val))
			return reg(sprintf("format.%s", $type))($val);

		return null;
	}

	/**
	 * Date humanize
	 * Example: format("humanize", when("today"))
	 * 
	 * @param \DateTime|string $date
	 * 
	 * @return string
	 */
	format("humanize", function(\DateTime|string $date):string{

		$format = "Y-m-d H:i:s";
		if($date instanceof \DateTime)
			$date = $date->format($format);

		return @when($date)->when();
	});

	/**
	 * Date formatting
	 * Example: format("date", when("today"))
	 * 
	 * @param \DateTime|string $date
	 * 
	 * @return string
	 */
	format("datetime", function(\DateTime|string $date):string{

		$format = "Y-m-d H:i:s";
		if(is_string($date))
			if(strtotime($date))
				$date = new \DateTime($date);

		return $date->format($format);
	});	

	/**
	 * Date formatting
	 * Example: format("date", when("today"))
	 * 
	 * @param \DateTimeInterface|string $date
	 * 
	 * @return string
	 */
	format("date", function(string|\DateTimeInterface $date):string{

		$format = "Y-m-d";
		if(is_string($date))
			if(strtotime($date))
				$date = new \DateTime($date);

		return $date->format($format);
	});	
}

if(helper_add("stack")){

	/**
	 * @param string|int|null $message
	 * 
	 * @return \Strukt\Stack
	 */
	function stack(string|int|null $message = null):Stack{

		return new class($message) extends Stack{

			public function __construct(string $message){

				if(!is_null($message))
					parent::__construct($message);
			}
		};
	}
}

if(helper_add("json")){

	/**
	 * @param string|array $obj
	 */
	function json(string|array $obj){

		return new class($obj){

			private $obj;

			/**
			 * @param string|array $obj
			 */
			public function __construct(string|array $obj){

				if(is_array($obj))
					$obj = Json::encode($obj);

				$this->obj = $obj;
			}

			/**
			 * Pretty Print
			 * 
			 * @return string
			 */
			public function pp():string{

				return Json::pp($this->obj);
			}

			/**
			 * @return array
			 */
			public function decode():array{

				return Json::decode($this->obj);
			}

			/**
			 * @return string
			 */
			public function encode():string{

				return $this->obj;
			}

			/**
			 * Is Json valid
			 * 
			 * @return boolean
			 */
			public function valid():bool{

				return Json::isJson($this->obj);
			}
		};
	}
}

if(helper_add("app")){


	/**
	 * App Dependency Injection 
	 * 
	 * @param string $name
	 * @param ?array $classes - can only be array|string
	 * 
	 * @return mixed
	 */
	function app(string $name, ?array $classes = null){

		$abbrv = null;
		if(preg_match("/^\w+\.\w+$/", $name))
			list($name, $abbrv) = str($name)->split(".");

		if(is_null(alias($name)))
			raise(sprintf("Alias[%s] not found!", $name));

		if(negate(interface_exists(alias($name))))
			raise(sprintf("Interface for[alias.%s] does not exists!", $name));

		if(notnull($classes)){

			$classls = arr($classes);
			$success = $classls->each(fn($k, $class)=>in_array(alias($name),class_implements($class)));
			if(negate($success->product())){

				$failures = arr($success->filter(fn($k,$v)=>$v==false)->keys())->join(",");
				raise(sprintf("Incompatible class(es) app({%s[%s]})!", $name, $failures));
			}

			reg(sprintf("app.%s", $name), $classes);
		}

		if(is_null($classes)){

			if(notnull($abbrv)){

				$name = sprintf("%s.%s", $name, $abbrv);
				return reg(sprintf("app.%s", $name));
			}

			$classes = reg(sprintf("app.%s", $name));
			return array_shift($classes);
		}
	}
}

if(helper_add("config")){

	/**
	 * Global configuration
	 * 
	 * @param string $key
	 * @param array|string|int|null $options
	 * 
	 * @return mixed
	 */
	function config(string $key, array|string|int|null $options = null):mixed{

		if(!reg()->exists("config"))
			if(fs()->isDir(phar("cfg")->adapt())){

				foreach(fs(phar("cfg")->adapt())->ls() as $ini_file)
					if(negate(str($ini_file)->endsWith("~")))
						$configs[trim($ini_file, ".ini")] = fs(phar("cfg")->adapt())->ini($ini_file);

				reg("config", $configs);
			}

		if(!is_null($options))
			reg(sprintf("config.%s", $key), $options);

		$config = collect(reg("config"));
		if($config->exists($key))
			return $config->get($key);

		return null;
	}
}