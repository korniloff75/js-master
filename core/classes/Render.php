<?php
namespace php\classes;

\Plugins::getHook('integration_system');

class Render
{
	function __construct()
	{
		// \Plugins::getHook('integration_system');
	} // __construct


	public static function meta()
	:string
	{
		$Data= &\Page::$Data;

		$_SESSION['captcha'] = random_int(1e3,1e6);

		$meta = "<meta charset=\"UTF-8\">
		<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
		<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n
		<meta name=\"author\" content=\"" . \OWNER['name'] . "\">
		<meta name=\"robots\" content=\"" . (empty($Data['hidden']) ? "index, follow" : "noindex, nofollow") . "\">
		<meta name=\"generator\" content=\"" . \VERSION . " -  js-master.ru\">";

		#SEO
		if(isset($Data['seo']))
		{
			$Data['seo'] = array_replace([null,null], $Data['seo']);
			// var_dump($Data['seo']);
			list($Data['description'], $Data['keywords']) = $Data['seo'];
		}

		// \H::$Data = $Data;

		if(isset($Data['description']))
		{
			$meta .= "<meta name=\"description\" content=\"{$Data['description']}\"/>\n";
		}

		if(isset($Data['keywords']))
		{
			$Data['keywords'] = preg_replace("#\,\s+#", ',', $Data['keywords']);
			$meta .= "<meta name=\"keywords\" content=\"{$Data['keywords']}\"/>\n";
		}

		# favicon
		$meta.= self::_findIcon($Data['template']) ?? self::_findIcon('./') ?? '';

		// var_dump(__FILE__, __LINE__, $meta);
		return $meta;
	} // meta


	private static function _findIcon(string $path)
	:?string
	{
		foreach(['svg','ico','gif','png'] as &$ext) {
			if(!file_exists($iconpath= $path.'favicon.' . $ext)) continue;
			$iconLink = '<link rel="icon" type="image/' . ($ext === 'ico' ? 'x-icon' : $ext) . '" href="/' . $iconpath . '" />'
			. '<link rel="shortcut icon" type="image/x-icon" href="/' . $iconpath . '" />';
			break;
		}
		return $iconLink ?? null;
	}


	public static function head()
	: string
	{
		$Data= &\Page::$Data;

		return self::meta()
		. "\n<title>{$Data['title']} - " . \SITENAME . '</title>'
		. "\n" . '<link rel="stylesheet" type="text/css" href="/css/base.css">'
		. "\n" . '<link rel="stylesheet" type="text/css" href="/assets/font-awesome/css/font-awesome.min.css">'
		. "\n<!--Site::\$Page->headhtml-->\n" . \Site::$Page->headhtml . "\n<!--/Site::\$Page->headhtml-->\n"
		. \Page::setSV()
		. "\n" . \H::addFromDir('js/') //todo в продакшне собрать в 1 файл
		. \Plugins::getHook('head');
	}


	public static function contentCollect($dirPathname, $opts = [])
	: string
	{
		$Data= \Page::$Data;

		$current = $dirPathname === \DIR;
		\Page::$DIR = $dirPathname;

		tolog(__METHOD__,null,['DIR'=>\Page::$DIR, '$dirPathname'=>$dirPathname]);

		ob_start();

		$idf = new \DirFilter($dirPathname);
		$Data = $current ? $Data : \Page::getData($dirPathname);
		$images = (new \DirFilter($dirPathname, "#\.(jpe?g|png)$#"))->natSort();
		$cond = \ADMIN && empty($opts['rss']);
		$hidden = !\ADMIN && !empty($Data['hidden']);

		if($hidden) return '';

		$eswitcher = '<select size="1" class="core note editorSwitcher">
		<option class="core info" title="Без редактирования" selected="selected">normal</option>
		<option class="core note" title="Визуальный редактор">contentEditable</option>
		<!-- <option class="core warning" title="Открыть файл">editFile</option> -->
		</select>';

		if(count($content_htm = $idf->natSort()))
		{
			foreach($content_htm as &$htm) {
				$path= \Site::getPathFromRoot($htm);
				if($cond) echo "$eswitcher<div data-path=\"$path\" class=\"editor\">";
				include_once $htm;
				if($cond) echo "</div>";
			}
		}

		# Add thumbs
		// todo -> plugins
		if(\MODULES['Thumb']['enable'] && (!isset($Data['thumb']) || $Data['thumb'] == true) && $images)
			echo \H::includeModule('Thumb')->toPage();
			// exit;

		//* Add content from *.md files
		if(count($content_md = (new \DirFilter($idf->iterator, "#\.(md)$#"))->natSort()))
		{
			foreach($content_md as &$md) {
				$out .= file_get_contents($md);
			}

			echo \H::includeModule('Parsedown')->text($out);
			// require_once 'php/modules/Parsedown.php';
			// echo (new \Parsedown)->text($out);
		}

		$content = ob_get_clean();

		$content.= \Plugins::getHook('content');
		$content.= \Plugins::getHook('integration_pages');

		tolog(__METHOD__,null,['$Data'=>$Data,$Data]);

		return "<header>
		<h1" . (!empty($Data['hidden']) ? " class=hidden" : "") . ">{$Data['title']}</h1>
		</header>\n$content";
	}



	/**
	 ** Отдаём собранный контент
	 */
	public static function content()

	{
		$out = '';

		$Data= &\Page::$Data;

		if(!\ADMIN && !empty($Data['hidden']))
			\Site::shead(404);

		$content = self::contentCollect(\Page::$DIR);
		// var_dump(\DIR, $content); exit;

		if(strlen($content)){
			# Microtemplater
			$content = str_replace(
				[
					'{DIR}',
					'--', '---'
				],
				[
					'/' . \DIR,
					'–', '—'
				],
			$content);
			$content = "<div class=\"content\">\n{$content}\n</div>\n<!-- /.content -->\n";

			# Add comments & return
			#
			// $content = self::breadCrumbs() . $content;

			// !Disabled
			if(false && \MODULES['comments'])
				$content .= self::comments();

			/*  */
			if(!empty(\CF['counter']) && !\LOCALHOST)
			{
				$content .= \CF['counter'];
			}

			$content .= '<div class="DA_del">';

			// note deprecated
			/* if(\ADMIN || \TEST)
			{
				# Выводим логи
				foreach(\H::$log as $log) {
					$content .= "<pre class='core warning' style='max-height: 200px; overflow: auto;'>$log</pre>";
				}

			} */

			$content .= '</div> <!--/.DA_del-->';
		}

		return $content;

	} // content


	public static function comments()
	:string
	{
		global $Data;

		if(isset($Data['comments']) && $Data['comments'] == -1)
			return '';

		# Add comments
		ob_start();
		echo '<section id="comments">';
		require_once 'php/modules/comments/comments.php';
		echo '</section>';

		return ob_get_clean();
	}



	// todo
	public static function breadCrumbsRecurse($arr = [])
	{
		global $Nav;

		// $arr = explode('/', \H::$fileInfo->getPath());


		$crumb_path = dirname($crumb_path ?? \DIR);
		if(\Navigate::skipSlashes($crumb_path) === \Navigate::skipSlashes(\CONT)) return;

		var_dump($crumb_path);
		$data = $Nav->setData($crumb_path);
		var_dump($data);
		$arr[$data['title']] = $crumb_path;

		self::breadCrumbsRecurse($arr);
		die;


		$str = '<div id="breadcrumbs" style="margin: 15px 0 -2em;">';
		$path = '/' . \CONT;

		for ($i=1; $i < count($arr); $i++) {
			$c = $arr[$i];
			$path .= "$c/";
			$str .= "<a href=\"$path\" title=$c>$c</a> &middot; ";
		}

		return $str . '</div>';
	}


	public static function footer()
	: string
	{
		$f= \Plugins::getHook('footer');

		$f .= \H::addFromDir('js/__defer/', [
			'ext' => 'js',
			'defer' => 1,
			'except' => \ADMIN || USE_BROWS_LESS ? 0 : 'LESS'
		]);

		if(\ADMIN) $f .= '<script src="/js/modules/admin.js"></script>';

		return $f;
	} // footer


	protected static function createAdminItem ($name, $val)
	:string
	{
		$str = '';
		if(is_array($val))
		{
			foreach($val as $n => &$i) {
				$str .= self::createAdminItem("$name-$n", $i);
			}
		}
		else
		{
			$str = "<div class=\"flex\"><label>$name</label> - <input value=\"" . $val . "\"></div>";
		}
		return $str;
	}


	public static function adminBlock ()
	:string
	{
		if(!\ADMIN) return '';

		ob_start();
		?>

		<button id="save-data" class="core message button" onclick="_A.saveSettings($(this))">Save</button>
		<div id="page_settings">
		<?php
		// var_dump($Data);
		// if(!count($Data)) $Data = ["title" => "Untitled"];

		foreach(\Page::$Data as $name => &$val) {
			if($name === 'template' && $val === \TEMPLATE) continue;
			echo self::createAdminItem($name, $val);

		}
		?>

		</div>
		<button id="add_setting" class="core note button" onclick="_A.addSetting($('#page_settings'))">Add NEW</button>

		<?php
		return "<pre id=\"adm\" class=\"DA_del\">" . ob_get_clean() . '</pre>';

	} // adminBlock


	/**
	 * final Output
	 */
	public static function finalPage ($opts = [])
	: string
	{
		global $Nav, $Page;

		$Data= &\Page::$Data;

		tolog(['$Data'=>$Data]);

		# Wrap content in template

		require_once "{$Data['template']}/template.php";


		# Close main buff
		# opened in Site
		$html = ob_get_clean();

		$content = self::content();
		$footer = self::footer();

		// var_dump($html);

		// todo Admin
		if(\ADMIN) $html = preg_replace('~(<body)[^>]*>~', "$1 style=\"/*padding-top:15px;*/\">\n" . self::adminBlock(), $html, 1);

		tolog(__METHOD__,null,['\Site::$Page'=>\Site::$Page, $Page]);
		tolog(__METHOD__);

		$html = preg_replace([
			'~</head>~', '~</header>~', '~<!--\s*\$TITLE\$\s*-->~', '~<!--\s*\$CONTENT\$\s*-->~', '~</body>~'
		], [
			self::head() . "$0",
			\Plugins::getHook('header') . "\n$0",
			$Data['title'],
			'<div id="ajax-content">' . $content . "</div>\n",
			$footer . "\n$0"
		], $html, 1);

		\Plugins::$html= &$html;
		\Plugins::getHook('integration_end');

		// var_dump($Data);
		// var_dump($html);

		return $html;
	}
}