<?php

require_once __DIR__."/../CommonBot.class.php";
require_once __DIR__."/UniConstruct.trait.php";
require_once __DIR__."/Helper.class.php";


class UniKffBot extends CommonBot implements Game,PumpInt,DrawsInt
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
		{
			$btns_val = array_flip(self::BTNS);

			list($cmdName, $cmd) = [
				'GameTest', !is_numeric($btns_val[$cmdName])
				? $btns_val[$cmdName]
				: "{$cmdName}__{$cmd}"
			];
		}

		//* exp
		/* if(
			$curBtn = constant("self::".strtoupper($cmdName)."_BTNS")
			&& in_array($cmdName, $curBtn)
		)
		{
			$btns_val = array_flip($curBtn);

			list($cmdName, $cmd) = [
				'Pump', !is_numeric($btns_val[$cmdName])
				? $cmd
				: "{$cmdName}__{$cmd}"
			];
		} */


		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);
			//* Aliases
			if($cmdName === 'Draws') $cmdName = 'GameTest';
			if($cmdName === 'Drs') $cmdName = 'Draws';
			if($cmdName === 'Pump') $cmdName = 'PumpMarket';

			switch ($cmdName)
			{
				case 'Gismeteo':
				case 'Youtube':
				case 'Zen':
				case 'GameTest':
				case 'Draws':
				case 'PumpMarket':
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
		'advanced'=>'Дополнительно',
		//* draws
		'new draw'=>'Создать розыгрыш',
		'play draw'=>'Разыграть',
		'show participants'=>'Участники',
		'participate'=>'Участвовать',
		//*
		'pump market'=>'Биржа 泵 насосов',
		'sale blue pump'=>'🔷泵🔷',
		'sale all'=>'🔷泵🔶',
		'sale gold pump'=>'🔶泵🔶',
		'replacePumps',
		'parsePumps',
		'sale',
		'unsale',
	];
}

interface PumpInt {
	const
		PUMP_BTNS= [
			'pump/market'=>'Биржа 泵 насосов',
			'pump/sale blue'=>'🔷泵🔷',
			'pump/sale all'=>'🔷泵🔶',
			'pump/sale gold'=>'🔶泵🔶',
			'pump/replacePumps',
			'pump/parsePumps',
			'pump/sale',
			'pump/unsale',
		];
}

interface DrawsInt {
	const
		DRS_BTNS= [
			'drs/new draw'=>'Создать розыгрыш',
			'drs/play draw'=>'Разыграть',
			'drs/show participants'=>'Участники',
			'drs/participate'=>'Участвовать',
		],
		INFO = [
			'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.\n/draws - Группа с розыгрышами, где любой участник может создавать розыгрыши, а также участвовать в существующих.",
			'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
			'settings'=>'Какие нужны индивидуальные настройки? Пишите @korniloff75',
			'advanced'=>'',
			'help'=>"Поможем всем!\nТут будут ссылки на поддержку. Скорее всего, инлайн-кнопками.",

			'pump market'=>"Приветствую в клановой бирже насосов!\nЗдесь можно размещать информацию по своим насосам, которые планируются на продажу в общей бирже.",
			'pumpName'=> [
				'blue'=> 'Синие🔷насосы',
				'gold'=> 'Рыжие🔶генераторы'
			],
			'sale'=> [
				'fail'=> 'Введены неверные данные. Попробуйте ещё раз по инструкции.',
				'blue'=>"Для добавления насоса в список введите команду в формате sale/blue__DATE__NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, DATE - дата планируемой поломки, NUMBER - номер насоса.\n<u>Например:</u>\nsale/blue__2020-09-02__5380",
				'blue pump'=>"Для добавления насоса в список введите команду в формате sale/blue__DATE__NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, DATE - дата планируемой поломки, NUMBER - номер насоса.\n<u>Например:</u>\nsale/blue__2020-09-02__5380",
				'gold'=>"Для добавления насоса в список введите команду в формате sale/blue__DATE__NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, DATE - дата планируемой поломки, NUMBER - номер насоса.\n<u>Например:</u>\nsale/gold__2020-09-02__5380",
				'gold pump'=>"Для добавления насоса в список введите команду в формате sale/blue__DATE__NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, DATE - дата планируемой поломки, NUMBER - номер насоса.\n<u>Например:</u>\nsale/gold__2020-09-02__5380",
				'all'=> "Для самых ленивых, таких как я. Пакетное добавление насосов.\n\nСуществуют возможности как простого добавления, так и добавления <u>с заменой</u> (с предварительным полным удалением старой базы одноименных насосов):\n\n<b>parsePumps/[...]</b> - Добавление\n<b>replacePumps/[...]</b> - Замена\n<b>[...]</b> - Скопированный список насосов или генераторов из бота Кэпа.",
		],
		'unsale'=> "Для удаления любого из насосов из списка введите команду в формате unsale/NUMBER[__NUMBER2...]\nГде __ - 2 нижних дефиса, NUMBER - номер насоса.\n<u>Например:</u>\nunsale/5380__6390__2121",

	];
}

interface DrawsInt_B extends DrawsInt
{

}


$UKB = new UniKffBot;