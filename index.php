<?php
// use php\classes;

if (version_compare(PHP_VERSION, '7.1', '<') ) die("Извини, брат, с пыхом ниже 7.1 - не судьба!\n");

/* ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1); */

$START_PROFILE = microtime(true);

// *Автозагрузка, Логгер и основные константы
require_once __DIR__.'/core/define.php';

$Site= new Site;

$Router = new Router;

// var_dump($Nav->map);
// exit;

# Rendering full page
header('Content-type: text/html; charset=utf-8');
echo php\classes\Render::finalPage();
# /html
