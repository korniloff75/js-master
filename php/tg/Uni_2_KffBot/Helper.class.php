<?php
class Helper extends CommonBot implements Game
{
	protected
		$data;

	protected function getCurData()
	{
		if(!file_exists(static::FOLDER))
		{
			$this->log->add('folder '.static::FOLDER.' was created!');
			mkdir(static::FOLDER, 0755);
		}

		//* get data
		$this->data = file_exists(static::BASE)
			? json_decode(
				file_get_contents(static::BASE), 1
			)
			: [];

		$this->data['change'] = 0;
	}

	protected function saveCurData()
	{
		// $this->log->add('$this->BTNS=',null,[$this->BTNS]);
		if(!$this->data['change']) return;
		unset($this->data['change']);

		// $this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		if(!file_put_contents(
			static::BASE,
			json_encode($this->data, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES), LOCK_EX
		)) $this->send(['text' => "Сервер в данный момент перегружен и Ваши данные не были сохранены. Попробуйте повторить."]);

			return $this;
	} //* saveCurData

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
				$o = [
					'text' => self::INFO['about'],
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::CMD['Gismeteo']['Gismeteo']],
								['text' => self::CMD['Draws']['general']],
							],
				],],];
				break;

			case 'help':
				$o = [
					'text' => self::INFO['help'],
					'reply_markup' => [
						"inline_keyboard" => [
							[
								['text' => 'Support', 'url' => 'https://t.me/korniloff75'],
								['text' => 'Development', 'url' => 'https://t.me/korniloff75'],
							],
							[
								['text' => '💬Community', 'url' => 'https://t.me/joinchat/KCwRpFHSzSKtAaymBBRbgg'],
							],
						],
				],];
				break;

			default:
			$o=null;
		}
		return $o;
	}


	protected function showUsername(array &$user)
	{
		$arr= $user['from'] ?? $user;
		return "<b>" . ($arr['realName'] ?? $arr['first_name']) . "</b> {$arr['username']} ({$arr['id']})\n";
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

		/* $arr = [
			//* Чат или бот?
			'text' => is_numeric(substr($this->chat_id,0,1))
			? 'Вы находитесь в частном боте группы Добрые люди.'
			: "Бот запущен из группы, где не имеет возможности интерактивного диалога с вами.\n\nДля использования всех возможностей бота - пеерейдите по ссылке @Uni_2_KffBot",
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		]; */

		//* Бот или чат?
		if(!$this->is_group)
		{
			$txt= 'Вы находитесь в боте чата Бюро Добрых Услуг.';
		}
		else
		{
			$txt= "Бот запущен из группы.";
		}

		$arr= [
			'text'=>$txt,
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		];

		return array_merge_recursive($arr,$o);
	} //* showMainMenu


	//* Отправляем
	public function send(array $o)
	{
		if(empty($o)) return;

		//* Подготовка и отправка

		// $this->fixBtns4Chat($o);

		$this->log->add(__METHOD__.' $o',null,[$o]);

		//* add keyboard options
		if(!empty($o['reply_markup']['keyboard']))
		{
			$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
		}

		//* Склеиваем текст
		if(is_array($o['text']))
		{
			$o['text'] = implode("\n\n", $o['text']);
		}

		//* Send
		$method= !empty($message_id= $this->cbn['message']['message_id'])
			? 'editMessageText'
			: 'sendMessage';

		if($method)
		{
			$o['message_id'] = $message_id;
		}

		if(!empty($this->toAllParticipants))
		{
			$this->toAllParticipants = null;
			foreach($draws['participants'] as $p)
			{
				$o['chat_id'] = $p['id'];
				$this->apiRequest($o);
			}

			unset($draws, $this->data['current draws']);
			$this->data['change']++;
		}
		else
		{
			$o['chat_id'] = $this->user_id;
			$this->apiRequest($o,$method);
		}

		//* Отправляем админу
		if(!empty($this->sendToOwner) && !$this->statement['BDU_admin'])
		{
			$this->sendToOwner = null;
			$o['chat_id'] = $draws['owner']['id'];
			$this->apiRequest($o);
		}
	}

	public function __destruct()
	{
		$this->saveCurData();
	}
} //* Helper
