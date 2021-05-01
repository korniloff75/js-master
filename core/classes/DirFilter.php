<?php

class DirFilter extends \FilterIterator

{
	// Object SplFileInfo without filters
	public $iterator, $regEx,
	$arr = [];


	function __construct($data, $regEx = '#\.(php|html?)$#i', $method_name = 'getBasename')
	{
		$this->data = $data;
		$this->method_name = $method_name;
		$this->regEx = $regEx;
		// var_dump($data, is_object($data), !!$data);
		if(!$data) return;

		$this->iterator = is_object($data) ? $data : $this->createIterator($data);


		parent::__construct($this->iterator);

	}

	protected function createIterator($path)
	{
		if(!file_exists($path))
		{
			throw new Exception("Нет файла по пути - $path", 1);
		}

		return new IteratorIterator(
			new DirectoryIterator($path)
		);
	}


	# servise
	public function accept()
	{
		// var_dump($this->regEx, $this->getBasename());
		return !$this->iterator->isDot() && preg_match($this->regEx, Path::fixSlashes($this->{$this->method_name}()));
	}


	# custom
	public function natSort()
	:array
	{
		$arr = [];
		// var_dump($this);

		foreach($this as $i) {
			$arr[] = \Path::fixSlashes($i->getPathname());
		}

		natsort($arr);
		// var_dump($arr);
		return $arr;
	}



	public function _debug($regEx = "#^" . CONT . "((?!thumb|img|PHPMailer).)*$#u")
	{
		// $regEx = $this->regEx;
		echo '<div style="position: absolute, z-index: 20">';
		foreach($this as $i) {
			var_dump(
				Path::fixSlashes($i->getPathname()),
				$regEx
				// $this->accept()
				// preg_match($regEx, Path::fixSlashes($i->getPathname()))
			);
		}
		echo '</div>';
	}
} // DirFilter