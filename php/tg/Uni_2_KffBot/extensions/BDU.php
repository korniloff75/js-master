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
		&& $this->user_id === $this->data['current draws']['owner']['id'];

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
		$curBase= &$this->data[$this->user_id];

		$name= $curBase['realName'] ?? $curBase['from']['first_name'].($curBase['from']['last_name']??' Незнакомец');

		return [
			'text' => "Привет, $name! Давай знакомиться получше!\n" . $this->about($curBase),
			'reply_markup' => ['keyboard' => [
				[
					['text' => self::CMD['BDU']['fio']],
					['text' => self::CMD['BDU']['category']],
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
		$curBase= &$this->data[$this->user_id];

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
			'text' => "Здесь Вы можете ввести данные о своих возможностях и профессиональных навыках по одному на каждой строке\n\n<u>Например:</u>\n\n<b>Инженер\nСтроительство\nСейсмика</b>\n\n".$this->about(),
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
			'text' => "Введите регион своего влияния\n\n<u>Например:</u>\n\n<b>Москва\nИнтернет</b>\n\n".$this->about(),
			// 'reply_markup' => ['keyboard' => []]
		];
	}

	private function category()
	{
		$this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>'category'
		]);

		$o= [
			'text' => $this->about(),
			'reply_markup' => [
				"keyboard" => [[
					['text' => self::CMD['BDU']['add_category']],
					['text' => self::CMD['BDU']['remove_category']],
				],
				[
					['text' => self::CMD['BDU']['familiar']],
					['text' => self::BTNS['general']],
				],],
			],
		];

		return $o;
	}

	private function show_category_buttons(&$ikb,$method='save')
	{
		foreach(array_chunk(self::CATEGORIES,3) as $nr=>$row)
		{
			$ikb[]= [];
			foreach($row as $spec)
			{
				$ikb[$nr][]= [
					'text'=> $spec,
					'callback_data'=> "/BDU/{$method}_category__$spec",
				];
			}
		}
		// return $o;
	}

	private function add_category()
	{
		/* $this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>'category'
		]); */

		$o= [
			'text' => "Выберите категорию для <u>добавления</u> \n\n".$this->about(),
			'reply_markup' => [
				"inline_keyboard" => [],
			],
		];

		$this->show_category_buttons($o['reply_markup']['inline_keyboard']);

		return $o;
	}

	private function remove_category()
	{
		/* $this->UKB->setStatement([
			'wait familiar data'=>1,
			'dataName'=>'category'
		]); */

		$o= [
			'text' => "Выберите категорию для <u>удаления</u> \n\n".$this->about(),
			'reply_markup' => [
				"inline_keyboard" => [],
			],
		];

		$this->show_category_buttons($o['reply_markup']['inline_keyboard'], 'unsave');

		return $o;
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

		$curBase= &$this->data[$this->user_id];

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
		$curBase= &$this->data[$this->user_id];
		$curBase['hashtags']= $arrStr;
		$this->data['change']++;
		$this->send(['text'=>$this->about()]);
	} //* save_hashtags

	private function save_region($arrStr)
	{
		$curBase= &$this->data[$this->user_id];
		$curBase['region']= $arrStr;
		$this->data['change']++;
		$this->send(['text'=>$this->about()]);
	} //* save_region

	private function save_category($arrStr)
	{
		$curBase= &$this->data[$this->user_id];
		$curBase['category'][]= $arrStr[0];
		$this->data['change']++;
		$this->send(['text'=>$this->about($curBase)]);
		$this->log->add(__METHOD__." \$arrStr[0]=",null,[$arrStr[0]]);
	} //* save_category

	private function unsave_category($arrStr)
	{
		$curBase= &$this->data[$this->user_id];

		$curBase['category']= array_filter($curBase['category'], function(&$i)use($arrStr){
			return $i !== $arrStr[0];
		});
		$this->data['change']++;
		$this->send(['text'=>$this->about($curBase)]);
		// $this->log->add(__METHOD__." curBase=",null,[$curBase]);
	} //* unsave_category


	//* Текущие данные
	private function about($curBase=null)
	:string
	{
		$header= '';
		if(!$curBase)
		{
			$curBase= &$this->data[$this->user_id];
			$header= "<u>Текущие данные:</u>\n";
		}
		$about= '';

		foreach($curBase as $fName=>$fld)
		{
			switch ($fName) {
				case 'realName':
					$about.= "Ваше реальное имя - {$fld}\n";
					break;
				case 'category':
					$about.= "Ваши категории - ". implode(', ', $fld) ."\n";
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


	//* Пользователи
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
						if(!empty($fld['username'])) $users.= " - {$fld['username']}";
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
			/* 'reply_markup' => ["keyboard" => [
				[
					['text' => self::CMD['BDU']['list_categories']],
					['text' => self::CMD['BDU']['scope']],
					['text' => self::CMD['BDU']['users']],
				],
				[
					['text' => self::BTNS['general']],
				],
			]], */
		];
	}


	//* Поиск
	private function scope()
	{
		$txt= '';
		$catTags= $this->collectCategories();

		foreach($catTags as $cat=>&$tags)
		{
			natsort($tags);
			$txt.= "<u><b>$cat</b></u>\n\n" . implode(PHP_EOL,$tags) . "\n\n";
		}

		// $this->log->add(__METHOD__." catTags=",null,[$catTags,]);

		return [
			'text'=>$txt,
			'reply_markup' => ["keyboard" => [
				[
					['text' => self::CMD['BDU']['list_categories']],
					// ['text' => self::CMD['BDU']['scope']],
					['text' => self::CMD['BDU']['users']],
				],
				[
					['text' => self::BTNS['general']],
				],
			]],
		];
	} //* scope


	/**
	 ** Собираем массив с категориями
	 * @param filter - фильтр по категориям
	 */
	private function collectCategories(?string $filter=null)
	:array
	{
		$data= &$this->data;
		$catTags=[];
		$region='(Регион не указан)';

		foreach ($data as $id => &$uData)
		{
			$curCats = $uData['category']??['Другое'];
			if($filter && !in_array($filter,$curCats))
				continue;

			if(!empty($tags= $uData['hashtags']??null)) foreach($tags as &$tag)
			{
				if(!empty($uData['region'])) $region= '(' . implode(', ',$uData['region']) . ')';

				$tag= str_replace([' ','-'],'_',$tag);
				$strTag= "#$tag - ".$this->showUsername($data[$id]) . $region . PHP_EOL;

				//* Добавляем в каждую категорию
				foreach($curCats as &$cat)
				{
					$catTags[$cat][]= $strTag;
				}
			}
		}

		ksort($catTags, SORT_NATURAL);

		return $catTags;
	} //* collectCategories


	//* Список категорий
	private function list_categories()
	{
		// $this->show_category_buttons()
		$o= [
			'text' => "Выберите категорию для <u>просмотра</u> \n\n",
			'reply_markup' => [
				"inline_keyboard" => [],
			],
		];

		$this->show_category_buttons($o['reply_markup']['inline_keyboard'], 'list');
		return $o;
	}

	/**
	 ** Список тегов по выбранной категории
	 */
	private function list_category($arrStr)
	{
		$curBase= &$this->data[$this->user_id];
		$catName= $arrStr[0];
		$this->data['change']= 0;

		$txt= '';
		if(
			!$curCat= $this->collectCategories($catName)[$catName]
		) $txt= "В этой категории пока пусто";
		else
		{
			$txt= implode(PHP_EOL,$curCat) . "\n\n";
		}
		$this->send([
			'text'=>"<u><b>$catName</b></u>\n\n$txt"
		]);
		$this->log->add(__METHOD__." \$catName,curCat=",null,[$catName,$curCat]);
	}
} //* BDU
