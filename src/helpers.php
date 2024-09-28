<?php

use Strukt\Builder\Collection as CollectionBuilder;
use Strukt\Core\Collection;
use Strukt\Core\Map;
use Strukt\Core\Today;
use Strukt\Contract\AbstractArr;
use Strukt\Type\Arr;
use Strukt\Type\Json;
use Strukt\Env;
use Strukt\Raise;
use Strukt\Cache\Cache;

helper("commons");

if(helper_add("collect")){

	function collect(array $assoc){

		return CollectionBuilder::create()->fromAssoc($assoc);
	}
}


if(helper_add("map")){

	function map(array $assoc){

		return new Map(collect($assoc));
	}
}

if(helper_add("arr")){

	function arr(array $bundle){

		return new class($bundle) extends \Strukt\Contract\Arr{

			protected $val;

			public function __construct(array $bundle){

				$this->val = $bundle;
			}

			public function level(){

				return Arr::level($this->val);
			}
		};
	}
}

if(helper_add("reg")){

	function reg(string $key = null, mixed $val = null){

		$reg = Strukt\Core\Registry::getSingleton();
		if(!is_null($key) && !is_null($val))
			$reg->set($key, $val);

		if(!is_null($key) && is_null($val))
			return $reg->get($key);

		return $reg;
	}
}

if(helper_add("config")){

	function config(string $key, array|string $options = null){

		if(!reg()->exists("config"))
			if(fs()->isDir(phar("cfg")->adapt())){

				foreach(fs(phar("cfg")->adapt())->ls() as $ini_file)
					$configs[trim($ini_file, ".ini")] = fs(phar("cfg")->adapt())->ini($ini_file);

				reg("config", $configs);
				
				if(reg("config")->exists("app")){

					$app_name = reg("config.app")->get("app-name");
					reg("config.app")->remove("app-name");
					reg("config.app")->set("name", $app_name);
				}
			}

		$nkey = sprintf("config.%s", rtrim($key, "*"));
		if(str($key)->endsWith("*"))
			return arr(array_flip(reg($nkey)->keys()))->each(function($k, $v) use($nkey){

				return reg($nkey)->get($k);

			})->yield();

		if(!is_null($options))
			reg(sprintf("config.%s", $key), $options);

		if(reg("config")->exists($key))
			return reg("config")->get($key);

		return null;
	}
}

if(helper_add("cache")){

	function cache(string $filename, string|array $val = null){
		
		if(preg_match("/\./", $filename)){

			$arr = arr(str($filename)->split("."));
			$filename = $arr->dequeue();
			$key = $arr->concat(".");

			$cache = Cache::make($filename);
			if(!is_null($val))
				return $cache->put($key, $val);

			return $cache->get($key);
		}

		return new Cache($filename);
	}
}

if(helper_add("raise")){

	function raise($error, $code = 500){

		return new Raise($error, $code);
	}
}

if(helper_add("token")){

	function token(string $token){

		return new \Strukt\Core\TokenQuery($token);
	}
}

if(helper_add("tokenize")){

	function tokenize(array $parts){

		return arr($parts)->tokenize();
	}
}

if(helper_add("str")){

	function str(string $str){

		return new \Strukt\Type\Str($str);
	}
}

if(helper_add("when")){

	function when(string|int $date = "now"){

		if(is_numeric($date))
			if(\Strukt\Type\DateTime::isTimestamp($date))
				return \Strukt\Type\DateTime::fromTimestamp($date);

		return new \Strukt\Type\DateTime($date);
	}
}

if(helper_add("period")){

	function period(\DateTime $start = null, \DateTime $end = null){

		return new class($start, $end){

			public function __construct(\DateTime $start = null, \DateTime $end = null){

				if(!is_null($start))
					$this->create($start, $end);
			}

			function create(\DateTime $start, \DateTime $end = null){

				if(is_null($end))
					$end = new DateTime("99999/12/31 00:00:00");

				Today::makePeriod($start, $end);

				return $this;
			}

			function reset(\DateTime $reset = null){

				Today::reset($reset);

				return $this;
			}
		};
	}
}

if(helper_add("today")){

	function today(){

		return new Today();
	}
}

if(helper_add("format")){

	function format(string $type, $mixed = null){

		if(is_callable($mixed))
			return event(sprintf("format.%s", $type), $mixed);

		return event(sprintf("format.%s", $type))->apply($mixed)->exec();
	}

	format("date", function(\DateTime $date){

		return $date->format("Y-m-d H:i:s");
	});	
}

if(helper_add("env")){

	function env(string $key, string|int|bool $val = null){

		if(!is_null($val))
			Env::set($key, $val);

		return Env::get($key);
	}
}

if(helper_add("json")){

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

			public function first(){

				$arr = arr($this->decode());
				if(negate($arr->isMap()))
					return $arr->current()->yield();

				return null;
			}

			public function has(mixed $val){

				if(!$this->valid())
					raise("Invalid JSON!");
				
				$obj = $this->decode($this->obj);

				return arr($obj)->has($val);
			}

			public function assert(string $key, callable $fn = null){

				if(!$this->valid())
					raise("Invalid JSON!");
				
				$obj = $this->decode($this->obj);

				if(array_key_exists($key, $obj)){

					$val = $obj[$key];
					if(is_callable($fn)){

						if(is_array($val))
							return $fn(json($val));
						
						return $fn(json($obj));
					}

					return true;
				}

				return false;
			}
		};
	}
}

if(helper_add("msg")){

	function msg(string|array|int $message = null){

		return new class($message) extends \Strukt\NoteList{

			public function __construct($message){

				if(!is_null($message))
					parent::__construct($message);
			}
		};
	}
}

if(helper_add("negate")){

	function negate(bool $any){

		return !$any;
	}
}

if(helper_add("notnull")){

	function notnull(mixed $var){

		return negate(is_null($var));
	}
}

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

if(helper_add("dd")){

	VarDumper::setHandler(function (mixed $var): void {
	    $cloner = new VarCloner();
	    $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper();

	    $dumper->dump($cloner->cloneVar($var));
	});
}