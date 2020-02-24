<?php

class BDU extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../BDU',
		BASE = self::FOLDER . '/base.json';

	protected
		$draws;

	private
		$toAllParticipants;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveCurData();

	} //* __construct


	private function init()
	{
		// $this->log->add('$this->BTNS=',null,[$this->BTNS]);

		$this->getCurData();

		$this->drawsOwner = isset($this->data['current draws']['owner'])
		&& $this->chat_id === $this->data['current draws']['owner']['id'];

		$this->data['change'] = 0;

		//* Ждем данные
		if(!empty($this->statement['wait familiar data']))
		{
			//* Отправляем на приём данных
			$this->checkFamilar($this->statement['dataname']);
			$this->UKB->setStatement([
				'wait familiar data'=>0,
			]);
		}

		$from= &$this->message['from'];
		$curBase= &$this->data["{$from['id']}"];

		//* Пишем в базу from
		if(empty($curBase['from']))
		{
			$curBase['from']= $from;
			$this->data['change']++;
		}

		// $this->log->add(__METHOD__.' $this->data=',null,$this->data);

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		$o = parent::routerCmd($cmd) ?? [];

		if(!$cmd)
			$cmd = &$this->cmd[0];
		$opts = &$this->cmd[1];

		if(method_exists(__CLASS__, $cmd))
			$o = array_merge_recursive($o, $this->{$cmd}($opts));

		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

		$this->send($o);

		return $this;
	}


	//note Знакомство
	private function familiar($opts)
	:array
	{
		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];

		$name= $curBase['realName'] ?? $curBase['from']['first_name'].($curBase['from']['last_name']??'Незнакомец');

		return [
			'text' => "Привет, $name! Давай знакомиться получше!",
			'reply_markup' => ['keyboard' => [
				[
					['text' => self::CMD['BDU']['fio']],
					['text' => self::CMD['BDU']['hashtags']],
				],
				[
					['text' => self::BTNS['general']],
				],
			]]
		];
	}


	//* Сохраняем данные из json
	private function fio()
	{
		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];

		$name= $curBase['realName'] ?? $curBase['from']['first_name'].($curBase['from']['last_name']??'');

		if($notRealName = empty($curBase['realName']))
		{
			$this->UKB->setStatement([
				'wait familiar data'=>1,
				// 'familiar from'=>$curBase['from'],
				'dataname'=>'fio'
			]);
		}

		return [
			'text' => "Приветствуем Вас <b>$name</b>! ". ($notRealName? "Рады знакомству. Введите своё настоящее имя. Даже, если оно совпадает с Вашим ником.": ""),
			// 'reply_markup' => ['keyboard' => []]
		];
	}

	private function hashtags()
	{
		$this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataname'=>'hashtags'
		]);
		// if($this->statement[])
		return [
			'text' => "Здесь Вы можете ввести данные о своих возможностях и профессиональных навыках по одному на каждой строке\n\nНапример:\n\n<b>Инженер\nСтроительство\nСейсмика</b>",
			// 'reply_markup' => ['keyboard' => []]
		];
	}

	//* Приём данных
	private function checkFamilar($dataname)
	{
		$this->log->add(__METHOD__.' $this->message',null,[$this->message]);

		$txt= trim($this->message['text']);

		if(method_exists(__CLASS__, "save_$dataname"))
		{
			$this->{"save_$dataname"}(explode("\n",$txt));
		}
		else
			$this->log->add(__METHOD__." method save_$dataname is FAIL",E_USER_WARNING);

	}


	private function save_fio($arrStr)
	{
		$name= $arrStr[0];

		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];

		$curBase['realName']= $name;
		$this->data['change']= 1;

		return $this->send([
			'text' => "Благодарю Вас, <b>$name</b>! Можете ввести свой стэк возможностей. Инструкция по вводу появится после нажатия на кнопку ниже.",
			// 'reply_markup' => ['keyboard' => []]
		]);

		// todo rudiment
		//* callback
		function validName($name)
		{
			$notLetters = !preg_match("/[\w\W]{3,}/u",$name);

			$notStrings = !is_string($name);
			$name = trim($name);
			if ($notStrings || $notLetters || strlen($name)<3) {
				return false;
			} else {
				return $name;
			}
		}

		$fio = filter_var_array(
			[
				'name'=>$name,
				'fatherName'=>$fatherName,
				'family'=>$family
			], [
				'name'=> [
					'filter'=> FILTER_CALLBACK,
					'options' => 'validName'
				]
			]
		);

		$this->log->add(__METHOD__." fio=",null,[$fio]);

		if(!$fio['name'])
		{
			$this->send(['text' => "Ваши данные не удалось обработать, попробуйте ещё раз по инструкции"]);
			die;
		}
		else
		{
			$strName= implode(' ',$fio);
			$this->send(['text' => "Ваши данные успешно сохранены. Итак, Вас зовут <b>$strName</b>. Продолжим?"]);
			$this->UKB->setStatement([
				'wait familiar data'=>0,
				'familiar fio'=>$fio,
				'dataname'=>''
			]);
		}
	} //* save_name


	private function save_hashtags($arrStr)
	{
		$from= &$this->message['from'];
		$curBase= &$this->data["{$from['id']}"];
		$curBase['hashtags']= $arrStr;
		$this->data['change']= 1;
	} //* save_hashtags


	//* Отправляем
	public function send(array $o)
	{
		$this->log->add(__METHOD__.' $o',null,[$o]);

		if(empty($o)) return;

		//* Подготовка и отправка

		//* add keyboard options
		if(!empty($o['reply_markup']['keyboard']))
		{
			//* Кнопки для организатора
			if(isset($this->data['current draws']) && $this->statement['drawsOwner'])
			{
				$keyboard = [
					['text' => $this->BTNS['play draw']],
					['text' => $this->BTNS['show participants']],
				];
			}
			//* Участвовать
			elseif(isset($this->data['current draws']) && !$this->drawsOwner)
			{
				if(!in_array($this->cbn['from'], $draws['participants']))
					$keyboard = [['text' => self::CMD['Draws']['participate']]];
			}
			//* Создать
			else
				$keyboard = [['text' => self::CMD['Draws']['new draw']]];

			$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];

			//* Добавляем кнопки
			if(
				!empty($keyboard) && !empty($this->cmd[0])
				// && !in_array($this->cmd[0], ['general','start'])
				&& in_array($this->cmd[0], ['advanced'])
			)
				$o['reply_markup']['keyboard'] = array_merge_recursive($o['reply_markup']['keyboard'], [$keyboard]);
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

		/* //* Добавляем себя в розыгрыш при создании
		if(!empty($this->addSelf))
		{
			$this->addSelf = null;
			return $this->routerCmd('participate');
		} */

		//* Отправляем админу
		if(!empty($this->sendToOwner) && !$this->statement['BDU_admin'])
		{
			$this->sendToOwner = null;
			$o['chat_id'] = $draws['owner']['id'];
			$this->apiRequest($o);
		}
	}

} //* BDU
