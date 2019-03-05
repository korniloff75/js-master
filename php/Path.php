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

class Path {
	public $path;

	public function __construct($path)

	{
		$wrap = is_object($path) ? $path : new SplFileInfo($path);
		$this->path = $wrap->getPathname();

		/* 	var_dump(
		$wrap->getPath(),
		$wrap->getPathname(),
		$wrap->getRealPath(),
		$wrap->getFilename()
		); */
	}


	public static function fixSlashes($path)
	:string
	{
		$path = str_replace('\\', '/', $path);
		return preg_replace("#(?!https?|^)//+#", '/', $path);
	}


	public function fromRoot()
	:string
	{
		return str_replace($this->fixSlashes($_SERVER['DOCUMENT_ROOT']) . '/', '', $this->fixSlashes($this->path));
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

