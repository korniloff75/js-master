<?php
require_once __DIR__."/Helpers.trait.php";

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

		// *dagam fix
		$_SERVER['DOCUMENT_ROOT']= str_replace('private_html','public_html', self::fixSlashes($_SERVER['DOCUMENT_ROOT']));
		// *Глобальный корень
		define( "GDR", $_SERVER['DOCUMENT_ROOT'] );
		// *Корень сайта
		$_SERVER['DOCUMENT_ROOT']= self::fixSlashes(dirname(__DIR__));
		define( "DR", $_SERVER['DOCUMENT_ROOT'] );

		// ?
		define( "POLLING", isset($_REQUEST["mode"]) && $_REQUEST["mode"] === 'list' );

		spl_autoload_register([__CLASS__,'_autoloader']);

		$this->_initLog();

		// $this->test();

		# Use singleton H
		// todo remake to Helper.trait
		require_once 'Helper.php';

		// var_dump($_REQUEST);

		if( !POLLING )
			session_start();

		// todo Plugins

	}

	private static function _autoloader($class)
	{
		$parts = explode('\\', $class);
		// tolog(__METHOD__,null,$parts);
		// var_dump($parts);

		$className = end($parts);
		if(file_exists($path= \DR."/core/classes/$className.php")){
			include_once $path;
		}
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



// *Admin
function is_adm()
{
	return !empty($_SESSION['adm']);
}