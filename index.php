<?php
// use php\classes;

if (version_compare(PHP_VERSION, '7.1', '<') ) die("Извини, брат, с пыхом ниже 7.1 - не судьба!\n");

/* ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

$START_PROFILE = microtime(true);

// *Автозагрузка
spl_autoload_register(function($class){
	$namespace = explode('\\', $class);
	// tolog(__METHOD__,null,$namespace);
	// var_dump($namespace);

	$className = end($namespace);
	if(file_exists($path= __DIR__."/core/classes/$className.php")){
		include_once $path;
	}
});


// *dagam fix
$_SERVER['DOCUMENT_ROOT']= str_replace('private_html','public_html', Site::fixSlashes($_SERVER['DOCUMENT_ROOT']));
// *Глобальный корень
define( "GDR", $_SERVER['DOCUMENT_ROOT'] );
// *Корень сайта
$_SERVER['DOCUMENT_ROOT']= Site::fixSlashes(__DIR__);
define( "DR", $_SERVER['DOCUMENT_ROOT'] );


// *Логгер и основные константы
$Site= new Site;

$Router = new Router;

// var_dump($Nav->map);
// exit;

# Rendering full page
header('Content-type: text/html; charset=utf-8');
echo php\classes\Render::finalPage();
# /html
