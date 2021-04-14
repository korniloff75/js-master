<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/' . "CONST.php";


class kffFileInfo extends SplFileInfo
{
	public
		$path;

	public function __construct($path)
	{
		//*Методы родительского класса возвращают объекты - экземпляры дочернего класса.
		parent::setInfoClass(__CLASS__);

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

	/* public function getPathInfo($class_name=__CLASS__)
	{
		return (new self(parent::getPathInfo()));
	} */

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

} // kffFileInfo

