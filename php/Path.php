<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . "CONST.php";


class kffFileInfo extends SplFileInfo
{
	const
		ROOT = \BASE_URL;

	public
		$path;

	public function __construct($path)
	{
		return is_object($path) && in_array('SplFileInfo', class_parents($path)) ? $path : parent::__construct($path);
	}

	public static function fixSlashes($path)
	:string
	{
		$path = str_replace("\\", '/', $path);
		return preg_replace("#(?!https?|^)//+#", '/', $path);
	}

	public function fromRoot()
	:string
	{
		return str_replace($this->fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', $this->getPathname());
	}

	public function getPathname() :string
	{
		return self::fixSlashes(parent::getPathname());
	}

	public function getPath() :string
	{
		return self::fixSlashes(parent::getPath());
	}

	public function getRealPath() :string
	{
		return self::fixSlashes(parent::getRealPath());
	}

}

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


	public static function fromRootStat($path)
	:string
	{
		return str_replace(self::fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', self::fixSlashes($path));
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

