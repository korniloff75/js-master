<?php

/*
Router::route('blog/(\w+)/(\d+)', function([$matches,$uri]){
  ...
});

// запускаем маршрутизатор, передавая ему запрошенный адрес
Router::execute($_SERVER['REQUEST_URI']);
*/

class Router
{
	// массив для хранения соответствия pattern => handler
	private static $routes = array();

	public static
		// defined in self::execute()
		$URI;

	private function __construct() {}
	private function __clone() {}


	// данный метод принимает pattern url-адреса
	// и связывает его с пользовательской функцией
	public static function route($pattern, $handler)
	{
		// $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/u';
		$pattern = '/' . str_replace('/', '\/', $pattern) . '/u';
		self::$routes[$pattern] = $handler;
	}


	// данный метод проверяет запрошенный $url(адрес) на
	// соответствие адресам, хранящимся в массиве $routes
	public static function execute(?string $url=null)
	{
		// $url= $url ?? $_GET["route"] ?? $_SERVER['REQUEST_URI'];
		$url= $url ?? $_GET["route"];

		//* bug with start page
		if(
			empty($url)
			&& empty($url= php\classes\Navigate::$firstPage)
		){
			tolog("\$url is EMPTY!",E_USER_ERROR,[]);
			Site::shead(404);
		}

		foreach (self::$routes as $pattern => $handler){

			if (!preg_match($pattern, $url, $params)){
				continue;
			}
			// if (preg_match_all($pattern, $url, $params)){
			// *удаляем первый элемент из массива $params, который содержит всю найденную строку
			array_shift($params);

			$args= ['matches'=>array_values($params), 'uri'=>array_values(array_filter(explode('/',$url)))];
			self::$URI = $args['uri'];

			tolog(__METHOD__,null,['$pattern'=>$pattern,'$args'=>$args]);

			return call_user_func($handler, $args);
		}

		\Site::shead(404);
	}
}