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
					$o = [
						'text' => 'Вы не можете создать розыгрыш, пока не разыгран предыдущий.'
					];
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
								['text' => self::GAME['play draw']],
								['text' => self::GAME['show participates']],
							],
							[
								['text' => self::GAME['balance']],
								['text' => self::GAME['info']],
							],
							[
								['text' => self::GAME['general']],
							],
				],],];
				$this->addSelf = 1;
				break;

			case 'show participates':
				$ps = '';
				foreach($data['participants'] as $p)
				{
					$ps .= $this->showUsername($p);
				}
				$o = [
					'text' => "<u>Зарегистрировались:</u> " . count($data['participants']) . " чел.\n\n$ps"
				];
				break;

			//* Розыгрыш
			case 'play draw':
				shuffle($data['participants']);
				$winners = []; $winStr = '';

				for($i=0; $i < $data['prizes_count']; $i++)
				{
					$winners[] = $data['participants'][$i];
					$winStr .= $this->showUsername($winners[$i]);
				}

				$o = [
					'text' => "<u>Победители:</u>\n\n$winStr",
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::GAME['general']],
							],
				],],];

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
				else $o = [
					'text' => 'Для вас нет доступных розыгрышей в настоящий момент.',
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::GAME['new draw']],
							],
							[
								['text' => self::GAME['balance']],
								['text' => self::GAME['info']],
							],
							[
								['text' => self::GAME['general']],
							],
				],],];
				break;

			default:
				$o = $this->showMainMenu();
				break;
		} //*switch

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
					['text' => self::GAME['show participates']],
				]]);
				$this->log->add(__METHOD__.' reply_markup=',null, [$o['reply_markup'],]);
			}

			if(!empty($o['reply_markup']['keyboard']))
			{
				$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
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
			if(!empty($this->sendToOwner) && $this->chat_id !== $data['owner']['id'])
			{
				$this->sendToOwner = null;
				$o['chat_id'] = $data['owner']['id'];
				$this->apiRequest($o);
			}
		}

		return $this;
	} //* routerCmd


	private function showUsername($user)
	{
		return "<b>{$user['first_name']}</b> @{$user['username']} ({$user['id']})\n";
	}

	// 💡

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
			'text' => is_numeric(substr($this->chat_id,0,1))?
				'Вы находитесь в тестовом игровом боте. Это главное меню. Оно будет появляться при старте бота, а также во всех недоработанных функциях по умолчанию.'
			:	"Бот запущен из группы, где не имеет возможности интерактивного диалога с вами.\n\nДля продолжения использования - пеерейдите в сам бот по ссылке @UniKffBot",
			'reply_markup' => [
				"keyboard" => $keyboard,
			]
		];
		return array_merge_recursive($arr,$o);
	} //* showMainMenu

} //* GameTest
