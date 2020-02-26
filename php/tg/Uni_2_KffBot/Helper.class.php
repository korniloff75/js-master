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
								['text' => $this->BTNS['help']],
								['text' => $this->BTNS['settings']],
							],
							[
								['text' => $this->BTNS['general']],
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
								['text' => $this->BTNS['Gismeteo']],
								['text' => $this->BTNS['general']],
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
								['text' => 'Поддержка разработчика', 'url' => 'https://t.me/korniloff75'],
							],
							[
								['text' => '💬Community', 'url' => 'https://t.me/joinchat/KCwRpE0yLGm6qDagOe6gYg'],
							],
						],
				],];
				break;

			default:
			$o=null;
		}
		return $o;
	}


	protected function showUsername($user)
	{
		return "<b>{$user['realName']}</b> @{$user['from']['username']} ({$user['id']})\n";
	}

	protected function showMainMenu($o=[])
	{
		$keyboard = [
			[
				['text' => self::CMD['BDU']['familiar']],
				['text' => self::CMD['BDU']['users']],
			],
			[
				['text' => self::BTNS['info']],
				['text' => self::CMD['Draws']['advanced']],
			],
		];

		$arr = [
			//* Чат или бот?
			'text' => is_numeric(substr($this->chat_id,0,1))
			? 'Вы находитесь в частном боте группы Добрые люди.'
			: "Бот запущен из группы, где не имеет возможности интерактивного диалога с вами.\n\nДля продолжения использования - пеерейдите в бот по ссылке @Uni_2_KffBot",
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		];
		return array_merge_recursive($arr,$o);
	} //* showMainMenu


	//* Отправляем
	public function send(array $o)
	{
		$this->log->add(__METHOD__.' $o',null,[$o]);

		if(empty($o)) return;

		//* Подготовка и отправка

		//* add keyboard options
		if(!empty($o['reply_markup']['keyboard']))
		{
			$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
		}

		//* Склеиваем текст перед отправкой
		if(is_array($o['text']))
		{
			$o['text'] = implode("\n\n", $o['text']);
		}

		//* Send
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
		else $this->apiRequest($o);

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
