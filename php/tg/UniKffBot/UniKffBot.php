<?php

require_once "../CommonBot.class.php";


class UniKffBot extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token;


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		//* Запускаем скрипт
		parent::__construct()->checkLicense()->init();

	} //__construct

	/**
	 *
	 */
	private function init()
	{
		//* Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->Router();

		die('OK');

	} // init


	private function Router()
	{
		switch ($this->cbn["data"]) {
			case '/gismeteo':
				require_once('gismeteo.php');
				break;

			default:
				# code...
				break;
		}


	}
} // UniKffBot


new UniKffBot;