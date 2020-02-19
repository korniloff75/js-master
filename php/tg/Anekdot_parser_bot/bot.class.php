#!/usr/bin/php
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once __DIR__ . "/../CommonBot.class.php";

//* FIX cron
if(php_sapi_name() === 'cli' && empty($_SERVER['DOCUMENT_ROOT']))
{
	$_SERVER = array_merge($_SERVER, [
		'DOCUMENT_ROOT' => realpath(__DIR__ . '/../..'),
	]);
}
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Parser.trait.php";
// echo "\$_SERVER['DOCUMENT_ROOT'] = ". $_SERVER['DOCUMENT_ROOT'];

function Router($fn)
{
	$class = explode('.',$fn)[0];
	require_once __DIR__."/$fn.php";
	new $class;
}

$classes= [
	'Anekdot.class'
];

//* Start from crontab
if(!empty($_SERVER['argv'][1]))
{
	Router($_SERVER['argv'][1]);
}
//* Start from array
else foreach ($classes as $fn)
{
	Router($fn);
}