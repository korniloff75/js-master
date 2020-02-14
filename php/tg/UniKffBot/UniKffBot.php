<?php

require_once "../CommonBot.class.php";

class UniKffBot extends CommonBot implements Game
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
		if(in_array($cmdName, self::BTNS))
			list($cmdName, $cmd) = ['GameTest', array_flip(self::BTNS)[$cmdName]];

		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);
			//* Aliases
			if($cmdName === 'Draws') $cmdName = 'GameTest';

			switch ($cmdName)
			{
				case 'Gismeteo':
				case 'Youtube':
				case 'Zen':
				case 'GameTest':
					require_once("extensions/$cmdName.php");
					new $cmdName($this, $cmd);
					break;
				case 'All':
					$this->sendToAll($cmd);
					break;

				default:
					$this->log->add(__METHOD__ . ' switch default', E_USER_WARNING);
					break;
			}
		}

	} //* Router

	private function sendToAll($txt)
	{
		$txt = str_replace(
			['!','синий','жёлтый'],
			['❗️','синий🔷','рыжий🔶'],
			$txt
		);

		foreach(array_keys($this->license) as $id)
		{
			$this->apiRequest([
				'chat_id'=> $id,
				'text'=> "❗️❗️❗️\n$txt",
			]);
		}
	}
} //* UniKffBot


interface Game {
	//* Command list
	const BTNS = [
		'general'=>'⬅️Главная',
		'balance'=>'💰Баланс',
		'info'=>'💡Информация',
		'help'=>'❓Помощь',
		'settings'=>'⚙️Настройки',
		'community'=>'💬Community',
		'new draw'=>'Создать розыгрыш',
		'play draw'=>'Разыграть',
		'show participants'=>'Участники',
		'participate'=>'Участвовать',
		'advanced'=>'Дополнительно',
	];
}

interface Draws {
	const INFO = [
		'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.\n/draws - Группа с розыгрышами, где любой участник может создавать розыгрыши, а также участвовать в существующих.",
		'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
		'settings'=>'Какие нужны индивидуальные настройки? Пишите @korniloff75',
		'advanced'=>'',
	];
}


$UKB = new UniKffBot;