#!/usr/bin/php
<?php
require_once __DIR__ . "/../CommonBot.class.php";
require_once __DIR__ . "/../tg.class.php";
// trigger_error(__FILE__.' inited');

class Advert extends TG
{
	protected $cron = [];

	private $argv= [
		'anekdot' => [
			'id' => -1001393900792,
			'token' => __DIR__.'/../Anekdot_parser_bot/token.json'
		],
		'news' => [
			'id' => -1001223951491,
			'token' => __DIR__.'/../NEWs_parser_bot/token.json'
		],
		'anekdot_parser' => [
			'id' => 673976740,
			'token' => __DIR__.'/../Anekdot_parser_bot/token.json'
		],
	];

	public $advert = [

		'AliExpress' => [
			// 'uri' =>'/',
			'base' =>'https://alitems.com',
			'alt' =>'Aliexpress INT',
			'links' =>[
				'3pl9pni30ga4ec867dbe16525dc3e8',
				'nc8nd50jlda4ec867dbe16525dc3e8',
				'jh0df3sloba4ec867dbe16525dc3e8',
				'6qq5igyqyfa4ec867dbe16525dc3e8',
				'ugv2rqjiica4ec867dbe16525dc3e8',
			]
		],

		'Samsung' => [
			'uri' =>'content',
			'alt' =>'Samsung [CPS] IN',
			'links' =>[
				'7n27paepuva4ec867dbefcdd16745e',
			]
		],

		'Timeweb' => [ //== Хост
			'uri' =>'Javascripts|content',
			'alt' =>'хостинг Timeweb',
			'links' =>[
				'n6q5j342fca4ec867dbe5fb557f5d8', 'tpflk1dgaga4ec867dbe5fb557f5d8', 'jsqzs4datla4ec867dbe5fb557f5d8', 'nhp2d1t7mga4ec867dbe5fb557f5d8', 'duvxj343x9a4ec867dbe5fb557f5d8', 'o98bdksin1a4ec867dbe5fb557f5d8'
			]
		],

		'Magzter' => [
			'uri' =>'content',
			'alt' =>'сервис Magzter [CPS] IN',
			'src' =>'https://www.magzter.com/static/images/maglogo/magzlogosm.png',
			'links' =>[
				'6zlx8gln2ua4ec867dbe03fc6030ed',
			]
		],
		'Letyshops' => [ //* Дисконт
			'uri' =>'Primery|content',
			'alt' =>'кэшбэк Letyshops',
			'base' =>'https://homyanus.com',
			'links' =>[
				'nkoywaphvra4ec867dbe8753afd1f1',
				'w27poh5mmla4ec867dbe8753afd1f1',
				'koujs74zmya4ec867dbe8753afd1f1',
				'eq0fuuj113a4ec867dbe8753afd1f1',
			]
		],
		'Tea101' => [
			'uri' =>'content',
			'base' =>null,
			'alt' =>'магазин 500 видов чая',
			'links' =>[
				'ipw0vli5fua4ec867dbed55ad7d85a', '8aq5xn9ydsa4ec867dbed55ad7d85a', 'xs3x94yw7ga4ec867dbed55ad7d85a', 'n692sbotrva4ec867dbed55ad7d85a',
			],
		],

	]; //*

	public function __construct($chat=null)
	{
		if($chat) $this->cron['chat'] = $chat;
		elseif (php_sapi_name() == 'cli')
		{
			$this->cron['chat'] = $this->argv[$_SERVER['argv'][1]];
		}

		// trigger_error(__CLASS__.' inited');
		$this->botFileInfo = new SplFileInfo(__FILE__);
		$this->getTokens($this->cron['chat']['token']);
		$this->webHook = 0;

		$this->log->add(__METHOD__.' botFileInfo,$this->cron= ',null,[$this->botFileInfo,$_SERVER['argv']]);

		parent::__construct();

		$shuffle = array_values($this->advert);
		shuffle($shuffle);
		$rnd = $shuffle[0];
		shuffle($rnd['links']);

		$href = ($rnd['base'] ?? 'https://ad.admitad.com') . '/g/' . $rnd['links'][0] . '/?i=4';

		$src = $rnd['src'] ?? (($rnd['base'] ?? 'https://ad.admitad.com') . '/b/' . $rnd['links'][0] . '/');

		$rnd['alt']= $rnd['alt'] ?? 'Подробнее';
		// $txt= "<a href='$src'>&#8205;</a>\n<a href='$href'><b>{$rnd['alt']}</b></a>";
		$txt= "<a href='$src'>&#8205;</a>\n<b>{$rnd['alt']}</b>";

		$this->log->add(__METHOD__.' txt=',null,$txt);

		$this->apiRequest([
			'text' => $txt,
			'disable_web_page_preview' => false,
			'reply_markup' => [
				"inline_keyboard" => [
					[
						[
							'text' => $rnd['alt'],
							'url' => $href
						],
					],
			],]
		]);
	}
} //* Advert


// $_SERVER['argv'][1]

new Advert;
/* new Advert([
	'id' => -1001393900792,
	'token' => __DIR__.'/../Anekdot_parser_bot/token.json'
]); */