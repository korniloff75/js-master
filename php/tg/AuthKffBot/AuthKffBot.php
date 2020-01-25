<?php
// https://js-master.ru/php/tg/AuthKffBot/AuthKffBot.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once "../CommonBot.class.php";


class AuthKffBot extends CommonBot
{
	protected
		# Test mode, bool
		$__test = 1 ;


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		parent::__construct()->init();

	} //__construct

	/**
	 *
	 */
	private function init()
	{
		if(empty($this->inputData) || !$this->message)
		{
			$this->log->add('Нет входящего запроса');
			die ('Нет входящего запроса');
		}

		// $siteUrl = "https://js-master.ru";
		$siteUrl = "http://invs.js-master.ru";

		// $respTG = $this->apiResponseJSON([
		$respTG = $this->apiRequest([
			'text' => "Do you want to authorize in the $siteUrl?",
			'reply_markup' => [
				"inline_keyboard" => [[[
					'text' => 'Login',
					'login_url' => [
						'url' => $siteUrl . '/examples/BotStats/',
						'forward_text' => "Войти на сайт $siteUrl под своей учётной записью",
						// 'request_write_access' => true,
					]
				]]],
				"one_time_keyboard" => true,
				"resize_keyboard" => true,
				"selective" => true
			]
		]);

		$this->log->add(__METHOD__ . ' $respTG = ',null,[$respTG]);

		die('OK');

	} //* init

} //* AuthKffBot

new AuthKffBot;