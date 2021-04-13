<?php
require_once __DIR__."/../Helpers.trait.php";

class Site
{
	use Helpers;

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


	public function __construct()
	{
		$this->_bufferOpen();

		// ?
		define( "POLLING", isset($_REQUEST["mode"]) && $_REQUEST["mode"] === 'list' );

		// autoload

		$this->_initLog();

		// $this->test();

		// todo make auth

		// *Admin
		function is_adm()
		{
			return !empty($_SESSION['adm']);
		}

		# Use singleton H
		// todo remake to Helper.trait
		require_once DR.'/Helper.php';

		// var_dump($_REQUEST);

		if( !POLLING )
			session_start();

		// todo Plugins

	}

	public static function autoloader()
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
	}


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

		date_default_timezone_set ('Europe/Moscow');

		define( "COOKIEPATH", "/" );
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
	}


	function test()
	{
		$n= new \php\classes\Navigate;
		tolog(__METHOD__,null,['Navigate'=>$n]);
		die;
	}
} //Site

