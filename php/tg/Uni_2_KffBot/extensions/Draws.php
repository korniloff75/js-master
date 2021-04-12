<?php

class Draws extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../Game_2',
		BASE = self::FOLDER . '/base.json',
		IMG = '/assets/roullete.jpg';

	protected
		$draws;

	private
		$drawsOwner,
		$addSelf,
		$toAllParticipants;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		// parent::__construct();

		// *Запрет запуска из комментариев
		// if($UKB->chat_id === -1001305018802)
		// 	die;

		$this->setConstruct($UKB, $cmd)
			->init()
			->routerCmd();
			// ->saveCurData();

	} //* __construct


	private function init()
	{
		// $this->log->add('$this->BTNS=',null,[$this->BTNS]);

		$this->getCurData();

		$this->drawsOwner = isset($this->data['current draws']['owner'])
		&& $this->user_id === $this->data['current draws']['owner']['id'];
		// $this->drawsOwner = $this->UKB->getStatement()->statement['drawsOwner'];

		if(!$this->drawsOwner)
			$this->UKB->setStatement([
				'drawsOwner'=>0
			]);

		$this->data['change'] = 0;

		// $this->log->add(__METHOD__.' $this->data=',null,$this->data);

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		$o = parent::routerCmd($cmd);

		$this->log->add(__METHOD__.' $o=',null,[$o]);

		$draws = &$this->data['current draws'];
		// $pumps = &$this->data['pumps'];

		if(!$o) switch ($cmd ?? $this->cmd[0]) {
			//*** Новый розыгрыш ***
			case 'draws':
			case 'new draw':
				if(!empty($draws))
				{
					$o = $this->showMainMenu([
						'text' => "Вы не можете создать розыгрыш, пока не разыгран предыдущий от " . $this->showUsername($draws['owner']) . ". Но вы можете участвовать в существующем - /participate",
						'reply_markup'=> [
							'inline_keyboard'=>[[[
								'text' => self::CMD['Draws']['participate'],
								'callback_data'=> '/Draws/participate'
							]]]
						]
					]);
				break;
				}

				$o = [
					'text' => 'Введите количество призов в розыгрыше.',
					'reply_markup' => [
						"inline_keyboard" => [
							[
								['text' => 1, 'callback_data' => '/Draws/prizes_count__1'],
								['text' => 2, 'callback_data' => '/Draws/prizes_count__2'],
								['text' => 3, 'callback_data' => '/Draws/prizes_count__3'],
							],
				],],];

				$this->statement = $this->UKB->setStatement([
					'drawsOwner'=>1
				]);
				$this->log->add('new draw $this->statement[drawsOwner]=',null, [$this->statement['drawsOwner']]);
				break;

			case 'prizes_count':
				$this->log->add('prizes_count $this->drawsOwner=',null, [$this->drawsOwner]);
				$this->log->add('prizes_count $this->statement[drawsOwner]=',null, [$this->statement['drawsOwner']]);

				if( !$this->drawsOwner && !$this->statement['drawsOwner'] )
				{
					$o = $this->showMainMenu([
						'text'=> 'Менять количество призов может только спонсор розыгрыша!',
					]);
					break;
				}

				$this->data['change']++;
				$draws = [
					'owner' => $this->cbn['from'],
					'prizes_count' => $this->cmd[1][0],
				];

				$this->statement = $this->UKB->setStatement([
					'drawsOwner'=>1
				]);

				$o = $this->showMainMenu([
					'text'=> 'Розыгрыш создан.',
				]);

				$this->addSelf = 1;

				//! sendToAll
				// $this->sendToAll([
				$this->sendToChats([
					'text'=> "Создан розыгрыш от " . $this->showUsername($draws['owner']) . ". Спешите принять участие!",
					'reply_markup'=> [
						'inline_keyboard'=>[[[
							'text' => self::CMD['Draws']['participate'],
							'callback_data'=> 'draws/participate'
						]]]
					]
				]);
				break;

			case 'cancel draw':
				if(!$this->is_owner && empty($this->statement['drawsOwner']))
				{
					$o= ['text'=>'Вы не можете отменить этот розыгрыш'];
					break;
				}

				$o= $this->showMainMenu(['text'=>"Розыгрыш от " . $this->showUsername($draws['owner']) . " был успешно удалён"]);
				//! sendToChats
				$this->sendToChats($o);

				unset($draws, $this->data['current draws']);
				$this->data['change']++;

				die;
				break;

			case 'show participants':
				$o = $this->showParticipants();
				$o['text'] .= "\n<a href='{$this->urlDIR}".self::IMG."'>&#8205;</a>";
				break;

			//* Розыгрыш
			case 'play draw':
				$this->statement = $this->UKB->setStatement([
					'drawsOwner'=>0
				]);

				if(!count($draws['participants']))
				{
					$o = $this->showMainMenu([
						'text' => "Кого разыгрывать собираемся, товагисч?\n\nРегистрируйте новый розыгрыш!",
					]);
					break;
				}

				shuffle($draws['participants']);
				$winners = []; $winStr = '';

				for($i=0; $i < $draws['prizes_count']; $i++)
				{
					if(empty($draws['participants'][$i]))
						break;

					$winners[] = $draws['participants'][$i];
					$winStr .= $this->showUsername($winners[$i],'tag');
				}

				$o = $this->showMainMenu([
					'text' => "<u>Победители:</u>\n\n$winStr\n<a href='{$this->urlDIR}".self::IMG."'>&#8205;</a>",
				]);

				$o = array_merge_recursive($this->showParticipants(), $o);
				//! sendToChats
				$this->sendToChats($o);
				unset($draws, $this->data['current draws']);
				$this->data['change']++;
				die;
				break;

			//* Участвовать
			case 'participate':
				$count = count($draws['participants']);

				if(in_array($this->cbn['from'], $draws['participants']))
				{
					$o = $this->showMainMenu([
						'text' => "Ты уже в регистрации на розыгрыш. Кончай сервер мучать, а то - забаню нах!\n\nА вообще уже $count чел. в розыгрыше."
					]);
					break;
				}
				elseif(
					!empty($owner = $draws['owner'])
				) {
					$this->data['change']++;
					$draws['participants'][]= $this->cbn['from'];
					$count++;

					$o = [
						'text' => "Участник " . $this->showUsername($this->cbn['from']) . " зарегистрировался в розыгрыше от {$owner['first_name']}.\nНа данный момент зарегистрировано {$count} чел.",
					];
					$this->sendToOwner = 1;
				}
				else $o = [
					'text' => 'Для вас нет доступных розыгрышей в настоящий момент. Но вы можете создать новый.',
					'reply_markup'=> [
						'inline_keyboard'=>[[[
							'text' => self::CMD['Draws']['new draw'],
							'callback_data'=> 'Draws/new draw'
						]]]
					]
				];
				break;

			case 'start':
			case 'general':
				$draw= [
					/* 'text' => isset($draws['owner'])
					? "Создан розыгрыш от {$draws['owner']['first_name']}. Спешите принять участие!"
					: "На данный момент созданных розыгрышей нет. Вы можете создать свой." */
				];

				$o = $this->showMainMenu($draw);
				break;

			default: die;
		} //*switch


		//note Подготовка и отправка
		if($o)
		{
			$this->log->add(__METHOD__.': Подготовка и отправка',null,['$this->is_owner'=>$this->is_owner]);
			/* if(is_null($this->is_owner))
				$this->log->add(__METHOD__.' is_null $this->is_owner this=',null,[$this]); */

			//* Кнопки для организатора
			if(
				!empty($draws) && (
					$this->statement['drawsOwner']
					|| $this->is_owner
				)
			)
			{
				$keyboard = [
					['text' => self::CMD['Draws']['play draw']],
					['text' => self::CMD['Draws']['show participants']],
					['text' => self::CMD['Draws']['cancel draw']],
				];
			}
			//* Участвовать
			elseif(
				!empty($draws) && !$this->drawsOwner
			)
			{
				if(!in_array($this->cbn['from'], $draws['participants']))
					$keyboard = [['text' => self::CMD['Draws']['participate']]];
			}
			//* Создать
			else
				// $keyboard = [['text' => self::CMD['Draws']['new draw']]];
				// *Убираем розыгрыши
				$keyboard = [];

			//* Добавляем кнопки
			if(
				!empty($keyboard) && !empty($this->cmd[0])
				// && !in_array($this->cmd[0], ['general','start'])
				&& in_array($this->cmd[0], ['advanced'])
			)
				$o['reply_markup']['keyboard'] = array_merge_recursive($o['reply_markup']['keyboard'], [$keyboard]);

			//* Send
			$o['chat_id']= $this->user_id;
			$this->apiRequest($o);

			//* Добавляем себя в розыгрыш при создании
			if(!empty($this->addSelf))
			{
				$this->addSelf = null;
				return $this->routerCmd('participate');
			}

			//* Отправляем владельцу розыгрыша
			if(!empty($this->sendToOwner) && !$this->statement['drawsOwner'])
			{
				$this->sendToOwner = null;
				$o['chat_id'] = $draws['owner']['id'];
				$this->apiRequest($o);
			}
		}

		return $this;
	} //* routerCmd 💡


	private function showParticipants()
	:array
	{
		$draws = &$this->data['current draws'];
		$ps = '';
		foreach($draws['participants'] as $p)
		{
			$ps .= $this->showUsername($p);
		}
		return [
			'text' => "<u>Зарегистрировались:</u> " . count($draws['participants']) . " чел.\n\n$ps\n<a href='{$this->urlDIR}".self::IMG."'>&#8205;</a>"
		];
	}

} //* Draws
