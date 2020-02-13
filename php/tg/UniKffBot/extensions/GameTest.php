<?php
require_once "UniConstruct.trait.php";

class GameTest extends CommonBot {
	use UniConstruct;

	const
		FOLDER = 'Game',
		BASE = self::FOLDER . '/base.json',
		//* Command list
		GAME = UniKffBot::GAME;

	protected
		$data;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveData();


	} //* __construct


	private function init()
	{
		// $this->log->add('self::GAME=',null,[self::GAME]);

		if(!file_exists(self::FOLDER))
			mkdir(self::FOLDER, 0755);

		//* set data
		$this->data = file_exists(self::BASE)?
			json_decode(
				file_get_contents(self::BASE), 1
			):
			[];
		$this->data['change'] = 0;

			return $this;
	} //* init


	private function saveData()
	{
		// $this->log->add('self::GAME=',null,[self::GAME]);
		if(!$this->data['change']) return;
		unset($this->data['change']);

		file_put_contents(
			self::BASE,
			json_encode($this->data, JSON_UNESCAPED_UNICODE), LOCK_EX
		);

			return $this;
	} //* saveData


	private function routerCmd()
	{
		$o=null;
		$data = &$this->data['current draws'];

		switch ($this->cmd[0]) {
			case 'info':
				$o = [
					'text' => 'Тут будет ну оооочень нужная информация... Можете помочь её составить.',
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::GAME['advanced']],
								['text' => self::GAME['help']],
								['text' => self::GAME['settings']],
							],
							[
								['text' => self::GAME['general']],
							],
				],],];
				break;

			case 'balance':
				$o = [
					'text' => 'У нас - коммунизм, товагисчи!!! Какие деньги?',
				];
				break;

			case 'settings':
				$o = [
					'text' => 'Какие нужны индивидуальные настройки? Пишите @korniloff75',
				];
				break;

			case 'advanced':
				$o = [
					'text' => 'Что тут можно разместить? Нужен ли этот раздел? Пишите @korniloff75',
				];
				break;

			case 'help':
				$o = [
					'text' => "Поможем всем!\nТут будут ссылки на поддержку. Скорее всего, инлайн-кнопками.",
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

			//* Новый розыгрыш
			case 'new draw':
				if(!empty($data))
				{
					$o = $this->showMainMenu([
						'text' => 'Вы не можете создать розыгрыш, пока не разыгран предыдущий. Но вы можете участвовать в существующем!'
					]);
				break;
				}

				$o = [
					'text' => 'Введите количество призов в розыгрыше.',
					'reply_markup' => [
						"inline_keyboard" => [
							[
								['text' => 1, 'callback_data' => '/GameTest/prizes_count__1'],
								['text' => 2, 'callback_data' => '/GameTest/prizes_count__2'],
								['text' => 3, 'callback_data' => '/GameTest/prizes_count__3'],
							],
				],],];
				break;

			case 'prizes_count':
				if(
					isset($data['owner'])
					&& $this->chat_id !== $data['owner']['id']
				)
				{
					$this->showMainMenu([
						'text'=> 'Менять количество призов может только спонсор розыгрыша!',
					]);
					break;
				}

				$this->data['change']++;
				$data = [
					'owner' => $this->cbn['from'],
					'prizes_count' => $this->cmd[1][0],
				];

				$o = [
					'text' => 'Розыгрыш создан.',
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::GAME['general']],
							],
				],],];
				$this->addSelf = 1;
				break;

			case 'show participants':
				$o = $this->showParticipants();
				$o['text'] .= "\n<a href='{$this->urlDIR}/assets/Zorro_300.png' title='ZorroClan'>&#8205;</a>";
				break;

			//* Розыгрыш
			case 'play draw':
				if(!count($data['participants']))
				{
					$o = $this->showMainMenu([
						'text' => "Кого разыгрывать собираемся, товагисч {$data['owner']['first_name']}?\n\nРегистрируйте новый розыгрыш!",
					]);
					break;
				}

				shuffle($data['participants']);
				$winners = []; $winStr = '';

				for($i=0; $i < $data['prizes_count']; $i++)
				{
					if(!empty($data['participants'][$i]))
						break;

					$winners[] = $data['participants'][$i];
					$winStr .= $this->showUsername($winners[$i]);
				}

				$o = [
					'text' => "<u>Победители:</u>\n\n$winStr\n<a href='{$this->urlDIR}/assets/Zorro_300.png' title='ZorroClan'>&#8205;</a>",
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::GAME['general']],
							],
				],],];

				$o = array_merge_recursive($this->showParticipants(), $o);

				// unset($data);
				unset($data, $this->data['current draws']);
				$this->data['change']++;
				break;

			//* Участвовать
			case 'participate':
				if(in_array($this->cbn['from'], $data['participants']))
				{
					$o = $this->showMainMenu([
						'text' => "Ты уже в регистрации на розыгрыш. Кончай сервер мучать, а то - забаню нах!\n\n"
					]);
					break;
				}
				elseif(
					!empty($owner = $data['owner'])
				) {
					$this->data['change']++;
					$data['participants'][]= $this->cbn['from'];
					$count = count($data['participants']);

					$o = [
						'text' => "Участник " . $this->showUsername($this->cbn['from']) . " зарегистрировался в розыгрыше от {$owner['first_name']}.\nНа данный момент зарегистрировано {$count} чел."
					];
					$this->sendToOwner = 1;
				}
				else $o = $this->showMainMenu([
					'text' => 'Для вас нет доступных розыгрышей в настоящий момент.',
				]);
				break;

			default:
				//todo Оповещать о созданном розыгрыше!
				$o = $this->showMainMenu();
				break;
		} //*switch


		//* Подготовка и отправка
		if($o)
		{
			//* Кнопки для организатора
			if(
				isset($data['owner'])
				&& $this->chat_id === $data['owner']['id']
				&& empty($o['reply_markup']['inline_keyboard'])
			)
			{
				$o['reply_markup']['keyboard'] = array_merge_recursive($o['reply_markup']['keyboard'] ?? [], [[
					['text' => self::GAME['play draw']],
					['text' => self::GAME['show participants']],
				]]);
				$this->log->add(__METHOD__.' reply_markup=',null, [$o['reply_markup'],]);
			}

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

			//*
			$this->apiRequest($o);

			//* Добавляем себя в розыгрыш при создании
			if(!empty($this->addSelf))
			{
				$this->addSelf = null;
				$this->cmd[0] = 'participate';
				return $this->routerCmd();
			}

			//* Отправляем владельцу розыгрыша
			if(!empty($this->sendToOwner) && $this->chat_id != $data['owner']['id'])
			{
				$this->sendToOwner = null;
				$o['chat_id'] = $data['owner']['id'];
				$this->apiRequest($o);
			}
		}

		return $this;
	} //* routerCmd 💡


	private function showParticipants()
	{
		$data = &$this->data['current draws'];
		$ps = '';
		foreach($data['participants'] as $p)
		{
			$ps .= $this->showUsername($p);
		}
		return [
			'text' => "<u>Зарегистрировались:</u> " . count($data['participants']) . " чел.\n\n$ps\n<a href='{$this->urlDIR}/assets/Zorro_300.png' title='ZorroClan'>&#8205;</a>"
		];
	}

	private function showUsername($user)
	{
		return "<b>{$user['first_name']}</b> @{$user['username']} ({$user['id']})\n";
	}

	private function showMainMenu($o=[])
	{
		$keyboard = [
			[
				['text' => self::GAME['new draw']],
			],
			[
				['text' => self::GAME['balance']],
				['text' => self::GAME['info']],
			],
		];

		if(isset($this->data['current draws']))
		{
			$keyboard[0][0] = ['text' => self::GAME['participate']];
		}

		$arr = [
			//* Чат или бот?
			'text' => is_numeric(substr($this->chat_id,0,1))?
				'Вы находитесь в тестовом игровом боте. Это главное меню. Оно будет появляться при старте бота, а также во всех недоработанных функциях по умолчанию.'
			: "Бот запущен из группы, где не имеет возможности интерактивного диалога с вами.\n\nДля продолжения использования - пеерейдите в сам бот по ссылке @UniKffBot",
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		];
		return array_merge_recursive($arr,$o);
	} //* showMainMenu

} //* GameTest
