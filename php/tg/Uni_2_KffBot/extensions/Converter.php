<?php

class Converter extends Helper
{
	use UniConstruct;


	/**
	 * @param cmd - 'cmdName__opt1__opt2__...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)
			// ->getCurData()
			->inputDataRouter()
			->routerCmd();

	} //* __construct


	private function init()
	{
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
			if(!empty($id= $this->statement['last']['message_id']))
			{
				$o['message_id']= $id;
				$this->apiRequest($o, 'editMessageText');
				// $this->apiResponseJSON($o, 'editMessageText');
			}
			else
				$this->apiResponseJSON($o);
		}
		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

		return $this;
	}


	private function Converter($opts=null)
	{
		$o= [
			'text'=>'Выберите раздел',
		];

		$ikb= &$o['reply_markup']['inline_keyboard'];
		$cats= [];

		foreach(self::CALC_DATA as $cat=>&$arr)
		{
			$cats[]= ['text'=>self::ALIASES[$cat], 'callback_data'=>"Converter/cat__" . $cat];
		}

		foreach(array_chunk($cats, 3) as $nr=>&$row)
		{
			// $ikb[]= [];
			foreach($row as &$btn)
			{
				$ikb[$nr][]= $btn;
			}
		}

		$this->statement['last']= null;
		return $o;
	}


	/**
	 * Вывод категории
	 */
	private function cat($opts)
	{
		$o= [
			'text'=>'Выберите единицу измерения',
		];

		$ikb= &$o['reply_markup']['inline_keyboard'];

		// $this->log->add(__METHOD__.' $opts=',null,[$opts,$name,$val]);

		foreach(array_chunk(self::CALC_DATA[$opts[0]], 3) as $nr=>&$row)
		{
			foreach($row as &$btn)
			{
				// Converter/
				$ikb[$nr][]= ['text'=>$btn['n'], 'callback_data'=>"calc__{$opts[0]}__" . json_encode($btn, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES)];
			}
		}
		return $o;
	}


	/**
	 * Вычисления
	 */
	private function calc($opts)
	{
		/* $o= [
			'text'=>'Выберите единицу измерения',
		]; */
		$txt= "Введите количество для расчёта\n\n";

		// $this->log->add(__METHOD__.' $opts=',null,[$opts,$name,$val]);

		/* $this->UKB->setStatement([
			'wait data'=>1,
			'dataName'=>'calc'
		]); */

		$from_cat= self::CALC_DATA[$opts[0]];

		$from= json_decode($opts[1],1);

		$this->log->add(__METHOD__.' $opts, from=',null,[$opts,$from]);

		$calcData= array_filter($from_cat, function($i){
			return $i['n'] !== $from['n'];
		});

		foreach($calcData as &$to)
		{
			$val= (1 - ($from['const']??0)) / $to['v'] * $from['v'] + ($to['const']??0);
			$txt.= "1 {$from['n']} = $val {$to['n']}\n";

		}
		return [
			'text'=>$txt,
		];
	}


	const ALIASES = [
		"load"=> "Нагрузки",
		"load_udel"=> "Удельные нагрузки",
		"pressure"=> "Давление",
		"speed"=> "Скорость",
		"area"=> "Площадь",
		"volume"=> "Объём",
		"temperature"=> "Температура",
	],

	CALC_DATA = [
		"load"=> [
			[
				"n"=> 'Ньютон (Н)',
				"v"=> 1
			],
			[
				"n"=> 'кН',
				"v"=> 1000
			],
			[
				"n"=> 'МН',
				"v"=> 1e6
			],
			[
				"n"=> 'Грамм (г)',
				"v"=> 9.807e-3
			],
			[
				"n"=> 'кг',
				"v"=> 9.807
			],
			[
				"n"=> 'тонна (т)',
				"v"=> 9.807e3
			],
		],


		"load_udel"=> [
			[
				"n"=> 'Н/м',
				"v"=> 1
			],
			[
				"n"=> 'Н/см',
				"v"=> 100
			],
			[
				"n"=> 'кН/м',
				"v"=> 1000
			],
			[
				"n"=> 'кН/см',
				"v"=> 1e5
			],
			[
				"n"=> 'кг/м',
				"v"=> 9.807
			],
			[
				"n"=> 'кг/см',
				"v"=> 980.7
			],
			[
				"n"=> 'т/м',
				"v"=> 9.807e3
			],
			[
				"n"=> 'т/см',
				"v"=> 9.807e5
			],
		],


		"pressure"=> [
			[
				"n"=> 'Паскаль',
				"v"=> 1
			],
			[
				"n"=> 'Атмосфера',
				"v"=> 101325
			],
			[
				"n"=> 'Бар',
				"v"=> 100000
			],
			[
				"n"=> 'См. ртутного ст.',
				"v"=> 1333.22387415
			],
			[
				"n"=> 'См. водяного ст.',
				"v"=> 98.0638
			],
			[
				"n"=> 'Мм. ртутного ст.',
				"v"=> 133.322387415
			],
			[
				"n"=> 'Мм. водяного ст.',
				"v"=> 9.80638
			],
			[
				"n"=> 'Кгс/м2',
				"v"=> 9.80665
			],
			[
				"n"=> 'Кгс/см2',
				"v"=> 98066.5
			],
			[
				"n"=> 'Кгс/мм2',
				"v"=> 9806650
			],
			[
				"n"=> 'Н/м2',
				"v"=> 1
			],
			[
				"n"=> 'Н/см2',
				"v"=> 10000
			],
			[
				"n"=> 'Н/мм2',
				"v"=> 1e6
			],
		],


		"speed"=> [
			[
				"n"=> 'м/с',
				"v"=> 1
			],
			[
				"n"=> 'м/мин',
				"v"=> 1/60
			],
			[
				"n"=> 'м/ч',
				"v"=> 1/3600
			],
			[
				"n"=> 'км/с',
				"v"=> 1000
			],
			[
				"n"=> 'км/мин',
				"v"=> 16.67
			],
			[
				"n"=> 'км/ч',
				"v"=> 1 / 3.6
			],

		],


		"area"=> [
			[
				"n"=> 'м2',
				"v"=> 1
			],
			[
				"n"=> 'см2',
				"v"=> 1e-4
			],
			[
				"n"=> 'мм2',
				"v"=> 1e-6
			],
			[
				"n"=> 'км2',
				"v"=> 1e6
			],
			[
				"n"=> 'Гектар',
				"v"=> 1e4
			],
			[
				"n"=> 'Ар (сотка)',
				"v"=> 100
			],
		],


		"volume"=> [
			[
				"n"=> 'м3',
				"v"=> 1
			],
			[
				"n"=> 'см3',
				"v"=> 1e-6
			],
			[
				"n"=> 'мм3',
				"v"=> 1e-9
			],
			[
				"n"=> 'Литр (л)',
				"v"=> 1e-3
			],
			[
				"n"=> 'дл',
				"v"=> 1e-2
			],
		],


		"temperature"=> [
			[
				"n"=> 'градус Цельсия',
				"v"=> 1
			],
			[
				"n"=> 'градус Фаренгейта',
				"v"=> 5/9,
				"const"=> 32
			],
			[
				"n"=> 'градус Кельвина',
				"v"=> 1,
				"const"=> 273.15
			],
		]
	];
} //* Converter
