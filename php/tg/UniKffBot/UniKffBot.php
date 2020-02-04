<?php

require_once "../CommonBot.class.php";

class UniKffBot extends CommonBot
{
	protected
		# Test mode, bool
		$__test = 1;


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		//* Запускаем скрипт
		parent::__construct()->checkLicense()->Router();

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

	} //* init


	private function Router()
	{
		$inputData = $this->cbn['data'] ?? $this->message["text"];

		//* FIX multibots
		$inputData= explode('@', $inputData)[0];
		//* Define command
		list($cmdName, $cmd) = array_values(array_filter(explode('/', $inputData)));

		$this->log->add(__METHOD__ . ' input = ', null, [$inputData, $cmdName, $cmd]);

		//* Приходит локация
		if(!empty($this->message['location']))
			list($cmdName, $cmd) = ['gismeteo', 'setLocation'];

		//* GAME
		if(in_array($cmdName, self::GAME))
			list($cmdName, $cmd) = ['GameTest', array_flip(self::GAME)[$cmdName]];

		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);
			switch ($cmdName)
			{
				case 'Gismeteo':
				case 'Youtube':
				case 'Zen':
				case 'GameTest':
					require_once("extensions/$cmdName.php");
					new $cmdName($this, $cmd);
					break;

				default:
					$this->log->add(__METHOD__ . ' switch default', E_USER_WARNING);
					break;
			}
		}

	}

	const
		GAME = [
			'general'=>'⬅️Главная',
			'balance'=>'💰Баланс',
			'info'=>'💡Информация',
			'help'=>'❓Помощь',
			'settings'=>'⚙️Настройки',
			'community'=>'💬Community',
			'new draw'=>'Создать розыгрыш',
			'play draw'=>'Разыграть',
			'show participates'=>'Участники',
			'participate'=>'Участвовать',
			'advanced'=>'Дополнительно',
		];

} //* UniKffBot


$UKB = new UniKffBot;