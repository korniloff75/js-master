<?php

/**
 * ! required \Site
 * @param pathname todo ...
 * *Usage
 * $newZip = new Pack;
 * ?optional
 * ? Pack::$dest = '/path/to/destFolder';
 * ? Pack::$name = 'archive';
 * ? Pack::$excludes[] = '\.zip';
 * ? Pack::$my_engine_format = 1;
 * ???
 * $newZip->Directory('/path/to/source');
 * OR
 * $newZip->RecursiveDirectory('/path/to/source');
 * @return archivePathname
 */

class Pack

{
	public static
		$dest = DR . '/tmp/zip',
		$name,
		$excludes = ['\.log$', '__$', 'cfg\.', 'token', 'categories\.json'],
		$my_engine_format = false;

	public
		$single = 1;

	function __construct (?string $pathname=null)

	{
		if(
			!class_exists('ZipArchive')
			|| !class_exists('\Site')
		)
			throw new Exception("Чего-то не хватает!", 1);

		// нужен для Internet Explorer, иначе Content-Disposition игнорируется
		if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');

		\Site::createDir(static::$dest);

		# класс для работы с архивами
		$this->zip = new ZipArchive;

	} // __construct


	/*
	Упаковка содержимого директории без рекурсии
	*/

	function Directory (string $pathdir) :string
	{
		return $this->_Pack(
			new FilesystemIterator ($pathdir, FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS), $pathdir
		);

	}

	function RecursiveDirectory (string $pathdir) :string
	{
		return $this->_Pack(
			new RecursiveIteratorIterator (
				new RecursiveDirectoryIterator($pathdir, FilesystemIterator::SKIP_DOTS|FilesystemIterator::UNIX_PATHS)
			), $pathdir
		);

	}


	private function _Pack ($iter, string $pathdir)
	{
		$excludes = '~'.implode('|',static::$excludes).'~u';

		$pathdir = \Site::fixSlashes($pathdir);

		$nameZIP = \Site::translit(static::$dest . '/' . (self::$name ?? basename($pathdir)) . (!$this->single ? ('_' . date("Ymd_His")):'')) .'.zip';

		// *Под Админом - перепаковываем
		if(!is_adm() && file_exists($nameZIP))
			return $nameZIP;

		tolog(__METHOD__,null,[static::$dest, explode(DIRECTORY_SEPARATOR,realpath($pathdir)), '$nameZIP'=>$nameZIP]);


		// *создаем архив, если все прошло удачно продолжаем
		if ( !$this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive::CREATE ))
			tolog(__METHOD__.' Не работает $this->zip->open', E_USER_WARNING);


		foreach ($iter as $pathname=>$fileInfo)
		{
			$pathname = \Site::fixSlashes($pathname);

			if(
				$fileInfo->isDir()
				|| preg_match($excludes, $pathname)
			)
				continue;

			// *Пакуем с добавлением корневой папки
			if(static::$my_engine_format)
			{
				$zipName = str_replace(
					dirname($pathdir),
					'',$pathname
				);
			}
			else
			// *Напрямую в архив
			{
				$zipName = str_replace(
					$pathdir,
					'',$pathname
				);
			}


			tolog(['$zipName' => $zipName]);

			$this->zip->addFile($pathname, trim($zipName,'/'))
			or new Exception ("ERROR: Could not add file: $fileInfo");
		}//foreach

		tolog(__METHOD__,null,['$pathdir'=>$pathdir,'status'=>$this->zip->getStatusString()]);

		$this->zip->close();

		return $nameZIP;
	}


	/*
Удаляем лишние резервные копии
*/
// todo ...
function actualQuantity ($pathdir, $nameCopy='articles') {
	global $cf, $rpsn;
	$pathdir= $rpsn . $pathdir;
	$dir= scandir($pathdir);
//	echo '$dir1= ' . print_r($dir) . '<br>';
	foreach($dir as $key=>$fn) {
		if(strpos($fn, $nameCopy)===false) unset($dir[$key]);
	}
	$dir= array_values($dir);

//	echo '$dir2= ' . print_r($dir) . '<br>';

	$unwanted= count($dir) - $cf['backup']['numberoffiles'];
	'<br>count($dir)= ' . count($dir) . '<br> $unwanted= ' . $unwanted . '<br>$cf[\'backup\'][\'numberoffiles\']= ' . $cf['backup']['numberoffiles'];

	if($unwanted<=0) return false;

	$o= '';
	for ($i = 0; $i < $unwanted; $i++) {
		if(unlink($pathdir . $dir[$i])) $o.= $pathdir . $dir[$i].'<br>';
	}
	return $o;
}

} // Pack
