<?php
class Helper extends CommonBot implements Game,DrawsInt,PumpInt
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
		// $this->log->add('static::BTNS=',null,[static::BTNS]);
		if(!$this->data['change']) return;
		unset($this->data['change']);

		// $this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		file_put_contents(
			static::BASE,
			json_encode($this->data, JSON_UNESCAPED_UNICODE), LOCK_EX
		);

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
								['text' => self::BTNS['advanced']],
								['text' => self::BTNS['help']],
								['text' => self::BTNS['settings']],
							],
							[
								['text' => static::PUMP_BTNS['pump/market']],
								['text' => self::BTNS['general']],
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
				];
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
								['text' => '💬Community', 'url' => 'https://t.me/korniloff75'],
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
		return "<b>{$user['first_name']}</b> @{$user['username']} ({$user['id']})\n";
	}

	protected function showMainMenu($o=[])
	{
		$keyboard = [
			[
				['text' => static::DRS_BTNS['drs/new draw']],
			],
			[
				['text' => static::BTNS['balance']],
				['text' => static::BTNS['info']],
			],
		];

		if(isset($this->data['current draws']))
		{
			$keyboard[0][0] = ['text' => static::DRS_BTNS['drs/participate']];
		}

		$arr = [
			//* Чат или бот?
			'text' => is_numeric(substr($this->chat_id,0,1))
			? 'Вы находитесь в боте для розыгрышей. Это главное меню. Оно будет появляться при старте бота, а также во всех недоработанных функциях по умолчанию.'
			: "Бот запущен из группы, где не имеет возможности интерактивного диалога с вами.\n\nДля продолжения использования - пеерейдите в бот по ссылке @UniKffBot",
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		];
		return array_merge_recursive($arr,$o);
	} //* showMainMenu
} //* Helper
