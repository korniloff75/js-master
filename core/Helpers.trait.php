<?php
/* if(!defined('DR'))
{
	define('DR', $_SERVER['DOCUMENT_ROOT']);
} */


interface BasicClassInterface
{
	const
		DATETIME_FORMAT = "Y-m-d H:i:s",
		DATE_FORMAT = "Y-m-d";
}


// *Admin
function is_adm()
{
	if(empty($_SESSION)) session_start();
	return isset($_SESSION['auth']['group'])
	&& $_SESSION['auth']['group'] === 'admin';
}


/**
 *
 */
trait Helpers
{
	public static function is(string $prop)
	{
		$prop = strtolower($prop);

		$defines = [
			'ajax' => function() {
				return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
				&& !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
				&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
			},
			'https' => function() {
				return !empty($_SERVER['HTTPS']) && ('off' !== strtolower($_SERVER['HTTPS']));
			},
		];

		$defines['http']= function(){return !$defines['https']();};

		if (!array_key_exists($prop, $defines)) return null;
		return $defines[$prop]();
	}


	static function createDir($pathname, ?int $chmod=0755)
	{
		if(
			!is_dir($pathname)
			&& !mkdir($pathname, $chmod, true)
		){
			header('Content-Type: text/html; charset=utf-8');
			throw new Exception("Невозможно создать директорию $pathname. Попробуйте создать её вручную");
		}
		// *Меняем права
		elseif(
			// *check UNIX
			DIRECTORY_SEPARATOR === '/'
			&& ($perms= fileperms($pathname)) !== $chmod
		){
			if(!chmod ($pathname, $chmod) || $perms !== $chmod)
				tolog(__METHOD__.": Измените вручную права на папку $pathname на 0" . decoct($chmod),E_USER_WARNING,[$pathname=>$perms,fileperms($pathname)]);
		}
	}


	// *Переводим все слеши в Unix
	public static function fixSlashes(?string $path)
	:?string
	{
		if(!$path) return null;
		$path = str_replace("\\", '/', $path);
		return preg_replace("#(?!https?|^)//+#", '/', $path);
	}


	//
	/**
	 * *Путь относительно DR
	 * @param {bool} $fromRootFolder === true ? Root folder : Domain root
	 */
	public static function getPathFromRoot(string $absPath, $fromRootFolder=false)
	:string
	{
		// $Root= defined('GDR') && !$fromRootFolder? \GDR: $_SERVER['DOCUMENT_ROOT'];
		$Root= defined('GDR') && !$fromRootFolder? \GDR: \DR;

		$absPath= self::fixSlashes($absPath);
		$Root= self::fixSlashes($Root) . '/';
		$out= str_replace($Root, '', $absPath);

		// tolog(__METHOD__,null,['$absPath'=>$absPath, '$Root'=>$Root, '$out'=>$out]);

		return $out;
	}


	// *Реальный IP
	public static function realIP ()
	{
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
	}


	// *Чтение строк из последних @size байт файла
	public static function rfileByte(string $file,$size=1024)
	:array
	{
		$size= ceil($size/1024)*1024;
		$f = fopen( $file, "r" );
		fseek( $f, -$size, SEEK_END );
		$str = fread( $f, $size );
		fclose( $f );

		$p =  mb_strpos( $str, "\n" );
		if ( $p !== false ) {
			$str = mb_substr( $str, ++$p );
		}

		return array_values(array_filter(explode("\n", $str)));
	}

	// *Чтение файла построчно с конца
	public static function rfile(string $file,$num=10)
	:?array
	{
		$buf=1024;
		if(!is_file($file)) return null;

		$f=fopen($file, 'r');
		$pos=filesize($file)-1;
		$c=0;$read="";
		while($pos>0) {
			$pos-=$buf;
			if($pos<0) {$buf+=$pos;$pos=0;}
			fseek($f,$pos);
			$tmp=fread($f,$buf);
			$c+=substr_count($tmp,"\n");
			$read=$tmp.$read;
			if($c>$num) break;
		}
		fclose($f);

		$a=explode("\n",$read);
		$c=count($a);
		$r=[];

		if(!$a[$c-1]) {
			unset($a[$c-1]);$c--;
		}
		$start=$c-$num; if($start<0) $start=0;

		for($i=$start;$i<$c;$i++) $r[]=$a[$i]."\n";

		return $r;

	}


	/**
	 * @param pathname - имя модуля или полный путь к директории
	 */
	public static function getZipModule(string $pathname)
	{
		if(
			!file_exists($pathname)
			&& !file_exists($pathname = DR."/modules/$pathname")
		)
		{
			return false;
		}

		Pack::$dest = DR . '/files/zip';
		Pack::$excludes[] = '\.zip$';
		// *Пакуем с добавлением корневой папки
		Pack::$my_engine_format = 1;

		$pack = new Pack;

		return $pack->RecursiveDirectory($pathname);
	}


	public static function translit(string $s, $direct = 0)
	:string
	{
		$translit = [
		'а' => 'a', 'б' => 'b', 'в' => 'v','г' => 'g', 'д' => 'd', 'е' => 'e','ё' => 'yo', 'ж' => 'zh', 'з' => 'z','и' => 'i', 'й' => 'j', 'к' => 'k','л' => 'l', 'м' => 'm', 'н' => 'n','о' => 'o', 'п' => 'p', 'р' => 'r','с' => 's', 'т' => 't', 'у' => 'u','ф' => 'f', 'х' => 'x', 'ц' => 'c','ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh','ь' => '\'', 'ы' => 'y', 'ъ' => '\'\'','э' => 'e\'', 'ю' => 'yu', 'я' => 'ya', ' ' => '_',

		 'А' => 'A', 'Б' => 'B', 'В' => 'V','Г' => 'G', 'Д' => 'D', 'Е' => 'E','Ё' => 'YO', 'Ж' => 'Zh', 'З' => 'Z','И' => 'I', 'Й' => 'J', 'К' => 'K','Л' => 'L', 'М' => 'M', 'Н' => 'N','О' => 'O', 'П' => 'P', 'Р' => 'R','С' => 'S', 'Т' => 'T', 'У' => 'U','Ф' => 'F', 'Х' => 'X', 'Ц' => 'C','Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHH','Ь' => '\'', 'Ы' => 'Y\'', 'Ъ' => '\'\'','Э' => 'E\'', 'Ю' => 'YU', 'Я' => 'YA',

		];

		if($direct) {
			$translit = array_flip(
				array_diff_key($translit, [
				'Ь' => 1, 'Ъ' => 1
			]));
		}

		return strtr($s, $translit);
	}


	/**
	 * *Преобразование массива в формат INI
	 */
	public static function arr2ini(array $a, array $parent = [])
	:string
	{
		$out = '';
		foreach ($a as $k => &$v)
		{
			if (is_array($v))
			{
				//*subsection case
				//merge all the sections into one array...
				$sec = array_merge((array) $parent, (array) $k);
				//add section information to the output
				$out .= '[' . join('.', $sec) . ']' . PHP_EOL;
				//recursively traverse deeper
				$out .= self::arr2ini($v, $sec);
			}
			else
			{
				if(is_string($v) && !is_numeric($v))
					$v= addslashes(htmlspecialchars_decode($v, ENT_NOQUOTES));

				//*plain key->value case
				$out .= "$k=\"$v\"" . PHP_EOL;
			}
		}
		return $out;
	}


	// *DOMNode extensions

	public static function getDOMinnerHTML(DOMNode $element)
	:string
	{
		$innerHTML = "";

		foreach ($element->childNodes as $child)
		{
			$innerHTML .= $element->ownerDocument->saveHTML($child);
		}

		return $innerHTML;
	}


	static function appendDOMinnerHTML(DOMNode &$element, $html)
	{
		$fragment = $element->ownerDocument->createDocumentFragment();
		$fragment->appendXML($html);

		$element->appendChild($fragment);
	}


	static function setDOMinnerHTML(DOMNode &$element, $html)
	{
		$fragment = $element->ownerDocument->createDocumentFragment();
		$fragment->appendXML($html);
		while ($element->hasChildNodes()){
			$element->removeChild($element->firstChild);
		}

		$element->appendChild($fragment);
	}


	/**
	 * @param haystack {string}
	 * @param needles {string|array}
	 * @param posArr service
	 * Возвращает вхождение первой подстроки из mixed @needles
	 */
	public static function stripos_array(string $haystack, $needles, ?int $offset= 0, $posArr= [])
	{
		if ( !is_array($needles) )
			return mb_stripos($haystack, $needles, $offset);
		elseif (!count($needles))
			return false;

		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = self::stripos_array($haystack, $str, $offset, $posArr);
			} else {
				$pos = mb_stripos($haystack, $str, $offset);
			}

			if ($pos !== false)
				$posArr[] = $pos;
		}

		sort($posArr, SORT_NATURAL);
		// ksort($posArr, SORT_NATURAL);
		return $posArr[0] ?? false;
		// return array_keys($posArr)[0] ?? false;
	}


	/**
	 * Сшиваем файлы в один
	 */
	public static function sewFiles(array &$pathes, string $outPathname, $sep='')
	{
		$outUri= '/'.\Site::getPathFromRoot($outPathname);

		if(file_exists($outPathname)) return $outUri;

		$cache= '';

		foreach($pathes as $script){
			// $cache.= addslashes(file_get_contents(__DIR__."/js/$script.js")) . ";\n";
			$cache.= file_get_contents($script) . "$sep\n";
		}

		file_put_contents($outPathname,$cache);

		return $outUri;
	}


	/**
	 * Return ERROR header
	 */
	public static function shead ($s, $o = '')
	{
		header('Content-type: text/html; charset=utf-8');

		if ($s == 401)
			header('HTTP/1.0 401 Unauthorized');

		elseif ($s == 404)
		{
			header('HTTP/1.0 404 Not Found');
			$o = "<div>
				<p>Данная страница не найдена...</p>
				<p>Пожалуйсла, перейдите на <a href=\"/\">Главную страницу</a> сайта.</p>
			<div>";
		}
		elseif ($s == 403)
		{
			header('HTTP/1.0 403 Forbidden');
		}

		$title = "Error $s";

		if(!include("templates/errorpages/$s.htm"))
		 echo '<h1>' . $title . "</h1>\n" . $o;

		\Logger::$notWrite = true;

		 die;

	}

	//* Remove numbers like 1.file.php -> file.php
	public static function skipNum(string $name)
	:string
	{
		return preg_replace("#^(\d+\.)?(.+?)(\..+)?$#", "$2", basename($name));
	}


	public static function protectScript(string $fn, $checkAdm = 0)
	{
		if (preg_match('/' . $fn . '/i', $_SERVER['SCRIPT_NAME'])) self::shead('403');

		if($checkAdm) {
			if(!is_adm()) {
				self::shead('403', '<p class="core warning">Access in ' . $fn . ' Denied!</p>');
			} else {
				// acess allowed;
			}
		}
	}
}
