<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

require_once __DIR__ . "/../tg.class.php";


class Info extends TG implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		# @KffTestBot
		$token = '921770806:AAHGGXko7zQj6xIRSpgeGgQTqQPs0HfMbIo';


	public function __construct()
	{
		$this->botFileInfo = new kffFileInfo(__FILE__);

		parent::__construct();

		ob_start();
		print_r($this->inputData);
		$json = ob_get_clean();

		$info = [
			'chat_id' => $this->chat_id,
			'parse_mode' => 'markdown',
			'text' => "```\n{$json}\n```",
		];

		$this->log->add("info = ", null, $info);

		$this->apiResponseJSON($info);

	} //__construct

	/**
	 *
	 */
	public function init()
	{

		die('OK');

	} // init

} // Info

new Info;