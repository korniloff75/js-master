<?php

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