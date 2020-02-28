<?php

require_once __DIR__."/../CommonBot.class.php";
require_once __DIR__."/UniConstruct.trait.php";
require_once __DIR__."/Helper.class.php";

class UniKffBot extends CommonBot implements Game
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
			// ->checkLicense()
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
		$inputArr= array_values(array_filter(explode('/', $inputData,3)));




		//* exp
		//* Aliases
		// if(is_array($res= self::findCommand($inputArr, $this->message)))

		if(is_array($res= $this->findCommand($inputArr, $this->message)))
		{
			$this->log->add(__METHOD__.' findCommand',null,[$res]);

			$cmdName = $res['cmdName'];
			$cmd = $res['cmd'];
		}
		else
		{
			$this->log->add(__METHOD__.' findCommand FAIL',E_USER_WARNING,[$res]);
		}

		$this->log->add(__METHOD__.' $this->statement_1',null,[$this->statement,$cmdName,$cmd,$this->BTNS]);


		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);

			switch ($cmdName)
			{
				case 'Gismeteo':
				case 'Draws':
				case 'BDU':
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
		$flipCmd = !is_numeric($flip[$inputCmd])
		? $flip[$inputCmd]
		: $inputCmd;
		return [
			'cmd'=>[$flipCmd],
		];
	}

	public function findCommand($inputArr, $message)
	:?array
	{
		list($cmdName, $cmd) = $inputArr;

		$this->log->add(__METHOD__ . ' inputData: $inputArr,$cmdName, $cmd = ', null, [$inputArr,$cmdName, $cmd]);

		//* Приходит локация
		if(!empty($message['location']))
			return [
				'cmdName'=>'gismeteo',
				'cmd'=>['setLocation']
			];

		//* Define cmd
		if(!empty($cmd))
		{
			return [
				'cmdName'=>$cmdName,
				'cmd'=> array_values(array_filter(explode('__', $cmd)))
			];
		}
		else $cmd= [$cmdName];

		$this->log->add(__METHOD__ . ' NEW $cmd = ', null, [$cmd]);

		foreach(self::CMD as $cmdName=>&$commands)
		{
			// $flip= array_flip($commands);
			if(in_array($cmd[0], $commands))
			{
				$this->setStatement([
					'cmdName'=>$cmdName,
					//* Отменяем ожидание вводимых данных
					'wait familiar data'=>0,
				]);

				$this->log->add(__METHOD__.' $this->statement_2',null,[$this->statement]);

				// $this->BTNS = $commands;
				$this->BTNS = array_merge(self::BTNS, $commands);

				return array_replace_recursive([
					'cmdName'=>$cmdName,
					'cmd'=>$cmd,
				], self::defineCurCmd($cmd[0], $commands));
				break;
			}
		}

		$this->log->add(__METHOD__.' не найдено в self::CMD  $cmdName, $cmd',null,[$cmdName, $cmd]);

		//* Если
		// if($cmdName= )
		return [
			'cmdName'=> $this->getStatement()->statement['cmdName'],
			'cmd'=>$cmd
		];
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
				'advanced'=>'Дополнительно',
				'help'=>'❓Помощь',
				'settings'=>'⚙️Настройки',
				'community'=>'💬Community',
				'new draw'=>'Создать розыгрыш',
				'play draw'=>'Разыграть',
				'show participants'=>'Участники',
				'participate'=>'Участвовать',
				'prizes_count',
			],

			'Gismeteo'=>[
				'Gismeteo'=>'⛅Погода',
				'gismeteo',
				'changeLocation',
				'forecast_aggregate',
			],

			'BDU'=>[
				'familiar'=>'☮ЛК',
				'fio'=>'Ваше имя',
				'hashtags'=>'Ваш стек',
				'region'=>'Ваш регион',
				//*
				'users'=>'👥Пользователи',
				'scope'=>'⚛Возможности',
			],
		],

		BTNS = [
			'general'=>'⬅️Главная',
			'balance'=>'💰Баланс',
			'info'=>'💡Информация',
	],

	INFO = [
		'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.
		/draws - Создание розыгрышей для всех желающих.",
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
}

interface DrawsInt {
}


$UKB = new UniKffBot;