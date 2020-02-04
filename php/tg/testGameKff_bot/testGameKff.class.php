<?php

require_once "../CommonBot.class.php";


class testGameKff extends CommonBot
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
	protected function init()
	{
		// $this->showMainMenu();
		$this->apiResponseJSON([
			'text' => 'Sample',
		]);
	} //* init


	private function showMainMenu()
	{
		// $this->apiResponseJSON([
		$this->apiRequest([
			'text' => 'Сделайте свой выбор.',
			'game_short_name' => 'testGameKff',
			/* 'reply_markup' => [
				"keyboard" => [[[
					'text' => 'btn1',
					'request_poll' => ['type'=>'testttttt'],
				]]],
				"one_time_keyboard" => false,
				"resize_keyboard" => true
			], */
		], 'sendGame');
	} //* showMainMenu

}

new testGameKff;