<?php

namespace Strukt\Cache\Driver;

use Strukt\Contract\CacheDriverInterface;

/**
 * @author Moderator <pitsolu@gmail.com>
 */
class Fs implements CacheDriverInterface{

	use \Strukt\Traits\Collection;

	private $fs;
	private $filename;
	private $buffer;

	/**
	 * @param string $file
	 */
	public function __construct(string $file){

		$filename = sprintf("%s.json", $file);

		if(!fs()->isDir(phar(".cache")->adapt()))
			fs()->mkdir(phar(".cache")->adapt());

		$this->fs = fs(phar(".cache")->adapt());
		if(!$this->fs->isFile($filename))
			$this->fs->touchWrite($filename, "[]");

		$data = json($this->fs->cat($filename))->decode();

		$this->buffer = map($data);
		$this->filename = $filename;
	}

	/**
	 * @param string $key
	 * 
	 * @return boolean
	 */ 
	public function exists(string $key):bool{

		return $this->buffer->exists($key);		
	}

	/**
	 * @return boolean
	 */
	public function empty():bool{

		$data = json($this->fs->cat($this->filename))->decode();

		return empty($data);
	}

	/**
	 * @param string $key
	 * @param string|array $val
	 * 
	 * @return static
	 */ 
	public function put(string $key, string|array $val):static{

		$this->buffer->set($key, $val);
		
		return $this;
	}

	/**
	 * @param string $key
	 * 
	 * @return mixed
	 */ 
	public function get(string $key):mixed{

		return $this->buffer->get($key);
	}

	/**
	 * @param string $key
	 * 
	 * @return static
	 */
	public function remove(string $key):static{

		$this->buffer->remove($key);
		
		return $this;
	}

	/**
	 * @return void
	 */
	public function save():void{

		$arr = $this->disassemble($this->buffer);

		$this->fs->overwrite($this->filename, json($arr)->pp());
	}
}