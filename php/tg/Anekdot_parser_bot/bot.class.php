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


class BotRouter
{
	const
		CLASSES= [
			'Anekdot.class'
		];

	public function __construct()
	{
		//* Start from crontab
		if(!empty($_SERVER['argv'][1]))
		{
			$this->route($_SERVER['argv'][1]);
		}
		//* Start from array
		else foreach (self::CLASSES as $fn)
		{
			$this->route($fn);
		}
	}


	private function route($fn)
	{
		$class = explode('.',$fn)[0];
		require_once __DIR__."/$fn.php";
		new $class;
	}
}

new BotRouter;
