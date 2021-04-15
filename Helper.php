<?php
# инициализация экземпляра класса
\H::getInstance();

# Helper singlton pattern
class H {
	// const
	// 	FILE_INFO;

	protected static $_instance;

	public static
		$tmp = [],
		$fileInfo,
		$Dir,
		$log = [],
		$notes = [];


	// public $add;

	private function __construct()
	{
		global $Nav;

		ini_set('short_open_tag', 'On');

		require_once \DR . '/CONST.php';


		if(array_key_exists('login', $_REQUEST))
			self::includeModule('Login');

		/* # Обрабатываем фаталы
		$this->handleFatals(); */

		// todo ->is_adm()
		define('ADMIN',
			isset($_SESSION['auth']['group'])
			&& $_SESSION['auth']['group'] === 'admin'
			// && $_SERVER['SERVER_ADDR'] === HOST_IP
		/* 	&& (
				strpos(Site::realIP(), \ADM) === 0
				|| LOCALHOST
			) */
		);

		// var_dump(\ADMIN, $_SESSION, self::realIP(), (strpos(self::realIP(), \ADM) === 0));
		define('BASE_URL', (Site::is('https') ? 'https' : 'http') . '://' . \HOST . '/');

		# Для подключение не через ROOT
		if (realpath('') !== realpath(\DR))
			return;

		tolog('Helper started from ROOT');


		if(ADMIN || \TEST || Site::DEV)
		{
			# Develop
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(-1);
			// var_dump($adm, LOCALHOST);
		} else {
			#Production
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
			error_reporting(0);
		}

		require_once \DR . '/php/funcs.php';

		//* create map ...
		// echo phpversion();
		$Nav = new \php\classes\Navigate;

		# Define current page
		self::$fileInfo = new kffFileInfo($_REQUEST['page'] ?? $Nav->firstPage);

		self::$Dir = self::$fileInfo->fromRoot() . '/';

		define('DIR', self::$Dir);

		if(!is_dir(self::$Dir)) self::shead(404);

		// var_dump($bg, IMAGES, BG);
		// exit;
	} // __construct


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
		if (!array_key_exists($prop, $defines)) return null;
		return $defines[$prop]();
	}


	/**
	 * Add all items from the dir
	 */
	public static function addFromDir($dir, $opts=[])
	:string
	{
		// echo "Запустили addFromDir.";
		if(is_string($opts)) $opts = ['ext' => $opts];

		$opts = array_merge([
			'defer' => 0,
			'except' => '0' // исплючения
		], $opts);

		if(!file_exists($dir)) {
			tolog("dir $dir not exist");
			return '';
		}

		$ext = $opts['ext'] ?? 0;


		if(!$ext) {
			$ext = explode('/', substr($dir, 0, -1));
			$ext = end($ext);
		}

		$items = (new \DirFilter ($dir, "#\.$ext$#i"))->natSort();


		# Для выведения по порядку файлы именовать:
		# 1.file.php
		# 2.file.php
		# 3... etc.
		// var_dump($opts, $ext, $items);


		# include file by its extension
		$next = function ($i) use (&$ext, &$opts)
		{
			if(is_object($i)) {
				// $i = $i->getRealPath();
				$i = $i->getPathname();
			}
			else
			{
				$i = Path::fixSlashes($i);
			}

			$pi = pathinfo($i);
			$fn = $pi['filename'];

			// var_dump($i, $fn);

			if(strpos($fn, (string) $opts['except']) === 0) return '';

			switch ($ext) {
				case 'js':
					$patt = "<script src=\"/$i\"" . ($opts['defer'] ? ' defer' : '') . "></script>";
					break;

				case 'css':
					$patt = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/$i\">";
					break;

				case 'less':
					// var_dump($fn);
					$css = $pi['dirname'] . "/$fn.css";

					tolog(["css = " => [$css, realpath('./'.$css), realpath('./templates/../' . "$fn.css")]], __FILE__, __LINE__);
					if(!\ADMIN && file_exists($css)) return;

					$patt = "<link rel=\"stylesheet/less\" type=\"text/css\" href=\"/$i\">";
					// var_dump($patt);
					// return;

					if(\ADMIN &&
						(!is_file($css) || filemtime($i) > filemtime($css))
					)
					{
						// require_once \DR . '/php/modules/lessc.inc.php';
						try {

							/* compile file $in to file $out if $in is newer than $out
							returns true when it compiles, false otherwise

							lessc::ccompile($in, $out, $less = null);
							var_dump($less->compileFile($i));
							*/
							// $less = new lessc;
							$less = self::includeModule('lessc');
							// $r = $less->checkedCompile($i, $css);
							$r = $less->compileFile($i, $css);

							// var_dump($less, $r);

							tolog("Compille LESS $i\nreturn $r", __FILE__, __LINE__);

						} catch (exception $e) {
							debug_zval_dump ("LESS not compilled.\nFatal error: \n" . $e->getMessage() . "\n" . __FILE__ . __LINE__);

						}
					}
					else
						tolog("NO class <b>lessc</b>", __FILE__);
				break;

				case 'php': //Legacy
					$patt = file_exists($i) ? "include_once(\"$i\");\n" : "var_dump(\"$i\");";
					// var_dump($patt);
				break;
			}

			return $patt ? str_replace("\\", "\\\\", $patt) : '';
			// return $patt;
		};

		$o='';

		foreach($items as $i)
		{
			// var_dump($i->getPathName());
			$nf = $next($i);
			// $nf = $next($i->getPathName());

			$o .= $nf . "\n";
		}

		// tolog('o = ' . $o);

		// var_dump(self::$notes, $o);
		return $o;

	} // addFromDir


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


	public static function protectScript(string $fn, $checkAdm = 0)

	{
		if (preg_match('/' . $fn . '/i', $_SERVER['SCRIPT_NAME'])) self::shead('403');

		if($checkAdm) {
			if(!\ADMIN) {
				self::shead('403', '<p class="core warning">Access in ' . $fn . ' Denied!</p>');
			} else {
				// success;
			}
		}

	}


	public static function shead ($s, $o = '')

	{
		global $Data, $Nav;
		header('Content-type: text/html; charset=utf-8');

		if ($s == 401)
			header('HTTP/1.0 401 Unauthorized');

		elseif ($s == 404)
		{
			unlink ($Nav->map_path);
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

		$Data['title'] = "Error $s";

		if(!include("templates/errorpages/$s.htm"))
		 echo '<h1>' . $Data['title'] . "</h1>\n" . $o;

		 die;

	}


	public static function includeModule ($name)
	{
		include_once \DR . "/php/modules/$name.php";
		return new $name();
	}


	public static function initvars ($names)
	{
		if(is_array($names))
			foreach($names as $name => $val) {
				$GLOBALS["$name"] = $val;
			}
	}


	public static function getMaxSizeUpload ()
	{
		return min(self::sizeToBytes(ini_get('post_max_size')), self::sizeToBytes(ini_get('upload_max_filesize')));
	}

	protected static function sizeToBytes ($sSize)
	{
		$sSuffix = strtoupper(substr($sSize, -1));
	   if (!in_array($sSuffix,array('P','T','G','M','K')))
		 return (int)$sSize;

	   $iValue = substr($sSize, 0, -1);
	   switch ($sSuffix) {
			case 'P':
				$iValue *= 1024;
			case 'T':
				$iValue *= 1024;
			case 'G':
				$iValue *= 1024;
			case 'M':
				$iValue *= 1024;
			case 'K':
				$iValue *= 1024;
				break;
	   }
	   return (int)$iValue;
	}


	/**
	* returns mixed
	* @$d :
	* NULL - get (array) json
	* string - get json[$d]
	* array - assert merge json & $d
	*
	* if bool @$rewrite - replace json -> $d
	*/
	public static function json ($path, $d = NULL, $rewrite = 0)

	{
		if(!is_array($d) && !file_exists($path)) return [];

		if(is_null($d))
		{
			# GET base
			return json_decode(@file_get_contents($path), true) ?? [];
		}
		elseif(is_array($d))
		{
			# SET assert the results
			// echo '<pre>'; var_dump($path, self::json($path));

			$d = !$rewrite ? array_replace(self::json($path), $d) : $d;

			return file_put_contents($path, json_encode($d, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX);
			//
		}
		elseif(is_string($d))
		{
			# GET value
			return @self::json($path)[$d];
		}
		else
		{
			throw new LogicException(__CLASS__ . __METHOD__ . " не может быть выполнена с аргументом $d");
		}

	} // json


	public static function realIP ()

	{
		return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
	}


	/*
	* $data - массив элементов из файла
	* Функция возвращает массив, содержащий фрагмент
	* массива $data, соответствующий запросу $name_request
	*/
	public static function paginator (array $data, int $max_entries=10, string $name_request='p', $reverse=1, $hash="")

	{
		$paginator = '';

		// var_dump($data);

		if(!$data_count = count($data)) return false;
		// var_dump($data_count);

		if($reverse) $data= array_reverse($data);

		$page_blocks_count=ceil($data_count/$max_entries);

		$p = $_REQUEST[$name_request] ?? "1";

		$first_page= ($p-1)*$max_entries;
		$last_page= $p*$max_entries; # -1


		if($page_blocks_count != 1) {
			$paginator .= "<div class=paginator data-id=\"$name_request\">";

			for($u=1; $u<=$page_blocks_count; $u++) {
				if($p!=$u){
					$paginator .= "<a href='/{$_REQUEST['page']}?$name_request= $u'>$u</a> ";
				}	elseif($p==$u){
					$paginator .= "<b>$u</b> ";
				}
			}

			$paginator .= "</div>";
		} else {
			$paginator = '';
		}

		return [
			'fragm'=>array_slice($data,$first_page,$max_entries), #-1
			'paginator' => $paginator,
			'fp'=>$first_page,
			'lp'=>$last_page,
			'data_count'=>$data_count,
		];
	}



	# Singlton methods
	public static function getInstance()
	{
		self::$_instance = self::$_instance ?? new self;
		return self::$_instance;
	}

	private function __clone() {}

	public function __wakeup() {}
} // H


/*
REM

# get filename from $pathname
	pathinfo($pathname)['filename'];
	OR
	substr(strrchr($path, "/"), 1);


*/