#!/usr/bin/php
<?php
require_once __DIR__ . "/../CommonBot.class.php";
require_once __DIR__ . "/../tg.class.php";
// trigger_error(__FILE__.' inited');

class Advert extends TG
{
	protected $cron = [];

	public function __construct($chat=null)
	{
		// trigger_error(__CLASS__.' inited');
		$this->botFileInfo = new SplFileInfo(__FILE__);
		$this->webHook = 0;

		$this->urlDIR = 'https://js-master.ru/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);

		if($chat)
		{
			$this->addChat($chat);
		}
		elseif (php_sapi_name() == 'cli')
		{
			$this->addChat($_SERVER['argv'][1]);
		}

		$this->log->add(__METHOD__.' botFileInfo,$this->cron= ',null,[$this->botFileInfo,$_SERVER['argv']]);

	}

	private function getAdvert()
	{
		$shuffle = array_values($this->advert);
		shuffle($shuffle);
		$rnd = $shuffle[0];
		if(!empty($rnd['links'])) shuffle($rnd['links']);

		$href = $rnd['href'] ?? (($rnd['base'] ?? 'https://ad.admitad.com') . '/g/' . $rnd['links'][0] . '/?i=4');

		if(!empty($rnd['src']))
			$src = strpos($rnd['src'],'http') === 0 ? $rnd['src'] : ($this->urlDIR . $rnd['src']);
		else
			$src = ($rnd['base'] ?? 'https://ad.admitad.com') . '/b/' . $rnd['links'][0] . '/';

		$rnd['alt']= $rnd['alt'] ?? 'Подробнее';
		// $txt= "<a href='$src'>&#8205;</a>\n<a href='$href'><b>{$rnd['alt']}</b></a>";
		$txt= "<a href='$src'>&#8205;</a>\n<b>{$rnd['alt']}</b>";

		$this->log->add(__METHOD__.' txt=',null,$txt);

		$this->apiRequest([
			'text' => $txt,
			'chat_id' => $this->cron['chat']['id'],
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

	public function addChat($chat)
	{
		if(!empty($this->argv[$chat])) $this->cron['chat'] = $this->argv[$chat];
		else return;

		$this->getTokens($this->cron['chat']['token']);

		parent::__construct();

		$this->getAdvert();

	}

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
			'alt' =>'Samsung [CPS] IN',
			'links' =>[
				'7n27paepuva4ec867dbefcdd16745e',
			]
		],

		'Timeweb' => [ //== Хост
			'alt' =>'хостинг Timeweb',
			'links' =>[
				'n6q5j342fca4ec867dbe5fb557f5d8', 'tpflk1dgaga4ec867dbe5fb557f5d8', 'jsqzs4datla4ec867dbe5fb557f5d8', 'nhp2d1t7mga4ec867dbe5fb557f5d8', 'duvxj343x9a4ec867dbe5fb557f5d8', 'o98bdksin1a4ec867dbe5fb557f5d8'
			]
		],

		'Magzter' => [
			'alt' =>'сервис Magzter [CPS] IN',
			'src' =>'https://www.magzter.com/static/images/maglogo/magzlogosm.png',
			'links' =>[
				'6zlx8gln2ua4ec867dbe03fc6030ed',
			]
		],
		'Letyshops' => [ //* Дисконт
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
			'base' =>null,
			'alt' =>'магазин 500 видов чая',
			'links' =>[
				'ipw0vli5fua4ec867dbed55ad7d85a', '8aq5xn9ydsa4ec867dbed55ad7d85a', 'xs3x94yw7ga4ec867dbed55ad7d85a', 'n692sbotrva4ec867dbed55ad7d85a',
			],
		],
		'cap_my_1'=> [
			'alt'=> "Учись инвестировать играя",
			'src'=> '/assets/Cap_300.jpg',
			'href'=>"https://t.me/CapitalistGameBot?start=673976740"
		],
		'invs'=> [
			'alt'=> "Дешевый хостинг",
			'src'=> 'https://lk.invs.ru/partner/file/7',
			'href'=>"https://invs.ru?utm_source=partner&ref=ueQYF"
		],
		'js-master'=> [
			'alt'=> "Заказать быстрый сайт",
			'src'=> '/assets/Js_ajax_700.jpg',
			'href'=>"https://js-master.ru/content/1000.Contacts/Zakazchiku/"
		],

	];
} //* Advert


// new Advert;
$adv = new Advert('anekdot');
$adv->addChat('news');
