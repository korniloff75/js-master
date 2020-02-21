<?php

require_once __DIR__."/../CommonBot.class.php";

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
		parent::__construct()
			->checkLicense()
			->getStatement()
			->Router();

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


	protected function getStatement()
	{
		$folder = __DIR__.'/statement';
		if(!file_exists($folder))
		{
			$this->log->add('folder '.$folder.' was created!');
			mkdir($folder, 0755);
		}

		//* get data
		$file= "$folder/{$this->chat_id}_base.json";
		$this->statement = file_exists($file)
			? json_decode(
				file_get_contents($file), 1
			)
			: [];

		$this->statement = array_merge($this->statement, ['file'=>$file,'change'=>0]);

		return $this;
	}

	protected function setStatement(array $state)
	{
		$state['change']= 1;
		return $this->statement = array_merge($this->statement, $state);
	}

	protected function saveStatement()
	{
		if(!$this->statement['change']) return;

		// $this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		file_put_contents(
			$this->statement['file'],
			json_encode($this->statement, JSON_UNESCAPED_UNICODE), LOCK_EX
		);

			return $this;
	} //* saveStatement


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
		/* if(in_array($cmdName, self::BTNS))
		{
			$btns_val = array_flip(self::BTNS);

			list($cmdName, $cmd) = [
				'GameTest', !is_numeric($btns_val[$cmdName])
				? $btns_val[$cmdName]
				: "{$cmdName}__{$cmd}"
			];
		} */

		//* exp
		if(
			!array_key_exists($cmdName, self::CMD)
		)
		{
			$cmd= $cmdName;
			$cmdName= $this->statement['cmdName'];
		}
		else
		{
			$this->setStatement(['cmdName'=>$cmdName]);
		}

		//* Aliases
			// if($cmdName === 'Draws') $cmdName = 'GameTest';
			if($cmdName === 'drs') $cmdName = 'Draws';
			if($cmdName === 'Pump') $cmdName = 'PumpMarket';

		if(
			!empty($curBtn = self::CMD[$cmdName])
		)
		{
			$this->BTNS = array_merge(self::BTNS, self::CMD[$cmdName]);
			$btns_val = array_flip($this->BTNS);

			$cmd = !is_numeric($btns_val[$cmd])
			? $btns_val[$cmd]
			: "{$cmdName}__{$cmd}";
		}

		$this->log->add(__METHOD__.' $this->statement',null,[$this->statement,$cmdName,$cmd]);


		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);

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

	public function __destruct()
	{
		$this->saveStatement();
	}

} //* UniKffBot


interface Game {
	//* Command list
	const
		CMD = [
			'Draws'=>[
				'new draw'=>'Создать розыгрыш',
				'play draw'=>'Разыграть',
				'show participants'=>'Участники',
				'participate'=>'Участвовать',
			],
			'PumpMarket'=>[
				'market'=>'Биржа 泵 насосов',
				'sale blue'=>'🔷泵🔷',
				'sale all'=>'🔷泵🔶',
				'sale gold'=>'🔶泵🔶',
				'replacePumps',
				'parsePumps',
				'sale',
				'unsale',
			]
		],
		BTNS = [
		'general'=>'⬅️Главная',
		'balance'=>'💰Баланс',
		'info'=>'💡Информация',
		'help'=>'❓Помощь',
		'settings'=>'⚙️Настройки',
		'community'=>'💬Community',
		'advanced'=>'Дополнительно',
		'market'=>'Биржа 泵 насосов',

		//* draws
		/* 'new draw'=>'Создать розыгрыш',
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
		'unsale', */
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
			'drs/general'=>'⬅️Главная',
			'drs/balance'=>'💰Баланс',
			'drs/info'=>'💡Информация',
			'drs/help'=>'❓Помощь',
			'drs/settings'=>'⚙️Настройки',
			'drs/community'=>'💬Community',
			'drs/advanced'=>'Дополнительно',

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


$UKB = new UniKffBot;