<?php
namespace php\classes;
\H::protectScript(basename(__FILE__));

class Navigate

{
	const
		ALLOWED = \CONT . "((?!thumb|img|PHPMailer|assets).)*";

	// private static $log;

	public $firstPage;


	function __construct($dir=null)

	{
		global $First_page;

		$dir = self::checkContDir($dir ?? \CONT);

		$this->allowed_dir = "#^" . self::ALLOWED . "$#u";
		$this->allowed_file = "#^" . self::ALLOWED . "\.(php|htm)$#u";

		$this->map_path = $map_path = $dir . 'map_content.json';

		#
		$this->mapObj = new \DbJSON($map_path);
		// $this->mapObj->test=1;

		tolog(__METHOD__,null,['count($this->mapObj)'=>count($this->mapObj), /* '$this->mapObj->get()'=>$this->mapObj->get() */]);

		if(!count($this->mapObj))
		{
			$this->mapObj->replace($this->createMap());
			// note Нужно сразу записать, понять нельзя!
			$this->mapObj->save();
			// trigger_error(__METHOD__." \$this->mapObj->db has count ");
			tolog(__METHOD__,null,['count($this->mapObj)'=>count($this->mapObj), $this->mapObj->count()]);
		}

		# Flat file array
		$this->map_flat = $this->mapObj->getFlat();
		natsort($this->map_flat);

		$this->firstPage = $this->map_flat[0];

	}


	# Remove numbers from names like 1.file.pfp
	public static function skipNum(string $name)
	:string
	{
		return preg_replace("#^(\d+\.)?(.+?)(\..+)?$#", "$2", basename($name));
	}


	# skipSlashes
	# /path/to/ -> path/to
	public static function skipSlashes(string $path)
	:string
	{
		$path = preg_replace("#^/|/$#", "", $path);
		// var_dump($re);
		return $path;
	}


	public static function checkContDir($dir=null)

	{
		$dir= $dir ?? \CONT;
		if(!is_dir($dir)) mkdir($dir);
		if(!file_exists("$dir.htaccess"))
			copy('assets/htaccess4content.txt', "$dir.htaccess");
		return $dir;
	}


	public
		$map_arr = [],
		$global_map = [];

	public function createMap($dir = \CONT, $pd = null)

	{
		$dirs_all = ( new \RecursiveOnlyDirFilter(\CONT, $this->allowed_dir) )->natSort();

		// var_dump($dirs);

		foreach($dirs_all as $k=>&$d) {
			if(count( (new \OnlyDirFilter($d, $this->allowed_dir) )->natSort()))
			{
				unset($dirs_all[$k]);
				continue;
			}
			else
				$d .= '/';

			eval('$this->map_arr["' . implode("\"][\"", array_filter(explode("/", $d))) . "\"] = \"$d\";");

		}

		# Flat file array
		$this->map_flat = array_values($dirs_all);

		// var_dump($dirs_all/* , $this->map_arr */);

		return $this->map_arr;

	} // createMap


	// todo В разработке
	public function createGlobalMap($map = null, $ref=\CONT)
	{
		$map = $map ?? $this->mapObj->get();

		foreach($map as $title => &$item_val) {
			$path = $ref . $title . '/';
			$data = self::setData($path);
			// var_dump($data);
			$class = '';

			if(!empty($data['hidden']))
			{
				if(!\ADMIN) continue;
				else $class .= "hidden ";
			}

			// var_dump($item_val);
			if(is_array($item_val))
			{
				# Submenu
				$class .= "folder ";
				// var_dump($item_val, $data);
				// $keys = array_keys($item_val);

				// $f_ch = $path . '/' . $keys[0];

				echo "<li class=\"$class\">"
				. $data['title']
				. "<ul>";

				# Recurse
				self::readMap($item_val, $path);

				echo "</ul>";
			}
			else
			{
				# Top level items
				// var_dump($data);
				$this->global_map[$title] = [
					'data' => $data
				];
				echo "<li class=\"$class\">"
				. '<a href="/' . $path . '">'
				. $data['title']
				. '</a>';
			}

			echo '</li>';
			// \H::translit($this->skipNum($path), 1);

		} // foreach
	} // createGlobalMap


	public static function readMap (array $map, $ref=\CONT)

	{
		foreach($map as $title => $cont_d) {
			$path = $ref . $title . '/';
			$data = self::setData($path);
			// var_dump($data);
			$class = '';

			if(!empty($data['hidden']))
			{
				if(!\ADMIN) continue;
				else $class .= "hidden ";
			}

			// var_dump($cont_d);
			if(is_array($cont_d))
			{
				# Submenu
				$class .= "folder ";
				// var_dump($cont_d, $data);
				// $keys = array_keys($cont_d);

				// $f_ch = $path . '/' . $keys[0];

				echo "<li class=\"$class\">"
				. $data['title']
				. "<ul>";

				# Recurse
				self::readMap($cont_d, $path);

				echo "</ul>";
			}
			else
			{
				# Top level items
				// var_dump($data);
				echo "<li class=\"$class\">"
				. '<a href="/' . $path . '">'
				. $data['title']
				. '</a>';
			}

			echo '</li>';
			// \H::translit($this->skipNum($path), 1);

		} // foreach($map as $title => $cont_d)
	}


	#  generate #menu_block
	public function genMenu(string $dir = null)
	:string
	{
		$dir = $dir ?? self::skipSlashes(\CONT);

		ob_start();
		?>

		<nav id="menu_content">\n
			<ul id="menu">

			<?php $this->readMap($this->mapObj->get($dir))?>

			</ul>
		<!-- #menu -->
		</nav>

		<div>
			<? if(!\ADMIN):?>
			<a href="/?login&action=form" style="position: absolute; bottom:0;" rel="nofollow">Login</a>
			<?else:?>
			<a href="/?login=logout" style="position: absolute; bottom:0;" rel="nofollow">Logout</a>
			<?endif?>
		</div>

		<?php
		// var_dump($this->mapObj->db);

		return ob_get_clean();
	}


	public static function setData($path)

	{
		# \H::$fileInfo
		# Current page - object SplFileInfo
		// if(is_object($path))
		if($path instanceof SplFileInfo)
		{
			$dir = \H::$Dir;
			$name = self::skipNum($path->getFilename());
		}
		else $dir = $path;

		$Data = \H::json($dir . '/data.json');

		$Data['title'] = $Data['title'] ?? \H::translit(self::skipNum($path), 1);

		// var_dump($Data);

		return $Data;
	}


	public function genNavMain()
	:string
	{
		$out='<nav id="nav_block">';

		foreach($this->map_flat as $i) {
			$out .= "\n<div class=\"nav_item\" data-page=\"" . \Path::fromRootStat($i) . "\" title=\"" . $i . "\"><div></div></div>";
		}
		return $out . "\n</nav>";
	}
};