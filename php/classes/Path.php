<?php

/*
	$path_from_root = (new \Path(__DIR__))->fromRoot();
	OR (use static method)
	Path::fromRootStat($path);

	Path::fixSlashes($path);

	Path::parentFolder('path/to/folder');
	returns 'path/to/'
	Path::parentFolder('path/to/folder/any', 'to');
	returns 'path/to/'
*/

class Path extends kffFileInfo {
	public $path;

	public function __construct($path)
	{
		return parent::__construct($path);

		/* 	var_dump(
		$wrapPath->getPath(),
		$wrapPath->getPathname(),
		$wrapPath->getRealPath(),
		$wrapPath->getFilename()
		); */
	}


	public static function fromRootStat(string $path)
	:string
	{
		// return str_replace(self::fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', self::fixSlashes($path));
		return Site::getPathFromRoot($path);
	}

	public static function parentFolder(string $haystack, $needle = null)
	:string
	{
		$str = '';
		$arr = array_filter(explode('/', $haystack));

		for ($i=0; $i < count($arr) - 1; $i++) {
			$str .= $arr[$i] . '/';
			if($needle && $needle === $arr[0])
				break;
		}

		return $str;
	}

} // class Path