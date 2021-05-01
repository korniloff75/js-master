<?php
// *before Site, used with bots
// fixed v.3.2
define( "TRAITS", realpath(__DIR__."/../traits") );

require_once __DIR__."/../Helpers.trait.php";
require_once \TRAITS."/Get_set.trait.php";

$_GET['route']= $_GET['route'] ?? null;


class Site implements BasicClassInterface
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
		// *Отдаём файлы
		/* if(!file_exists($fpath= "./{$_GET['route']}")){
			self::shead(404);
		} */
		if(is_file($fpath= "./{$_GET['route']}")){
			require_once $fpath;
			die;
		}

		$this->_bufferOpen();

		// ?
		define( "POLLING", isset($_REQUEST["mode"]) && $_REQUEST["mode"] === 'list' );
		define( "COOKIEPATH", "/" );

		require_once \DR . '/CONST.php';

		define('BASE_URL', 'http' . (self::is('https') ? 's' : '') . '://' . \HOST . '/');
		define('AJAX', self::is('ajax'));

		// autoload ->index.

		// *fix 4 polling
		$_SESSION = $_SESSION ?? [];

		// *Получаем данные из fetch
		$inp_data= json_decode(
			file_get_contents('php://input'),1
		);

		// *Включаем логирование и проверяем доступ к api
		$this->_initLog();
			// ->_api();

		// *Собираем все входящие в $_REQUEST
		if($inp_data){
			tolog(__METHOD__,null,['$inp_data'=>$inp_data]);
			$_REQUEST= array_merge($_REQUEST, $inp_data);
		}

		tolog(['$_GET'=>$_GET]);

		// *before Helper.php
		if( !POLLING && empty($_SESSION) )
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
		// if( isset($_REQUEST["dev"]) || !POLLING && is_adm() ){
		if( isset($_REQUEST["dev"]) || !POLLING ){
			global $log;
			$log = new Logger('my.log', \DR);
		}
		elseif(!function_exists('tolog')) {
			ini_set('display_errors', 0);
			function tolog(){}
		}

		tolog(__METHOD__,null,['DR'=>DR,'GDR'=>GDR, 'POLLING'=>POLLING]);
		return $this;
	}


	// *api
	protected function _api()
	{
		if(empty($_REQUEST['api'])){
			return $this;
		}

		$api= $_REQUEST['api'];

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
		global $act;

		// *Compat ME
		$act= filter_var($_REQUEST['act'] ?? null, FILTER_SANITIZE_STRING);

		// *Запрос к модулю (комменты, ...etc.)
		if(!empty($_REQUEST['module'])){
			if(!defined('DIR')) define('DIR', $_REQUEST['page'] . '/');

			// compat with Legacy
			if(!file_exists($module=\DR . "/{$_REQUEST['module']}")){
				$module = \DR . "/php/modules/{$_REQUEST['module']}.php";
			}

			tolog(['Request to module'=>$_REQUEST,'$act'=>$act]);

			\Logger::$notWrite = true;
			require_once $module;
			die;
		}

		// *Request to API
		Router::route('api/(.+)$', function($req){
			$api= $req['uri'][1];

			if(file_exists($api= __DIR__."/../api/$api")){
				tolog(__METHOD__,null,['$api'=>$api, ]);

				include_once $api;
			}
			die;
		});

		// *Обновление админ-панели
		Router::route('^(.+)/\?updAdminBlock', function($req){
			// tolog([func_get_args()]);
			tolog(['$req'=>$req]);
			\Page::$fileInfo= new kffFileInfo(\DR."/{$req['matches'][0]}");

			self::$Page= new Page();
			echo \php\classes\Render::adminBlock();
			\Logger::$notWrite = true;
			die;
		});

		// *Запрос к странице
		Router::route('^(?:site|content)/(.+)', function($req){
			// tolog([func_get_args()]);
			tolog(['$req'=>$req]);
			\Page::$fileInfo= new kffFileInfo(\DR."/content/{$req['matches'][0]}");

			self::$Page= new Page();
			tolog(__METHOD__ . ": \$Page defined",null,['DIR'=>\DIR,'Site::$Page'=>self::$Page]);
		});

		//todo *Запрос к примерам
		Router::route('(examples/.+)', function($req){
			// tolog([func_get_args()]);
			tolog(['$req'=>$req]);
			\Page::$fileInfo= new kffFileInfo(\DR."/{$req['matches'][0]}");

			self::$Page= new Page();
			tolog(__METHOD__ . ": \$Page defined",null,['DIR'=>\DIR,'Site::$Page'=>self::$Page]);
		});


		if(\AJAX){
			ob_clean();
			// *flush Render::content() & exit
			Router::execute($_REQUEST['page']);

			tolog(__METHOD__,null,['AJAX request'=>$_REQUEST]);

			header('Content-type: text/html; charset=utf-8');
			//? CONST to ajax variable sv
			echo \Page::setSV() . "\n"
			. php\classes\Render::content();

			ob_end_flush();
			die;
		}
		else
			Router::execute();


		// tolog(php\classes\Navigate::$firstPage);

	}//_route()



	function test()
	{
		$n= new \php\classes\Navigate;
		tolog(__METHOD__,null,['Navigate'=>$n]);
		die;
	}
} //Site
