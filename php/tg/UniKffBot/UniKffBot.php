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
		$btns_val = array_flip(self::BTNS);

		if(in_array($cmdName, self::BTNS))
			list($cmdName, $cmd) = [
				'GameTest', !is_numeric($btns_val[$cmdName])
				? $btns_val[$cmdName]
				: "{$cmdName}__{$cmd}"
			];

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
		'pump market'=>'Биржа 泵 насосов',
		'sale blue pump'=>'🔷泵🔷',
		'sale gold pump'=>'🔶泵🔶',
		'sale',
	];
}

interface Draws {
	const INFO = [
		'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.\n/draws - Группа с розыгрышами, где любой участник может создавать розыгрыши, а также участвовать в существующих.",
		'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
		'settings'=>'Какие нужны индивидуальные настройки? Пишите @korniloff75',
		'advanced'=>'',
		'help'=>"Поможем всем!\nТут будут ссылки на поддержку. Скорее всего, инлайн-кнопками.",
		'pump market'=>"Приветствую в клановой бирже насосов!\nЗдесь можно размещать информацию по своим насосам, которые планируются на продажу в общей бирже.",
		'sale blue pump'=>"Для добавления насоса в список введите команду в формате sale/blue__DATE__NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, DATE - дата планируемой поломки, NUMBER - номер насоса.\n<u>Например:</u>\nsale/blue__2020-09-02__5380",
		'sale'=> [
			'fail'=> 'Введены неверные данные. Попробуйте ещё раз по инструкции.',
		]
	];
}


$UKB = new UniKffBot;