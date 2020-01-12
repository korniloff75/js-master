<?php
// use php\classes;

if (version_compare(PHP_VERSION, '7.1', '<') ) die("Извини, брат, с пыхом ниже 7.1 - не судьба!\n");

date_default_timezone_set('Europe/Moscow');


# Open main buff
# closed in Render
ob_start();
session_start();


# Use singleton H
require_once 'Helper.php';
// if(array_key_exists('login', $_REQUEST))
// 	H::includeModule('Login');

/* \H::log([
	'echo "\$_REQUEST =" ', 'var_dump($_REQUEST)',
	'echo "H::\$Dir = " . H::$Dir ',
], __FILE__, __LINE__); */
// exit;

# Fork on modules || AJAX || full page
# & passing $SV to js
$Router = new Router;

// var_dump($Nav->map);
// exit;

# Rendering full page
header('Content-type: text/html; charset=utf-8');
echo php\classes\Render::finalPage();
# /html

// Переделать логирование на Logger
file_put_contents('kffLog.txt', H::$log);
# Write input data
// $testInputData = file_get_contents('php://input');
// if(strlen($testInputData)) file_put_contents('testInputData.json', $testInputData);
#############
