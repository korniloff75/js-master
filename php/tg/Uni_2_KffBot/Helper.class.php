<?php
class Helper extends CommonBot implements Game
{
	protected
		$data;

	public function __construct()
	{
		// parent::__construct();
	}

	protected function getCurData()
	{
		if(empty(static::FOLDER))
		{
			$this->log->add('static::FOLDER is empty!', E_USER_WARNING);
		}
		elseif(!file_exists(static::FOLDER))
		{
			$this->log->add('folder '.static::FOLDER.' was created!');
			mkdir(static::FOLDER, 0755);
		}

		//* get data
		$this->objData = new DbJSON(static::BASE);
		$this->data = $this->objData->get();
		/* $this->data = file_exists(static::BASE)
			? json_decode(
				file_get_contents(static::BASE), 1
			)
			: []; */

		$this->data['change'] = 0;
		return $this;
	}

	/* protected function saveCurData()
	{
		// $this->log->add('$this->BTNS=',null,[$this->BTNS]);
		if(!$this->data['change']) return;
		unset($this->data['change']);

		// $this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		if(!file_put_contents(
			static::BASE,
			json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES), LOCK_EX
		)) $this->apiRequest(['text' => "Сервер в данный момент перегружен и Ваши данные не были сохранены. Попробуйте повторить."]);

			return $this;
	} //* saveCurData */

	protected function routerCmd($cmd=null)
	{
		switch ($cmd ?? $this->cmd[0]) {
			case 'info':
				$o = [
					'text' => self::INFO['about'],
					'reply_markup' => [
						"keyboard" => [
							[
								// ['text' => $this->BTNS['advanced']],
								['text' => self::CMD['Draws']['help']],
								['text' => self::CMD['Draws']['settings']],
							],
							[
								['text' => self::CMD['Draws']['general']],
							],
				],],];
				break;

			case 'balance':
			case 'settings':
				$o = [
					'text' => self::INFO[$this->cmd[0]],
				];
				break;

			case 'advanced':
				// $o = self::INFO['advanced'];
				$o = [
					'text' => self::INFO['about'],
					'reply_markup' => ["keyboard" => [
						[
							['text' => self::CMD['Gismeteo']['Gismeteo']],
							['text' => self::CMD['Draws']['general']],
						],
					],],
				];
				break;

			case 'help':
				$o = self::INFO['help'];
				break;

			default:
			$o=null;
		}
		return $o;
	}


	protected function showUsername(array &$user, $tag=null)
	{
		$arr= $user['from'] ?? $user;
		return "<b>"
		. ($arr['realName'] ?? $arr['first_name'])
		. ($arr['last_name'] ?? '')
		. "</b> " . ($tag?'@':'')
		. "{$arr['username']} ({$arr['id']})\n";
	}


	//* Приём и обработка ожидаемых данных
	protected function inputDataRouter($prefix='w')
	{
		//* Ждем данные
		if(empty($this->statement['wait data']))
			return $this;

		$dataName= $this->statement['dataName'];
		$this->log->add(__METHOD__.' $this->message,$dataName',null,[$this->message,$dataName]);

		$txt= trim($this->message['text']);

		if(method_exists($this, "{$prefix}_$dataName"))
		{
			$this->{"{$prefix}_$dataName"}(explode("\n",$txt));
		}
		else
			$this->log->add(__METHOD__." method {$prefix}_$dataName is FAIL",E_USER_WARNING);

		$this->UKB->setStatement([
			'wait data'=>0,
		]);
		die;
	}


	/**
	 ** В чате преобразуем клавиатуру в inline
	 */
	protected function fixBtns4Chat(array &$o)
	{
		if(
			!$this->is_group
			// || empty($o['reply_markup']['keyboard'])
		)
			return;

		$markup= &$o['reply_markup'];

		//* to inline
		if(!empty($markup['keyboard'])) foreach($markup['keyboard'] as &$row)
		{
			foreach($row as &$btn)
			{
				$btn['callback_data']= $btn['text'];
			}
		}

		//* Если нет кнопок - добавляем на Главную
		if(
			empty($iKeyboard= array_merge_recursive($markup['inline_keyboard']??[], $markup['keyboard']??[]))
		) $iKeyboard= [[[
			'text'=> self::BTNS['general'],
			'callback_data'=> self::BTNS['general'],
		]]];

		$o['reply_markup']= [
			"inline_keyboard" => $iKeyboard,
		];

			// unset($markup['keyboard']);

		$this->log->add(__METHOD__.' $markup',null,[$markup]);
	}


	protected function showMainMenu($o=[])
	{
		$keyboard = [
			[
				['text' => self::CMD['BDU']['familiar']],
				// ['text' => self::CMD['BDU']['users']],
				['text' => self::CMD['BDU']['scope']],
			],
			[
				['text' => self::CMD['Draws']['info']],
				['text' => self::CMD['Draws']['advanced']],
			],
		];

		//* Бот или чат?
		if(!$this->is_group)
		{
			$txt= 'Вы находитесь в боте чата <a href="https://t.me/joinchat/KCwRpEeG8OoZmye-5Cz55Q">Бюро Добрых Услуг</a>.';
		}
		else
		{
			$txt= "Бот запущен из группы.";
		}

		$o= array_merge_recursive([
			'text'=>$txt,
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		],$o);

		return $o;
	} //* showMainMenu


	//* Отправляем
	public function send(array $o)
	{
		if(empty($o)) return;

		//* Подготовка и отправка

		// $this->fixBtns4Chat($o);

		// $this->log->add(__METHOD__.' $o',null,[$o]);

		//* Send
		$o['chat_id'] = $o['chat_id'] ?? $this->user_id;

		$this->apiRequest($o);
	}


	public function __destruct()
	{
		if(!$this->data['change']) return;

		// $this->log->add(__METHOD__." \$this->data=",null,[$this->data]);

		$this->objData->replace($this->data);
		// $this->saveCurData();
	}
} //* Helper
