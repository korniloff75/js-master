<?php

class RecursiveDirFilter extends \FilterIterator

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
		if(!is_dir($path)) throw new Exception("Error Type Input data. Аргумент должен содержать путь к директории.", 1);

		return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST );
		//  | FilesystemIterator::SKIP_DOTS
		// return new IteratorIterator(new RecursiveDirectoryIterator($path));
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
} // RecursiveDirFilter


/*
$path - to directory
!!! does not affect subdirectories
returns iterator from filtered items
*/

class DirFilter extends RecursiveDirFilter
{
	/* function __construct(string $path, array $ext = null)
	{
		parent::__construct($path, $ext);
	} */

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

	/* public function recursive()
	{
		$this->iterator = parent::createIterator($path);
	} */

} // dirFilter


class RecursiveOnlyDirFilter extends RecursiveDirFilter
{
	public function accept()
	{
		/* var_dump(
			$this->getPathname(),
			$this->current()->getPathname()
		); */
		return $this->current()->isDir() && !$this->iterator->isDot()
		&& preg_match($this->regEx, Path::fixSlashes($this->getPathname()));
		//  && parent::accept();
	}

} // RecursiveOnlyDirFilter


class OnlyDirFilter extends DirFilter
{
	public function accept()
	{
		/* var_dump(
			$this->getPathname(),
			$this->current()->getPathname()
		); */
		return $this->current()->isDir() && !$this->iterator->isDot()
		&& preg_match($this->regEx, Path::fixSlashes($this->getPathname()));
		//  && parent::accept();
	}

} // OnlyDirFilter




/*
# Объединить многомернй массив
	$arr = [
		"1" => [1,2,3],
		"2" => 4,
		"3" => [[5,6],7,8]
	];
	$iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($arr));
	$arrOut = iterator_to_array($iterator);
	var_dump($arrOut);
*/