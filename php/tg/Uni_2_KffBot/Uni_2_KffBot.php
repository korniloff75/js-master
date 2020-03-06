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
			->init()
			//* Добавляем в лицензию
			->checkLicense(null, [
				'condition'=> $this->is_group && in_array($this->chat_id, self::CHATS)
			])
			->Router();

	} //__construct

	/**
	 *
	 */
	private function init()
	{
		//* Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		//* Определяем точку запуска
		$this->is_group = !is_numeric(substr($this->chat_id,0,1));

		//* Защищаем от чужих чатов
		$allowedGrop= !$this->is_group || in_array($this->chat_id, self::CHATS);

		if(!$allowedGrop)
		{
			$this->apiResponseJSON([
				'chat_id'=>$this->chat_id,
				'parse_mode' => 'html',
				'text'=>"Ошибка\n<pre>{$this->user_id}\n{$this->chat_id}\n{$this->is_group}</pre>",
			]);
			die;
		}

		return $this;
	} //* init


	public function getStatement()
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
		$file= "$folder/{$this->user_id}_base.json";
		$this->statement = file_exists($file)
			? json_decode(
				file_get_contents($file), 1
			)
			: [];

		$this->statement = array_merge(['file'=>$file,'change'=>0], $this->statement);

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

		/* if(!empty($this->message))
			$this->setStatement([
				'last'=> $this->message
			]); */

		//* FIX multibots
		$inputData= explode('@', $inputData)[0];
		//* Define command
		$inputArr= array_values(array_filter(explode('/', $inputData,3)));


		//* Aliases
		// if(is_array($res= self::findCommand($inputArr, $this->message)))
		if(
			is_array($res= $this->findCommand($inputArr, $this->message))
		)
		{
			$this->log->add(__METHOD__.' findCommand',null,[$res]);

			$cmdName = $res['cmdName'];
			$cmd = $res['cmd'];
		}
		else
		{
			$this->log->add(__METHOD__.' findCommand FAIL',E_USER_WARNING,[$res]);
		}

		if(empty($this->statement))
			$this->getStatement();

		$this->log->add(__METHOD__.' $this->statement_1',null,[$this->statement,$cmdName,$cmd]);


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
		if(!empty($message['location']) && empty($message['venue']))
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
			$is_btn= in_array($cmd[0], $commands);

			if($is_btn || array_key_exists($cmd[0], $commands))
			{
				$this->setStatement([
					'cmdName'=>$cmdName,
					//* Отменяем ожидание вводимых данных
					'wait familiar data'=>0,
				]);

				$this->log->add(__METHOD__.' $this->statement_2',null,[$this->statement]);

				// $this->BTNS = $commands;
				$this->BTNS = array_merge(self::BTNS, $commands);

				return $is_btn
				? array_replace_recursive([
					'cmdName'=>$cmdName,
					'cmd'=>$cmd,
				], self::defineCurCmd($cmd[0], $commands))
				: [
					'cmdName'=>$cmdName,
					'cmd'=>$cmd,
				];
				break;
			}
		}

		$this->log->add(__METHOD__.' не найдено в self::CMD  $cmdName, $cmd',null,[$cmdName, $cmd]);

		//* Внутренняя команда
		return [
			'cmdName'=> $this->getStatement()->statement['cmdName'],
			'cmd'=>$cmd
		];
	}


	public function __destruct()
	{
		$this->log->add(__METHOD__.' $this->statement_3',null,[$this->statement]);
		$this->saveStatement();

		parent::__destruct();
	}

} //* UniKffBot


interface Game {
	//* Command list
	const CHATS = [-1001200025834],
	CMD = [
		'Draws'=>[
			'general'=>'⬅️Главная',
			'start',
			'drs', 'draws',
			'info'=>'💡Информация',
			'advanced'=>'Дополнительно',
			'help'=>'❓Помощь',
			'settings'=>'⚙️Настройки',
			'community'=>'💬Community',
			'new draw'=>'Создать розыгрыш',
			'cancel draw'=>'❌Отменить розыгрыш',
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
			'fio'=>'Имя',
			'category'=>'Категория',
			'hashtags'=>'Стек',
			'region'=>'Регион',
			//*
			'scope'=>'⚛Поиск',
			'users'=>'👥Пользователи',
			'list_categories'=>'🖹Категории',
			// 'list_categories'=>"&#128441;Категории",
			'add_category'=>'⨁Добавить',
			// 'add_category'=>'➕Добавить',
			'remove_category'=>'❌Удалить',
		],
	],

	BTNS = [
		'general'=>'⬅️Главная',
		'balance'=>'💰Баланс',
		'info'=>'💡Информация',
	],

	CATEGORIES = ['Медицина','Образование','IT','Строительство','Торговля','Финансы','Искусство','Общепит','Другое'],

	INFO = [
		'about'=>"Бот имеет расширенный функционал.\n<b>Основные команды:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.
		/draws - Создание розыгрышей для всех желающих.",
		'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
		'settings'=>'Какие нужны индивидуальные настройки? Пишите @korniloff75',
		'advanced'=>'',
		'help'=>"При возникновении трудностей с использованием бота обратитесь по одной из указанных ссылок.",

	];
}

interface PumpInt {
}

interface DrawsInt {
}


$UKB = new UniKffBot;