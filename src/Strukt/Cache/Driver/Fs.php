<?php

namespace Strukt\Cache\Driver;

use Strukt\Contract\CacheDriverInterface;

class Fs implements CacheDriverInterface{

	use \Strukt\Traits\Collection;

	private $fs;
	private $filename;
	private $buffer;

	public function __construct(string $file){

		$filename = sprintf("%s.json", $file);

		if(!fs()->isDir(".cache"))
			fs()->mkdir(".cache");

		$this->fs = fs(".cache");
		if(!$this->fs->isFile($filename))
			$this->fs->touchWrite($filename, "[]");

		$data = json($this->fs->cat($filename))->decode();

		$this->buffer = map($data);
		$this->filename = $filename;
	}

	public function exists(string $key):bool{

		return $this->buffer->exists($key);		
	}

	public function empty():bool{

		$data = json($this->fs->cat($this->filename))->decode();

		return empty($data);
	}

	public function put(string $key, string|array $val):self{

		$this->buffer->set($key, $val);
		
		return $this;
	}

	public function get(string $key):mixed{

		return $this->buffer->get($key);
	}

	public function remove(string $key):self{

		$this->buffer->remove($key, $val);
		
		return $this;
	}

	public function save():void{

		$arr = $this->disassemble($this->buffer);

		$this->fs->overwrite($this->filename, json($arr)->pp());
	}
}