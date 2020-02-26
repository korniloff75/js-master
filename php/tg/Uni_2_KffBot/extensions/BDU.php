<?php

class BDU extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../'.__CLASS__,
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
		$this->setConstruct($UKB, $cmd)
			->init()
			->routerCmd();
		// $this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveCurData();

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
			$this->checkFamilar($this->statement['dataName']);
			$this->UKB->setStatement([
				'wait familiar data'=>0,
			]);
			die;
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

		if(count($o))
		{
			$this->send($o);
		}
		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

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
			'text' => "Привет, $name! Давай знакомиться получше!\n" . $this->about($curBase),
			'reply_markup' => ['keyboard' => [
				[
					['text' => self::CMD['BDU']['fio']],
					['text' => self::CMD['BDU']['hashtags']],
				],
				[
					['text' => self::CMD['BDU']['region']],
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
				'dataName'=>'fio'
			]);
		}

		$o= [
			'text' => "Приветствуем Вас <b>$name</b>! ". ($notRealName ? "Рады знакомству. Введите своё настоящее имя. Даже, если оно совпадает с Вашим ником." : "Хотите изменить своё имя?")
		];

		if(!$notRealName)
		{
			$o['reply_markup']['inline_keyboard']= [
				[[
					'text'=>'Изменить',
					'callback_data'=>'/BDU/changeFamilar__fio',
				]]
			];
		}

		return $o;
	}

	private function hashtags()
	{
		$this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>'hashtags'
		]);
		// if($this->statement[])
		return [
			'text' => "Здесь Вы можете ввести данные о своих возможностях и профессиональных навыках по одному на каждой строке\n\nНапример:\n\n<b>Инженер\nСтроительство\nСейсмика</b>\n\n".$this->about(),
			// 'reply_markup' => ['keyboard' => []]
		];
	}

	private function region()
	{
		$this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>'region'
		]);
		// if($this->statement[])
		return [
			'text' => "Введите регион своего влияния\n\nНапример:\n\n<b>Москва\nИнтернет</b>\n\n".$this->about(),
			// 'reply_markup' => ['keyboard' => []]
		];
	}


	//* Приём и сохранение данных
	private function checkFamilar($dataName)
	{
		$this->log->add(__METHOD__.' $this->message,$dataName',null,[$this->message,$dataName]);

		$txt= trim($this->message['text']);

		if(method_exists(__CLASS__, "save_$dataName"))
		{
			$this->{"save_$dataName"}(explode("\n",$txt));
		}
		else
			$this->log->add(__METHOD__." method save_$dataName is FAIL",E_USER_WARNING);
	}


	private function changeFamilar(array $opts)
	{
		$dataName= $opts[0];
		// if(!$dataName) $dataName= $this->cmd[1][0];

		$this->log->add(__METHOD__." \$opts,\$this->cmd",null,[$opts,$this->cmd]);

		$this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>$dataName
		]);

		return ['text' => "Введите новые данные."];
	}


	private function save_fio($arrStr)
	{
		$name= $arrStr[0];

		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];

		$curBase['realName']= $name;
		$this->data['change']++;

		return $this->send([
			'text' => $this->about()
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
			$this->send(['text'=>$this->about()]);
		}
	} //* save_name


	private function save_hashtags($arrStr)
	{
		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];
		$curBase['hashtags']= $arrStr;
		$this->data['change']++;
		$this->send(['text'=>$this->about()]);
	} //* save_hashtags

	private function save_region($arrStr)
	{
		$id= &$this->message['from']['id'];
		$curBase= &$this->data["$id"];
		$curBase['region']= $arrStr;
		$this->data['change']++;
		$this->send(['text'=>$this->about()]);
	} //* save_region


	private function about($curBase=null)
	:string
	{
		$header= '';
		if(!$curBase)
		{
			$id= &$this->message['from']['id'];
			$curBase= &$this->data["$id"];
			$header= "<u>Текущие данные:</u>\n";
		}
		$about= '';

		foreach($curBase as $fName=>$fld)
		{
			switch ($fName) {
				case 'realName':
					$about.= "Ваше реальное имя - {$fld}\n";
					break;
				case 'hashtags':
					$about.= "Ваши возможности - ". implode(', ', $fld) ."\n";
					break;
				case 'region':
					$about.= "Ваша зона влияния - ". implode(', ', $fld) ."\n";
					break;
			}
		}

		return empty($about) ? "Пока нам о Вас ничего не известно. Исправим?\n" : $header.$about;
	}


	//* users
	private function users()
	{
		$users= '';
		foreach($this->data as $curBase)
		{
			foreach($curBase as $fName=>$fld)
			{
				switch ($fName) {
					case 'from':
						$users.= "{$fld['first_name']}";
						if(!empty($fld['username'])) $users.= " - @{$fld['username']}";
						$users.= "\n";
						break;
					case 'realName':
						$users.= "Реальное имя - {$fld}\n";
						break;
					case 'hashtags':
						$users.= "Возможности - ". implode(', ', $fld) ."\n";
						break;
					case 'region':
						$users.= "Зона влияния - ". implode(', ', $fld) ."\n";
						break;
				}
			}
			$users.= "\n";
		}
		return [
			'text'=>$users,
			'reply_markup' => ["keyboard" => [
				[
					['text' => self::CMD['BDU']['scope']],
					// ['text' => self::CMD['BDU']['users']],
				],
				[
					['text' => self::BTNS['general']],
				],
			]],
		];
	}

	private function scope()
	{
		$data= &$this->data;
		//* Сортировка по 2 ключам
		$arrRegions = [];
		$arrHashtags = [];
		$txt='';

		foreach ($data as $id => &$row)
		{
			// $this->log->add(__METHOD__." row=",null,[$row]);
			if(isset($row['region']))
			{
				$arrRegions[$id] = $row['region'];
			}
			if(isset($row['hashtags']))
			{
				$arrHashtags[$id] = $row['hashtags'];
			}

		}

		array_multisort($arrRegions,  SORT_NATURAL, $arrHashtags,  SORT_NATURAL, $data);

		foreach ($arrHashtags as $id => &$tags)
		{
			$txt.= implode(PHP_EOL, $tags).PHP_EOL;
		}

		$this->log->add(__METHOD__." data=",null,[/* $data, */$arrRegions,$arrHashtags]);

		return [
			'text'=>$txt
		];
	}
} //* BDU
