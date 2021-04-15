<?php
class Router
{
	function __construct()
	{
		# Main globals
		global $Nav, $Data, $Render, $SV;

		//* Remove first slash
		if(strpos($_SERVER['REQUEST_URI'], '/') === 0) $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1);

		$Data = $Nav->setData(\H::$fileInfo);
		$Data['template'] = $Data['template'] ?? \TEMPLATE;

		// var_dump($_REQUEST);

		if(\ADMIN && ($rq = $_REQUEST['adm'] ?? null))
		{
			// var_dump($_REQUEST, \H::$fileInfo);

			# Reload #adm
			if(isset($rq['loadSettings']))
			{
				echo php\classes\Render::adminBlock();
				die;
			}
			# Save local settins from #adm
			# admin.js
			if(isset($rq['saveSettings']))
			{
				// var_dump($rq['saveSettings']);

				$Data = $rq['saveSettings'];
				\H::json(\DIR . 'data.json', $rq['saveSettings'], 'rw');

				// die;
				$_REQUEST['ajax'] = 1;
			}

			// die;
		}


		define('AJAX', isset($_REQUEST['ajax']) ? ($_REQUEST['page'] ?? $_REQUEST['module']) : null);

		$this->setSV();

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
			header('Content-type: text/html; charset=utf-8');
			# CONST to ajax variable sv
			echo $SV;
			echo php\classes\Render::content();
			// echo $Render->content();

			die;
		} // AJAX


		\H::includeModule('SiteMap_RSS');
	} // __construct


	protected function setSV()
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