<?php
require_once "UniConstruct.trait.php";

class GameTest extends CommonBot implements Game,Draws {
	use UniConstruct;

	const
		FOLDER = 'Game',
		BASE = self::FOLDER . '/base.json';

	protected
		$data;

	private
		$addSelf,
		$toAllParticipants;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveData();


	} //* __construct


	private function init()
	{
		// $this->log->add('self::BTNS=',null,[self::BTNS]);

		if(!file_exists(self::FOLDER))
			mkdir(self::FOLDER, 0755);

		//* set data
		$this->data = file_exists(self::BASE)
			? json_decode(
				file_get_contents(self::BASE), 1
			)
			: [];

		//* Pumps
		$this->data['pumps'] = $this->data['pumps'] ?? [];
		$this->data['change'] = 0;

			return $this;
	} //* init


	private function saveData()
	{
		// $this->log->add('self::BTNS=',null,[self::BTNS]);
		if(!$this->data['change']) return;
		unset($this->data['change']);

		$this->log->add('$this->data[\'pumps\']=',null,[$this->data['pumps']]);

		file_put_contents(
			self::BASE,
			json_encode($this->data, JSON_UNESCAPED_UNICODE), LOCK_EX
		);

			return $this;
	} //* saveData


	private function routerCmd($cmd=null)
	{
		$o=null;
		$data = &$this->data['current draws'];
		// $pumps = &$this->data['pumps'];

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
								['text' => self::BTNS['pump market']],
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


			//*** Биржа насосов ***
			case 'pump market':
				$o = [
					'text' => [
						self::INFO['pump market'],
						$this->showPumps(),
					],
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::BTNS['sale blue pump']],
								['text' => self::BTNS['sale gold pump']],
							],
							[
								['text' => self::BTNS['pump market']],
								['text' => self::BTNS['general']],
							],
				],],];
				break;

			case 'sale blue pump':
			case 'sale gold pump':
				$sale = explode(' ',$this->cmd[0],2);
				$this->log->add('$sale=',null,[$sale]);
				$o = [
					'text' => [
						self::INFO['sale'][$sale[1]],
						self::INFO['unsale'],
					],
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::BTNS['pump market']],
								['text' => self::BTNS['general']],
							],
				],],];
				break;

			case 'parsePumps':
				$this->parsePumps($this->cmd[1]);
				break;

			case 'sale':
				$this->addPump($this->cmd[1]);
				break;

			case 'unsale':
				$this->removePump($this->cmd[1]);
				return $this->routerCmd('pump market');
				break;


			//*** Новый розыгрыш ***
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
								['text' => self::BTNS['general']],
							],
				],],];
				$this->addSelf = 1;

				$this->sendToAll("Создан розыгрыш от <b>{$this->cbn['from']['first_name']}</b>. Спешите принять участие!");
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
						'text' => "Кого разыгрывать собираемся, товагисч?\n\nРегистрируйте новый розыгрыш!",
					]);
					break;
				}

				shuffle($data['participants']);
				$winners = []; $winStr = '';

				for($i=0; $i < $data['prizes_count']; $i++)
				{
					if(empty($data['participants'][$i]))
						break;

					$winners[] = $data['participants'][$i];
					$winStr .= $this->showUsername($winners[$i]);
				}

				$o = [
					'text' => "<u>Победители:</u>\n\n$winStr\n<a href='{$this->urlDIR}/assets/Zorro_300.png' title='ZorroClan'>&#8205;</a>",
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::BTNS['general']],
							],
				],],];

				$o = array_merge_recursive($this->showParticipants(), $o);

				$this->toAllParticipants = 1;
				break;

			//* Участвовать
			case 'participate':
				$count = count($data['participants']);

				if(in_array($this->cbn['from'], $data['participants']))
				{
					$o = $this->showMainMenu([
						'text' => "Ты уже в регистрации на розыгрыш. Кончай сервер мучать, а то - забаню нах!\n\nА вообще уже $count чел. в розыгрыше."
					]);
					break;
				}
				elseif(
					!empty($owner = $data['owner'])
				) {
					$this->data['change']++;
					$data['participants'][]= $this->cbn['from'];
					$count++;

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
				$draw= [
					'text' => isset($data['owner'])
					? "Создан розыгрыш от {$data['owner']['first_name']}. Спешите принять участие!"
					: "На данный момент созданных розыгрышей нет. Вы можете создать свой."
				];

				$o = $this->showMainMenu($draw);
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
					['text' => self::BTNS['play draw']],
					['text' => self::BTNS['show participants']],
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

			//* Send
			if(!empty($this->toAllParticipants))
			{
				$this->toAllParticipants = null;
				foreach($data['participants'] as $p)
				{
					$o['chat_id'] = $p['id'];
					$this->apiRequest($o);
				}
				//*
				unset($data, $this->data['current draws']);
				$this->data['change']++;
			}
			else $this->apiRequest($o);

			//* Добавляем себя в розыгрыш при создании
			if(!empty($this->addSelf))
			{
				$this->addSelf = null;
				return $this->routerCmd('participate');
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
	:array
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

	/**
	 * Пакетное добавление насосов
	 */
	private function parsePumps($cmd)
	{
		$type= strpos($cmd[0],'нефтяные насосы') !== false ? 'blue' : 'gold';

		preg_match_all("~Серийный номер .+: (\\d+)$|будет работать до ([\\d\\.]+)$~im", $cmd[0], $parse);

		list(,$numbers,$dates) = $parse;

		$numbers= array_values(array_filter($numbers));
		$dates= array_values(array_filter($dates));
		$dates= array_map(function(&$i){
			return DateTime::createFromFormat('j.m.Y', $i)->format('Y-m-j');
		}, $dates);

		// $pumps= array_combine(,array_filter($dates));
		$this->log->add(__METHOD__.'',null,[$numbers,$dates]);

		foreach($numbers as $k=>$n)
		{
			$this->addPump([$type,$dates[$k],$n]);
		}
	}


	private function removePump($cmd, &$arr=null)
	{
		if(!$arr) $arr = &$this->data['pumps'];

		foreach($arr as $key=>&$val)
		{
			if(is_array($val))
			{
					$this->removePump($cmd,$val);
			}
			elseif(in_array($val, $cmd))
			{
				$this->log->add(__METHOD__.'$arr',null,[$arr, $arr[$key]]);
				unset($arr[$key]);
				$this->data['change']++;
			}
		}
	}

	private function addPump($cmd)
	{
		// list($type,$date,$numbers)= $cmd;
		list($type,$date,$numbers)= [array_shift($cmd), array_shift($cmd), $cmd];

		if(empty($date) || empty($numbers))
		{
			$o = [
				'text' => self::INFO['sale']['fail']
			];
		}
		else
		{
			$this->data['pumps'] = array_merge_recursive($this->data['pumps'], [$type=> [
				$date=> ["@{$this->cbn['from']['username']}"=> $numbers]
				// $date=> "$numbers - @{$this->cbn['from']['username']}"
			]]);

			$uPumps = &$this->data['pumps'][$type][$date]["@{$this->cbn['from']['username']}"];
			$uPumps = array_unique($uPumps);

			$this->data['change']++;
			return $this->routerCmd('pump market');
		}
	}

	private function showPumps()
	{
		$pList = '';

		foreach($this->data['pumps'] as $type=>&$p)
		{
			ksort($p, SORT_NATURAL);
			$pList.= '<b>'. self::INFO['pumpName'][$type]."</b>\n";

			foreach($p as $date=>&$val)
			{
				if(!count($val))
				{
					unset($p[$date]);
					$this->data['change']++;
				}
				else $pList.= "{$date}\n";

				foreach($val as $name=>&$num)
				{
					if(!count($num))
					{
						unset($val[$name]);
						$this->data['change']++;
					}
					else $pList.= "{$name} - " . implode(', ',$num) . "\n";
				}
			}
		}
		if(strlen($pList))
			$pList = "<b><u>На продажу будут выставлены:</u></b>\n$pList";
		return $pList;
	}

	private function showMainMenu($o=[])
	{
		$keyboard = [
			[
				['text' => self::BTNS['new draw']],
			],
			[
				['text' => self::BTNS['balance']],
				['text' => self::BTNS['info']],
			],
		];

		if(isset($this->data['current draws']))
		{
			$keyboard[0][0] = ['text' => self::BTNS['participate']];
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

} //* GameTest
