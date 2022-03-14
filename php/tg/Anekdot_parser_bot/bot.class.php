#!/usr/bin/php
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once __DIR__ . "/../CommonBot.class.php";


//* FIX cron
/* if(CLI && empty($_SERVER['DOCUMENT_ROOT'])){
	$_SERVER = array_merge($_SERVER, [
		'DOCUMENT_ROOT' => realpath(__DIR__ . '/../..'),
	]);
} */

require_once $_SERVER['DOCUMENT_ROOT'] . "/core/traits/Parser.trait.php";
//? require_once \DR . "/core/traits/Parser.trait.php";



class BotRouter
{
	const
		CLASSES= [
			'Anekdot.class'
		];


	public function __construct()
	{
		$classes= CLI && !empty($_SERVER['argv'][1])?
			array_slice($_SERVER['argv'],1)
			:self::CLASSES;

		foreach ($classes as $cn){
			$this->execBot($cn);
		}

		/* note deprecated
		//* Start from crontab
		if(!empty($_SERVER['argv'][1]))
		{
			$this->execBot($_SERVER['argv'][1]);
		}
		//* Start from array
		else foreach (self::CLASSES as $fn)
		{
			$this->execBot($fn);
		} */
	}


	private function execBot($cn)
	{
		$class = explode('.',$cn)[0];
		require_once __DIR__."/$cn.php";
		new $class;
		usleep(1000);
	}
}

new BotRouter;
