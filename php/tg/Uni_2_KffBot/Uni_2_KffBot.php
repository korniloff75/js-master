<?php

require_once __DIR__."/../CommonBot.class.php";
require_once __DIR__."/UniConstruct.trait.php";
require_once __DIR__."/Helper.class.php";

class UniKffBot extends CommonBot implements Game
{
	const OPTS_SEPARATOR = '__';

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

		// tolog(__METHOD__,null,['$this->inputData'=>$this->inputData]);

		//* Определяем точку запуска
		$this->is_group = !is_numeric(substr($this->chat_id,0,1));

		// *FIX hidden admin
		if($this->user_id === 1087968824)
			$this->user_id= self::OWNER;

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
			tolog('folder '.$folder.' was created!');
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

		// tolog('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		file_put_contents(
			$file,
			json_encode($this->statement, JSON_UNESCAPED_UNICODE), LOCK_EX
		);

		return $this;
	} //* saveStatement


	private function Router()
	{
		$inputData = $this->cbn['data'] ?? $this->message["text"];

		if(!empty($this->message))
			$this->setStatement([
				'last'=> $this->message
			]);

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
			tolog(__METHOD__.' findCommand',null,['$res'=>$res]);

			$cmdName = $res['cmdName'];
			$cmd = $res['cmd'];
		}
		else
		{
			tolog(__METHOD__.' findCommand FAIL',E_USER_WARNING,['$res'=>$res]);
		}

		if(empty($this->statement))
			$this->getStatement();

		tolog(__METHOD__.' $this->statement_1',null,['statement'=>$this->statement, '$cmdName'=>$cmdName, '$cmd'=>$cmd]);


		if(!empty($cmdName))
		{
			$cmdName = ucfirst($cmdName);

			if(file_exists($ext = __DIR__."/extensions/$cmdName.php"))
			{
				require_once $ext;
				tolog(__METHOD__ . " founded extension $ext",null,[]);
				new $cmdName($this, $cmd);
			}
			else switch ($cmdName)
			{
				case 'All':
					$this->sendToAll($cmd);
					break;

				default:
					tolog(__METHOD__ . " Undefined command [$cmdName]", E_USER_WARNING);
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

		tolog(__METHOD__ . ' inputData:', null, ['$inputArr'=>$inputArr, '$cmdName'=>$cmdName, '$cmd'=>$cmd]);

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
				'cmd'=> array_values(array_filter(explode(self::OPTS_SEPARATOR, $cmd)))
			];
		}
		// else $cmd= [$cmdName];
		else $cmd= array_values(array_filter(explode(self::OPTS_SEPARATOR, $cmdName)));

		tolog(__METHOD__ . ' NEW', null, ['$cmd'=>$cmd]);

		foreach(self::CMD as $cmdName=>&$commands)
		{
			$is_btn= in_array($cmd[0], $commands);

			if($is_btn || array_key_exists($cmd[0], $commands))
			{
				$this->setStatement([
					'cmdName'=>$cmdName,
					//* Отменяем ожидание вводимых данных
					'wait data'=>0,
				]);

				tolog(__METHOD__.' $this->statement_2',null,[$this->statement]);

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

		tolog(__METHOD__.' не найдено в self::CMD',null,['$cmdName'=>$cmdName, '$cmd'=>$cmd]);

		//* Внутренняя команда
		return [
			'cmdName'=> $this->getStatement()->statement['cmdName'],
			'cmd'=>$cmd
		];
	}


	public function __destruct()
	{
		tolog(__METHOD__.' $this->statement_3',null,[$this->statement]);
		$this->saveStatement();

		parent::__destruct();
	}

} //* UniKffBot


interface Game {
	//* Command list
	const CHATS = [-1001200025834, -1001251056203,
		// *Новости Крыма - https://t.me/crimeanNewsComments
		-1001305018802,
	],
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
			'familiar'=>'⌂ЛК',
			'fio'=>'Имя',
			'category'=>'Категория',
			'hashtags'=>'Стек',
			'region'=>'Регион',
			//*
			'scope'=>'🔎Поиск',
			'users'=>'👥Пользователи',
			'list_categories'=>'🖹Категории',
			// 'list_categories'=>"&#128441;Категории",
			'add_category'=>'⨁Добавить',
			// 'add_category'=>'➕Добавить',
			'remove_self'=>'❌Удалить данные',
		],

		'Converter'=> [
			'converter'
		],
		'Reviews'=> [
			'Reviews'
		],
		'Admin'=> [
			'adm'
		],
	],

	BTNS = [
		'general'=>'⬅️Главная',
		'balance'=>'💰Баланс',
		'info'=>'💡Информация',
	],

	CATEGORIES = ['Медицина','Образование','IT','SEO','PR','Строительство','Торговля','Финансы','Искусство','Общепит','Другое'],

	INFO = [
		// 'about'=>"Бот имеет расширенный функционал.\n<b>Доступные команды боту:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.
		// /draws - Создание розыгрышей для всех желающих.",
		'about'=>"<b>Доступные команды боту:</b>\n/gismeteo - Показ текущей погоды по вашей геолокации с возможностью посмотреть прогноз на ближайшие дни.",
		'balance'=>'У нас - коммунизм, товагисчи!!! Какие деньги?',
		'settings'=>'Какие нужны индивидуальные настройки? Пишите @js_master_bot',
		/* 'advanced'=> [
			'text' => self::INFO['about'],
			'reply_markup' => ["keyboard" => [
				[
					['text' => self::CMD['Gismeteo']['Gismeteo']],
					['text' => self::CMD['Draws']['general']],
				],
			],],
		], */
		// 'help'=>"При возникновении трудностей с использованием бота обратитесь по одной из указанных ссылок.",
		'help'=> [
			'text' => "При возникновении трудностей с использованием бота обратитесь по одной из указанных ссылок.",
			'reply_markup' => ["inline_keyboard" => [
				[
					// ['text' => 'Support', 'url' => 'https://t.me/js_master_bot'],
					['text' => 'Development', 'url' => 'https://t.me/js_master_bot'],
					['text' => '💬Community', 'url' => 'https://t.me/joinchat/KCwRpEeG8OqYEb1bUKU6RA'],
				],
			],],
		],

	];
}


interface DrawsInt {
}


$UKB = new UniKffBot;