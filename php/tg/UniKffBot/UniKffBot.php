<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once "../CommonBot.class.php";


class UniKffBot extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token,
		$gismeteoToken;


	public function __construct()
	{
		// \H::json('license.json', $this->license);
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		# Protect from CommonBot
		// $this->checkLicense();
		parent::__construct()->checkLicense()->init();

	} //__construct

	/**
	 *
	 */
	private function init()
	{
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		

		# Запускаем скрипт
		# Protect from CommonBot
		parent::__construct()->checkLicense()->init();

		die('OK');

	} // init


	private function Router()
	{
		$responseData = $this->responseData;

	}
} // UniKffBot


new UniKffBot;