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
use Strukt\Contract\Arr as ArrContract;
use Strukt\Core\Registry;
use Strukt\Core\TokenQuery;
use Strukt\Type\Str;
use Strukt\Type\DateTime as StruktDateTime;

helper("commons");

if(helper_add("collect")){

	/**
	 * @param array $assoc
	 * 
	 * @return \Strukt\Core\Collection
	 */
	function collect(array $assoc):Collection{

		return CollectionBuilder::create()->fromAssoc($assoc);
	}
}


if(helper_add("map")){

	/**
	 * @param array $assoc
	 * 
	 * @return \Strukt\Core\Map
	 */
	function map(array $assoc):Map{

		return new Map(collect($assoc));
	}
}

if(helper_add("arr")){

	/**
	 * @param array $bundle
	 * 
	 * @return \Strukt\Contract\Arr
	 */
	function arr(array $bundle):ArrContract{

		return new class($bundle) extends ArrContract{

			protected $val;

			/**
			 * @param array $bundle
			 */
			public function __construct(array $bundle){

				$this->val = $bundle;
			}

			/**
			 * @return array
			 */
			public function level():array{

				return Arr::level($this->val);
			}
		};
	}
}

if(helper_add("reg")){

	/**
	 * @param string $key
	 * @param mixed $val
	 * 
	 * @return mixed
	 */
	function reg(?string $key = null, mixed $val = null):mixed{

		$reg = Registry::getSingleton();
		if(!is_null($key) && !is_null($val))
			$reg->set($key, $val);

		if(!is_null($key) && is_null($val))
			return $reg->get($key);

		return $reg;
	}
}

if(helper_add("config")){

	/**
	 * @param string $key
	 * @param mixed $options - can only be array|string
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

	/**
	 * @param string $filename
	 * @param mixed $val - can only be string|array
	 * 
	 * @return mixed
	 */
	function cache(string $filename, array|string|null $val = null):mixed{
		
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

	/**
	 * @param string $error
	 * @param integer $code
	 * 
	 * @return \Strukt\Raise
	 */
	function raise(string $error, int $code = 500):Raise{

		return new Raise($error, $code);
	}
}

if(helper_add("token")){

	/**
	 * @param string $token
	 * 
	 * @return \Strukt\Core\TokenQuery
	 */
	function token(string $token):TokenQuery{

		return new TokenQuery($token);
	}
}

if(helper_add("tokenize")){

	/**
	 * @param array $parts
	 * 
	 * @return string
	 */
	function tokenize(array $parts):string{

		return arr($parts)->tokenize();
	}
}

if(helper_add("str")){

	/**
	 * @param string $str
	 * 
	 * return \Strukt\Type\Str
	 */
	function str(string $str):Str{

		return new Str($str);
	}
}

if(helper_add("when")){

	/**
	 * @param string|integer $date
	 * 
	 * @return \Strukt\Type\DateTime
	 */
	function when(string|int $date = "now"){

		if(is_numeric($date))
			if(StruktDateTime::isTimestamp($date))
				return StruktDateTime::fromTimestamp($date);

		return new StruktDateTime($date);
	}
}

if(helper_add("period")){

	/**
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * 
	 * @return object
	 */
	function period(?\DateTime $start = null, ?\DateTime $end = null):object{

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
	 * @return \Strukt\Core\Today
	 */
	function today(){

		return new Today();
	}
}

if(helper_add("timezone")){

	function timezone(?string $locale = null){

		$timezone = ini_get("date.timezone");
		if($timezone == "UTC" && notnull($locale))
			ini_set("date.timezone", $locale);

		return $locale ?? $timezone;
	}
}

if(helper_add("format")){

	/**
	 * @param string $type
	 * @param mixed $val
	 * 
	 * @return @mixed
	 */
	function format(string $type, mixed $val = null):mixed{

		if(is_callable($val))
			return event(sprintf("format.%s", $type), $val);

		return event(sprintf("format.%s", $type))->apply($val)->exec();
	}

	/**
	 * Date formatting
	 *  Example: format("date", when("today"))
	 * 
	 * @param \DateTime $date
	 * 
	 * @return string
	 */
	format("date", function(\DateTime $date):string{

		return $date->format("Y-m-d H:i:s");
	});	
}

if(helper_add("env")){

	/**
	 * @param string $key
	 * @param mixed $val - can only be string|int|bool
	 * 
	 * @return string
	 */
	function env(string $key, int|string|bool|null $val = null):string{

		if(!is_null($val))
			Env::set($key, $val);

		return Env::get($key);
	}
}

if(helper_add("json")){

	/**
	 * @param string|array $obj
	 * 
	 * @return object
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
			 * @return boolean
			 */
			public function valid():bool{

				return Json::isJson($this->obj);
			}

			/**
			 * @return mixed
			 */
			public function first():mixed{

				$arr = arr($this->decode());
				if(negate($arr->isMap()))
					return $arr->current()->yield();

				return null;
			}

			/**
			 * @param mixed $val
			 * 
			 * @return bool
			 */
			public function has(mixed $val):bool{

				if(!$this->valid())
					raise("Invalid JSON!");
				
				$obj = $this->decode($this->obj);

				return arr($obj)->has($val);
			}

			/**
			 * @param string $key
			 * @param callable $fn
			 * 
			 * @return boolean
			 */
			public function assert(string $key, ?callable $fn = null){

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

	/**
	 * @param string|int|null $message
	 * 
	 * @return object
	 */
	function msg(string|int|null $message = null):\Strukt\NoteList{

		return new class($message) extends \Strukt\NoteList{

			public function __construct($message){

				if(!is_null($message))
					parent::__construct($message);
			}
		};
	}
}

if(helper_add("negate")){

	/**
	 * @param boolean $any
	 * 
	 * @return boolean
	 */
	function negate(bool $any){

		return !$any;
	}
}

if(helper_add("notnull")){

	/**
	 * @param mixed $var
	 * 
	 * @return boolean
	 */
	function notnull(mixed $var){

		return negate(is_null($var));
	}
}

if(helper_add("ini")){

	/**
	 * @param mixed $file
	 * 
	 * @return object
	 */
	function ini(mixed $file):object{

		return new class($file){

			private $file;
			private $oFile; // original
			private $nFile; // new
			private $dFile; // diff
			private $ini;

			/**
			 * @param string $file
			 */
			public function __construct(string $file){

				$this->file = $file;
				$this->oFile = fs()->ini($file);
				$this->nFile = parse_ini_string(str(fs()->cat($file))->replace(["; ", ";"],""), true);
				$this->dFile = @array_diff_assoc($this->nFile, $this->oFile);
			}

			/**
			 * @param string $name
			 * @param boolean $comment
			 * 
			 * @return static
			 */
			private function section(string $name, bool $comment = false):static{

					unset($this->dFile[$name]);
					$block_ls = array_keys($this->dFile);

					$this->ini = arr($this->nFile)->each(function($k, $sec) use($comment, $name, $block_ls){

						if(negate(is_array($sec)))
							return sprintf("%s = %s", $k, $sec);

						if($k != $name)
							$comment = false;

						if(in_array($k, $block_ls))
							$comment = true;

						$ln = sprintf("[%s]", $k);
						$ini[] = ($comment)?str("; ")->concat($ln)->yield():$ln;
						return arr($ini)->merge(arr($sec)->each(function($k, $item) use($comment){

							if(is_array($item))
								return arr($item)->each(function($_, $item) use($k, $comment){

									$ln = sprintf("%s[] = %s", $k, $item);
									$ln = ($comment)?str("; ")->concat($ln)->yield():$ln;
									return $ln;

								})->yield();

							if(is_string($item)){

								$ln = sprintf("%s = %s", $k, $item);
								$ln = ($comment)?str("; ")->concat($ln)->yield():$ln;
								return $ln;
							}

						})->yield())->yield();

					})->level();

				return $this;
			}

			/**
			 * @param string $name
			 * @param string $key
			 * @param boolean $comment
			 * 
			 * @return static
			 */
			private function withKeyVal(string $name, string $key, bool $comment = false):static{

				$lines = str(fs()->cat($this->file))->split("\n");
				$this->ini = arr($lines)->each(function($k, $ln) use($name, $key, $comment){

					if(negate($comment))
						if(str($ln)->startsWith(";") && str($ln)->contains($name) && str($ln)->contains($key))
							return str($ln)->replace(["; ", ";"],"")->yield();

					if($comment)
						if(str($ln)->contains($name) && str($ln)->contains($key))
							return str($ln)->prepend("; ")->yield();

					return $ln;

				})->yield();

				return $this;
			}

			/**
			 * @param string $key
			 * @param boolean $comment
			 * 
			 * @return static
			 */
			private function withKey(string $key, bool $comment = false):static{

				$lines = str(fs()->cat($this->file))->split("\n");
				$this->ini = arr($lines)->each(function($k, $ln) use($key, $comment){

					$oln = $ln;
					$ln = str($ln)->replace(["; ", ";"],"");
					if(negate($comment))
						if($ln->startsWith($key))
							return $ln->yield();

					if($comment)
						return $ln->prepend("; ")->yield();

					return $oln;

				})->yield();

				return $this;
			}

			/**
			 * @param string $name
			 * @param string $key
			 * 
			 * @return static
			 */
			public function disable(?string $name = null, ?string $key = null):static{

				if(notnull($key) && notnull($name))
					if(arr($this->nFile)->contains($name))
						if(arr($this->nFile[$name])->has($key))
							$this->withKeyVal($name, $key, comment:true);

				if(notnull($key) && is_null($name))
					$this->withKey($key, comment:true);

				if(is_null($key) && notnull($name))
					$this->section($name, comment:true);

				return $this;
			}

			/**
			 * @param string $name
			 * @param string $key
			 * 
			 * @return static
			 */
			public function enable(?string $name = null, ?string $key = null):static{

				if(notnull($key) && notnull($name))
					if(arr($this->nFile)->contains($name))
						if(arr($this->nFile[$name])->has($key))
							$this->withKeyVal($name, $key);

				if(notnull($key) && is_null($name))
					$this->withKey($key);

				if(is_null($key) && notnull($name))
					$this->section($name);

				return $this;
			}

			/**
			* @return string
			*/
			public function yield(){

				return arr($this->ini)->concat("\n");
			}
		};
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