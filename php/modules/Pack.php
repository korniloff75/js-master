<?php
// Убрать рудименты !!!

class Pack

{
	function __construct ($filename)

	{
		if(!class_exists('ZipArchive'))
			throw new Exception("Нет класса ZipArchive", 1);

		// нужен для Internet Explorer, иначе Content-Disposition игнорируется
		if(ini_get('zlib.output_compression'))  ini_set('zlib.output_compression', 'Off');

		$this->noDate= isset($_REQUEST['noDate']);
		# Папка назначения
		$this->dest= $_REQUEST['dest'] ?? HOME . SUBSITE . 'tmp/zip/';

		if(!is_dir($this->dest))
			if(!mkdir($this->dest, 0777, true)) die("Не удаётся создать $this->dest !");


		# класс для работы с архивами
		$this->zip = new ZipArchive;


		if(isset($_REQUEST['recurse']))
		{
			$this->nameZIP = $this->RecursiveFolder($filename, $dest, $noDate);

		}
		else
		{
			$this->nameZIP = $this->folder($filename);
		}


	} // __construct


	/*
	Упаковка содержимого директории без рекурсии
	Игнорируются: *.zip
	*/

	function folder (string $pathdir='../js/Diz_alt_LITE/') :string

	{
		$nameZIP = ($_REQUEST['nameZIP'] ?? $this->dest . basename($pathdir)) . '.zip';
		$pathdir = HOME . $pathdir;

		if(!\ADMIN && file_exists($nameZIP))
			return $nameZIP;

		$iter = new FilesystemIterator ($pathdir, FilesystemIterator::SKIP_DOTS);

		// var_dump($iter);

		if(!$this->zip->open($nameZIP, ZIPARCHIVE::OVERWRITE | ZipArchive:: CREATE )) die ('Произошла ошибка при создании архива' . __FILE__ . ' : ' . __LINE__ );


		# создаем архив, если все прошло удачно продолжаем

		# открываем папку с файлами

		foreach ($iter as $fn)
		{
			if(!$fn->isFile() || $fn->getExtension() === 'zip') continue;

			// echo '<pre>'; var_dump($fn->getPathname(), $fn->getExtension()); echo '</pre>';

			$this->zip->addFile($fn->getPathname(), $fn->getFilename());
		}

		$this->zip->close();
		// exit;

		return $nameZIP;

	} // folder


	/*
	Упаковка содержимого директории с рекурсией поддиректорий
	*/
	function RecursiveFolder ($pathdir) {
		global $kff;

		$nameZIP = $kff['translit']($this->dest . basename($pathdir) . (!$this->single ? ('_' . date("Ymd_His")):'')) .'.zip';

		$kff['log']['RecursiveFolder'][] = basename($this->dest) .'___'. end(explode(DIRECTORY_SEPARATOR,realpath($pathdir)))  .'___'. $nameZIP . '<hr>';


		# создаем архив, если все прошло удачно продолжаем
		if ( !$this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive:: CREATE )) e('Не работает ', '$this->zip->open', 'в ' . __FILE__ . __LINE__);


		# Создаем новый объект RecursiveDirectoryIterator
	//	echo basename($pathdir);
		$iter = new RecursiveDirectoryIterator($pathdir, FilesystemIterator::SKIP_DOTS);
		// Цикл по списку директории
		// Нужно создать новый экземпляр RecursiveIteratorIterator
		@$kff['packZipRecurse'] .= '<div>';


		foreach (new RecursiveIteratorIterator($iter) as $key) {

			$expr= preg_replace('#.*'.basename($pathdir).'.(.+?)$#','$1',$key);
			$kff['packZipRecurse'] .= basename($pathdir) . "__$key=> $expr <br>";

			$this->zip->addFile($key) or die ("<br>ERROR: Could not add file: $key in " . __FILE__ . ' : ' . __LINE__);
		}

		$kff['packZipRecurse'] .= '</div>';

		$this->zip->close(); # закрываем архив

		return $nameZIP;
	}

} // Pack




/*
===== FNS =====


/*
Упаковка содержимого директории без рекурсии с итератором
*/
function packZipIter(string $pathdir='../js/Diz_alt_LITE/', $zipdir='/out_files/zip/'):string {
	$addDate= !$single? ('_' . date("Ymd_His")):'';
	$nameZIP = realpath($zipdir) . '/' . end(explode(DIRECTORY_SEPARATOR,realpath($pathdir))) . $addDate .'.zip';
//	echo $pathdir .'___'. end(explode(DIRECTORY_SEPARATOR,realpath($pathdir))) .'___'. $nameZIP . '<hr>';
	# класс для работы с архивами
	$this->zip = new ZipArchive;
	# создаем архив, если все прошло удачно продолжаем
	if ( ! $this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive:: CREATE )) die ('Не работает $this->zip->open в packZipRecur');

	# Создаем новый объект RecursiveDirectoryIterator
	$iter = new DirectoryIterator($pathdir);
	print_r($iter).'<br>';
	echo '<p>Работаем с методами:</p>';
	print_r($iter->isDot());
	echo '<p>Работаем с циклом:</p>';
	// Цикл по списку директории
	// Нужно создать новый экземпляр RecursiveIteratorIterator
	foreach (new IteratorIterator($iter) as $key=>$value) {
		echo $key . '__' . $value . "<br>";
//		$this->zip->addFile(realpath($key), $key) or die ("ERROR: Could not add file: $key");
	}

	$this->zip->close(); # закрываем архив.
	return $nameZIP; exit();
}


/*
Удаляем лишние резервные копии
*/
function actualCount ($pathdir, $nameCopy='articles') {
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
	$kff['log']['packToZip'][]= '<br>count($dir)= ' . count($dir) . '<br> $unwanted= ' . $unwanted . '<br>$cf[\'backup\'][\'numberoffiles\']= ' . $cf['backup']['numberoffiles'];

	if($unwanted<=0) return false;

	$o= '';
	for ($i = 0; $i < $unwanted; $i++) {
		if(unlink($pathdir . $dir[$i])) $o.= $pathdir . $dir[$i].'<br>';
	}
	return $o;
}

/*
Архивирование массива файлов
*/
function packZipArr ($arrFiles, $zipdir='out_files/zip/', bool $single=null) {
	$rpsn = HOME . SUBSITE;
	$zipdir= $rpsn . $zipdir;
	if(!file_exists($zipdir)) die("<p>Нет директории $zipdir</p>" );
	$addDate= !$single? ('_' . date("Ymd_His")):'';
	$nameZIP = realpath($zipdir) . '/' . end(explode(DIRECTORY_SEPARATOR,realpath($arrFiles[0]))) . $addDate .'.zip';
	$this->zip = new ZipArchive;

	if ( ! $this->zip->open($nameZIP, ZipArchive::OVERWRITE | ZipArchive:: CREATE )) die ('Не работает $this->zip->open в packZipFile');
	foreach($arrFiles as $f) {
		$f= $rpsn . $f;
		if (!file_exists(realpath($f))) die("ERROR: NO file exist: $f");
		$this->zip->addFile(realpath($f), basename($f)) or die ("ERROR: Could not add file: $arrFiles");
	}
//	$this->zip->addFile(realpath($arrFiles), basename($arrFiles)) or die ("ERROR: Could not add file: $arrFiles");

	$this->zip->close(); # закрываем архив.
	return $nameZIP; exit();
}
# !!!!!!!!!!!!!!! #


function unpackZip($pathdir='test/') {
	//название архива
	$nameZIP = 'test.zip';
	// класс для работы с архивами
	$this->zip = new ZipArchive;
	// открываем архив
	if ($this->zip->open($nameZIP) === true) {
		 // распаковываем архив
		 $this->zip->extractTo($pathdir);
		 // закрываем архив.
		 $this->zip->close();
		 echo 'Архив распакован в ' . $pathdir;
	} else {
		die (' Произошла ошибка при распаковке архива' );
	}
}

# Резервное копирование директории (рекурсивно)
function copyDir (string $sourse, $dest='./content/backup/', bool $compTime=false, $log='') { # 4 login.php
	$log.= 'Лог работы фукции <b>copyDir</b><hr>sourse= ' . $sourse . '; dest= ' . $dest . '<hr>';
	if (!is_dir($dest)) {mkdir ($dest); $log.='<br>Создана директория'.$dest;}
	$fixPathes= fixWINpathes($sourse, $dest);
	$sourse= $fixPathes[0];
	$dest= $fixPathes[1];
	$files= scandir($sourse);
	$dest_files= scandir($dest);

	# Удаляем старые версии
	foreach ($dest_files as $file) {
		if (in_array($file, $files) )  continue;

		if(is_file($file)) unlink($dest.$file);
		elseif(is_dir($dest.$file) && !preg_match("/\.+/", $file)) {
			rmdir($dest.$file);
			$log.= 'Директория' . $file . ' должна быть удалена';
		} ;
	}
	# Копируем файлы в $dest. Если директория - рекурсия.
	foreach ($files as $i=>$f) {
		$sfArr= fixWINpathes($sourse.$f);
		$sf= $sfArr[0];
		$df= $dest.$f;
		if (is_file($sf) && !preg_match("/index|404/",$f)) {
			$log.= '<br> Файлы для копирования: sf= ' . $sf . '; df= ' . $df . '<br>';
			# Пропускаем одинаковые файлы # filemtime() - Не работает
			if($compTime && ($f==$dest_files[i]) && (filectime($sf)===filectime($df))) continue;
			$log.= '<b>filectime($sf)</b>= ' . filectime($sf) . '_<b>filectime($df)</b>= ' . filectime($df) . '_' . (filectime($sf)===filectime($df)) .'<br>';;
			if(!copy($sf, $df)) die ('<p class=warning>Ошибка резервного копирования. Файл '.sf.' не скопирован!</p>');

		} else {
			$log.= '<br>Проверка условия существования директории <b>'.$f.'</b> = ' . (!preg_match("/\.+/u",$f));
			if (is_dir($sf) && !preg_match("/\.+/",$f)) { copyDir ($sf.'/', $df.'/', $log);}
		}
	}
	return $log;
//	return true;
} //== /copyDir