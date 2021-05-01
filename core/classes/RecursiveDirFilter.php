<?php

class RecursiveDirFilter extends \DirFilter

{
	protected function createIterator($path)
	{
		if(!is_dir($path)) throw new Exception("Error Type Input data. Аргумент должен содержать путь к директории.", 1);

		return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST );
		//  | FilesystemIterator::SKIP_DOTS
		// return new IteratorIterator(new RecursiveDirectoryIterator($path));
	}

} // RecursiveDirFilter