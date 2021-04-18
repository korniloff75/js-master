<?php
require_once __DIR__."/../Helpers.trait.php";
require_once \TRAITS."/Get_set.trait.php";


class Site
{
	use Helpers;

	use Get_set;

	const
		DEV= true,
		// DEV= false,
		// DBPATHNAME= \DR."/chat.db",
		// ARH_PATHNAME= \DR.'/db',
		// AUTH_PATHNAME= \DR.'/assets/adm.db.json',
		FILES_DIR= '/files_B',
		ADM= [
			'feedback'=>"<a href='//t.me/js_master_bot'>Telegram</a>",
		],
		TEMPLATE_DEFAULT= '_default_';

	static
		$Page;


	public function __construct()
	{
		$this->_bufferOpen();

		// ?
		define( "POLLING", isset($_REQUEST["mode"]) && $_REQUEST["mode"] === 'list' );
		define( "COOKIEPATH", "/" );

		require_once \DR . '/CONST.php';

		define('BASE_URL', 'http' . (self::is('https') ? 's' : '') . '://' . \HOST . '/');
		define('AJAX', self::is('ajax'));

		// autoload ->index.php

		// *Включаем логирование и проверяем доступ к api
		$this->_initLog()
			->_api();

		tolog(['$_GET'=>$_GET]);

		// *before Helper.php
		if( !POLLING )
			session_start();

		# Use singleton H
		// todo remake to Helper.trait
		require_once DR.'/Helper.php';

		tolog(__METHOD__,null,['$_SERVER[REQUEST_URI]'=>$_SERVER['REQUEST_URI']]);

		// *routing
		$this->_route();

		// todo make auth

		// var_dump($_REQUEST);

	}//__construct


	/* public static function autoloader()
	{
		spl_autoload_register(function($class){
			$namespace = explode('\\', $class);
			// tolog(__METHOD__,null,$namespace);
			// var_dump($namespace);

			$className = end($namespace);
			if(file_exists($path= \DR."/core/classes/$className.php")){
				include_once $path;
			}
		});
	} */


	private function _bufferOpen()
	{
		mb_internal_encoding( "UTF-8" );
		mb_http_output( "UTF-8" );
		// mb_http_input( "UTF-8" );
		mb_language( "uni" );
		mb_regex_encoding( "UTF-8" );

		//* Open main buff
		# closed in Render
		ob_start( "mb_output_handler" );
	}


	protected function _initLog()
	{
		// *Логгируем загрузку страницы
		// *Отсекаем поллинги
		if( isset($_REQUEST["dev"]) || !POLLING ){
			global $log;
			$log = new Logger('my.log', \DR);
		}
		elseif(!function_exists('tolog')) {
			function tolog(){}
		}

		tolog(__METHOD__,null,['DR'=>DR,'GDR'=>GDR, 'POLLING'=>POLLING]);
		return $this;
	}


	// *api
	protected function _api()
	{
		// *fix 4 polling
		$_SESSION = $_SESSION ?? [];

		// *Получаем данные из fetch
		$inp_data= json_decode(
			file_get_contents('php://input'),1
		);
		tolog(__METHOD__,null,['$inp_data'=>$inp_data]);

		// *Собираем все входящие в $_REQUEST
		if($inp_data){
			$_REQUEST= array_merge($_REQUEST, $inp_data);
		}

		if(empty($_REQUEST['api'])){
			return $this;
		}

		// $api=$_REQUEST['api']

		// *isset $api
		$this->set('api',filter_var($api));

		unset($_REQUEST['api']);


		if(file_exists($api= __DIR__."/../api/$api.php")){
			tolog(__METHOD__,null,['$api'=>$api, ]);

			include_once $api;
		}

		return $this;
	}


	protected function _route()
	{
		global $Page;

		// *Define $Page
		// $GLOBALS['Page']= &self::$Page;
		$Page= &self::$Page;

		// Page::$fileInfo= new kffFileInfo(\DR."/content/{$req['matches'][0]}");
		// tolog(__METHOD__,null,['AJAX request'=>$_REQUEST]);

		Router::route('^(?:site|content)/(.+)', function($req){
			// tolog([func_get_args()]);
			tolog(['$req'=>$req]);
			\Page::$fileInfo= new kffFileInfo(\DR."/content/{$req['matches'][0]}");

			// *Current folder uri
			define('DIR', \Page::$fileInfo->fromRoot() . '/');
			self::$Page= new Page();
			tolog(__METHOD__ . ": \$Page defined",null,['Site::$Page'=>self::$Page]);
		});


		if(!empty($_REQUEST['module'])){
			// *Запрос к модулю (комменты)
			if(!defined('DIR')) define('DIR', $_REQUEST['page'] . '/');
			tolog(['Request to module'=>$_REQUEST]);
			require_once \DR . "/{$_REQUEST['module']}";
			die;

		}


		if(\AJAX){
			// *flush Render::content() & exit
			Router::execute($_REQUEST['page']);

			tolog(__METHOD__,null,['AJAX request'=>$_REQUEST]);

			header('Content-type: text/html; charset=utf-8');
			# CONST to ajax variable sv
			echo $SV;
			echo php\classes\Render::content();
			// echo $Render->content();

			die;
		}

		// tolog(php\classes\Navigate::$firstPage);

		Router::execute();
	}//_route()



	function test()
	{
		$n= new \php\classes\Navigate;
		tolog(__METHOD__,null,['Navigate'=>$n]);
		die;
	}
} //Site
