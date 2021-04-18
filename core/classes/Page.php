<?php

/**
 * undocumented class
 */
class Page
{
	use Get_set;

	public
		$headhtml='';

	static
		$fileInfo,
		// todo
		$cfg= ['admin'=>['name'=>'KorniloFF']],
		$Data,
		$DB,
		$DIR;

	protected static
		$SV;

	public function __construct()
	{
		// $this->content= $this->_setContent();

		// $this->deprecated();
		$this->defineData();

	}

	function defineData()
	{
		global $Data;
		$Data= &self::$Data;

		self::$DIR = self::$fileInfo->getPathname();
		self::$DB= new DbJSON(self::$DIR . '/data.json');

		$Data = $Data ?? self::setData(self::$fileInfo);
		/* self::$DB= new DbJSON;
		self::$DB->set($Data); */
		tolog(['Page::$DB'=>self::$DB]);

		return $Data;
	}


	/**
	 * @param kfi {string | kffFileInfo}
	 * return array $Data || 404
	 */
	public static function setData(kffFileInfo $kfi)
	{
		$name = Site::skipNum($kfi->getFilename());

		// $Data = \H::json(self::$DIR . '/data.json');
		$Data = self::$DB->get();

		$Data['title'] = $Data['title'] ?? \Site::translit(Site::skipNum($kfi), 1);
		$Data['template'] = $Data['template'] ?? \TEMPLATE;

		tolog(__METHOD__,null,['self::$DIR'=>self::$DIR,'$Data'=>$Data]);

		// *Current folder uri
		// define('DIR', $kfi->fromRoot() . '/');
		\H::$Dir = \DIR;

		return $Data;
	}


	public static function getData($path)
	{
		// tolog($path);
		$path= Site::getPathFromRoot($path);

		$Data= \H::json(\DR . "/$path/data.json");
		$Data['title'] = $Data['title'] ?? \Site::translit(Site::skipNum($path), 1);
		return $Data;
	}


	public static function setSV()
	{
		$SV= &self::$SV;

		if($SV) return $SV;

		$userCONST = get_defined_constants(true)['user'];
		$userCONST = array_filter($userCONST, function($i) {
			return !is_string($i) || !preg_match("/^HOST_IP/i", $i);
		});
		// var_dump($userCONST);
		$SV = "<script>\nwindow.sv =" . json_encode($userCONST, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK)
		. ";
		</script>\n";
		return $SV;
	}

	// ?
	protected function _setContent()
	{
		ob_start();
		// require_once
		return ob_get_clean();
	}


	/**
	 * !deprecated
	 */
	function deprecated()
	{
		# Main globals
		global $Nav, $Data, $Render, $SV;

		//* Remove first slash
		if(strpos($_SERVER['REQUEST_URI'], '/') === 0) $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);

		// $Data = $Nav->setData(\H::$fileInfo);
		self::$Data = self::$Data ?? self::setData(self::$fileInfo);
		$Data['template'] = $Data['template'] ?? \TEMPLATE;

		// var_dump($_REQUEST);

		if(\ADMIN && ($rqAdm = $_REQUEST['adm'] ?? null))
		{
			// var_dump($_REQUEST, \H::$fileInfo);

			# Reload #adm
			if(isset($rqAdm['loadSettings']))
			{
				echo php\classes\Render::adminBlock();
				die;
			}
			# Save local settins from #adm
			# admin.js
			if(isset($rqAdm['saveSettings']))
			{
				// var_dump($rqAdm['saveSettings']);

				$Data = $rqAdm['saveSettings'];
				\H::json(\DIR . 'data.json', $rqAdm['saveSettings'], 'rw');

				// die;
				$_REQUEST['ajax'] = 1;
			}

			// die;
		}


		define('AJAX', isset($_REQUEST['ajax']) ? ($_REQUEST['page'] ?? $_REQUEST['module']) : null);

		$this->d_setSV();

		// var_dump($Data);

		# Route to module
		if(isset($_REQUEST['module'])) {
			$module = strpos($_REQUEST['module'], '/') ? $_REQUEST['module'] : ('php/modules/' . $_REQUEST['module'] . '.php');
			/* var_dump($module, strpos($_REQUEST['module'], '/'));
			die; */
			if(file_exists($module))
				require_once $module;
			die;
		}

		if(\AJAX)
		{
			tolog(__METHOD__,null,['AJAX request'=>$_REQUEST]);
			header('Content-type: text/html; charset=utf-8');
			# CONST to ajax variable sv
			echo $SV;
			echo php\classes\Render::content();
			// echo $Render->content();

			die;
		} // AJAX


		\H::includeModule('SiteMap_RSS');
	} // __construct


	protected function d_setSV()
	{
		global $Nav, $SV;

		# Arr with images from current dir
		$imgs = (new \DirFilter(\H::$Dir, "#\.(jpe?g|png)$#"))->natSort();

		if(!count($imgs))
		{
			$imgs = (new \DirFilter($Nav->firstPage, "#\.(jpe?g|png)$#"))->natSort();
		}

		define('IMAGES', $imgs);

		if(!count(IMAGES))
		{
			define('BG', (new \DirFilter('assets/images/bg/', "#\.(jpe?g|png)$#"))->natSort());
		}

		# CONST to js variable sv
		$userCONST = get_defined_constants(true)['user'];
		$userCONST = array_filter($userCONST, function($i) {
			return !is_string($i) || !preg_match("/^HOST_IP/i", $i);
		});
		// var_dump($userCONST);
		$SV = "<script>\nwindow.sv =" . json_encode($userCONST, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK)
		. "; sv.firstPage = \"$Nav->firstPage\";
		</script>\n";
	}


}
