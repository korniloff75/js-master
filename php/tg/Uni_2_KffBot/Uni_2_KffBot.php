<?php

require_once __DIR__."/../CommonBot.class.php";

class UniKffBot extends CommonBot implements Game,PumpInt,DrawsInt
{
	public
		$webHook=0;
	protected
		# Test mode, bool
		$__test = 1,
		//* 4 Local
		$cron = [
			'chat'=> ['id' => 673976740],
			'from'=> ['id' => 673976740],
			'text'=> '⛅Погода'
		];


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		//* Запускаем скрипт
		parent::__construct()
			->checkLicense()
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
		if(!empty($this->statement))
			return $this;

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

	public function setStatement(array $state)
	{
		$state['change']= 1;
		return $this->statement = array_merge($this->getStatement()->statement, $state);
	}

	protected function saveStatement()
	{
		if(!$this->statement['change']) return;
		$file= $this->statement['file'];
		unset(
			$this->statement['change'],
			$this->statement['file']
		);

		// $this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		file_put_contents(
			$file,
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
		$inputArr= array_values(array_filter(explode('/', $inputData,2)));




		//* exp
		//* Aliases
		if(is_array($res= self::findCommand($inputArr, $this->message)))
		{
			$this->log->add(__METHOD__.' findCommand',null,[$res]);

			$cmdName = $res['cmdKey'];
			$cmd = $res['cmd'];
		}
		else
		{
			$this->log->add(__METHOD__.' findCommand FAIL',E_USER_WARNING,[$res]);
		}

		if(
			!array_key_exists($cmdName, self::CMD)
		)
		{
			$this->log->add(__METHOD__.' Нет в self::CMD',null,[array_key_exists($cmdName, self::CMD),$cmdName,$cmd,self::CMD]);

			$cmd= $cmdName;
			$cmdName= $this->getStatement()->statement['cmdName'];
		}

		if(
			!empty($curBtn = @self::CMD[$cmdName])
		)
		{
			$this->BTNS = array_merge(self::BTNS, $curBtn);
			$btns_val = array_flip($this->BTNS);

			if(!empty($btns_val[$cmd]))
			{
				$cmd = !is_numeric($btns_val[$cmd])
				? $btns_val[$cmd]
				: "{$cmdName}__{$cmd}";
			}
		}
		else $this->log->add(__METHOD__.' $curBtn FAIL',E_USER_WARNING,[$curBtn]);

		$this->log->add(__METHOD__.' $this->statement_1',null,[$this->statement,$cmdName,$cmd,$this->BTNS]);

		$this->setStatement([
			'cmdName'=>$cmdName,
			'change'=> !empty($this->statement)
				&& $this->statement['cmdName'] !== $cmdName
		]);

		$this->log->add(__METHOD__.' $this->statement_2',null,[$this->statement]);



		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);

			switch ($cmdName)
			{
				case 'Gismeteo':
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


	public static function defineCurCmd($inputCmd, array $commands)
	{
		if(!in_array($inputCmd, $commands))
			return null;
		$flip= array_flip($commands);
		$cmd = !is_numeric($flip[$inputCmd])
		? $flip[$inputCmd]
		: $inputCmd;
		return [
			'cmdName'=>$inputCmd,
			'cmd'=>$cmd
		];
	}

	public static function findCommand($inputArr, $message)
	:?array
	{
		list($cmdName, $cmd) = $inputArr;

		// trigger_error(__METHOD__ . ' inputData: $cmdName, $cmd = ', null, [$cmdName, $cmd]);

		//* Приходит локация
		if(!empty($message['location']))
			return [
				'cmdKey'=>'gismeteo',
				'cmd'=>'setLocation'
			];

		$cmd= $cmd ?? $cmdName;

		/* if(array_key_exists($cmdName, self::CMD))
		{
			return self::defineCurCmd($cmd, self::CMD);
		} */

		foreach(self::CMD as $cmdName=>&$commands)
		{
			// $flip= array_flip($commands);
			if(in_array($cmd, $commands))
			{
				return array_merge([
					'cmdKey'=>$cmdName
				], self::defineCurCmd($cmd, $commands));
				break;
			}
		}
		return null;
	}

	public function __destruct()
	{
		$this->log->add(__METHOD__.' $this->statement_3',null,[$this->statement]);
		$this->saveStatement();
	}

} //* UniKffBot


interface Game {
	//* Command list
	const
		CMD = [
			'Draws'=>[
				'general'=>'⬅️Главная',
				'start',
				'drs',
				'info'=>'💡Информация',
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
			],
			'Gismeteo'=>[
				'Gismeteo'=>'⛅Погода',
			],
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
		'Gismeteo'=>'⛅Погода',

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
	],

	INFO = [
		'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.\n/draws - Группа с розыгрышами, где любой участник может создавать розыгрыши, а также участвовать в существующих.",
		'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
		'settings'=>'Какие нужны индивидуальные настройки? Пишите @korniloff75',
		'advanced'=>'',
		'help'=>"При возникновении трудностей с использованием бота обратитесь по одной из указанных ссылок.",

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
}


$UKB = new UniKffBot;