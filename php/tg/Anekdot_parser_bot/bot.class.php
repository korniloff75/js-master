<?php
// define("TG_TEST", 1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";
require_once \HOME . "php/Path.php";
define("BOT_DIR", Path::fromRootStat(__DIR__));
# TG
require_once __DIR__ . "/../tg.class.php";


class AnekdotBot extends TG implements iTG 
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '1052237188:AAFIh-yeUO05Qv--LfAGaJnFmo8vvT9jDjY',
		# Время обновления базы, s
		$addTime = 12 * 3600,
		$id = [
			'anekdoty' => -1001393900792,
			 'tome' => 673976740,
		];

	public static
		# Input stream
		$json,
		$base,
		$content = [],
		// $contentMD,
		$postFields,
		$DOMNodeList,
		$adv = [
			'Зарабатывай без вложений' => 'https://t.me/CapitalistGameBot?start=673976740',
			'Учись инвестировать играя' => 'https://t.me/CapitalistGameBot?start=673976740',
			'Заказать быстрый сайт' => 'https://js-master.ru/content/1000.Contacts/Zakazchiku/',
			'Сайт на AJAX с поддержкой SEO!' => 'https://js-master.ru/content/1000.Contacts/Zakazchiku/',
			'Дешевый хостинг' => 'https://invs.ru?utm_source=partner&ref=ueQYF',
			'Хостинг от 49р' => 'https://invs.ru?utm_source=partner&ref=ueQYF',
		],
		$noUdates = "Обновлений пока нет. Попробуйте позже.";

	protected static
		$remoteSourse = [
			'https://www.anekdot.ru/',
			'https://www.anekdot.ru/best/anekdot/1214/',
		],
		# param 4 request
		$url,
		# Имя текущего файла для парсера
		$currentName = '',
		$lastBaseItem,
		$lastBase = [],

		# TMP
		$responseSetWebhook,
		$t = [];


	// private static

	

	public function __construct()
	{
		self::$url = \BASE_URL . BOT_DIR . '/' . basename(__FILE__);

		parent::__construct()->webHook()->init();

		self::log([
			'echo "END of __construct"'
		], __FILE__, __LINE__);

	} //__construct


	/**
	 * Singl
	 */
	protected function webHook()
	{
		# Однократно запускаем webHook
		if(file_exists("webHookRegistered.trigger"))
		{
			echo "<pre>Webhook уже зарегистрирован\n";
		}
		else
		{
			self::$responseSetWebhook = $this->request([
				'url' => self::$url,
				'parse_mode' => null,
			], 'setWebhook') ?? [];
	
			self::log([
				'echo "url = " . static::$url',
				'echo "response after setWebhook = "',
				'var_dump(static::$responseSetWebhook)',
			], __FILE__, __LINE__);
	
	
			if(count(self::$responseSetWebhook))
				file_put_contents("webHookRegistered.trigger", json_encode(self::$responseSetWebhook, JSON_UNESCAPED_UNICODE));
		}
		
		self::log(['echo "END of webHook"'], __FILE__, __LINE__);
		return $this;
	}

	/**
	 *
	 */
	public function init()
	{
		$baseDir = 'base/';

		if(!is_dir($baseDir)) mkdir($baseDir);

		self::$currentName = $baseDir . time();

		# Сканируем базу в массив из json-файлов
		# Имена файлов по убыванию - SCANDIR_SORT_DESCENDING
		self::$base = scandir($baseDir);
		natsort(self::$base);
		self::$base = array_reverse(self::$base);

		self::$base = array_filter(self::$base, function($i) use ($baseDir) {
			return pathinfo($baseDir . $i, PATHINFO_EXTENSION) === 'json';
		});
		self::$base = array_values(self::$base);

		self::log(['echo "self::\$base = "','var_dump(static::$base)'], __FILE__, __LINE__);

		# Получаем содержимое последнего файла в массив
		if(!empty(self::$base[0]))
		{
			self::$lastBaseItem = $baseDir . self::$base[0];
			self::$content = self::$lastBase = \H::json(self::$lastBaseItem);

		}


		self::log([
			'echo "static::\$content =" ',
			'var_dump(static::$content)',
			'echo "static::\$lastBaseItem =" ',
			'var_dump(static::$lastBaseItem)',
		], __FILE__, __LINE__);

		# Парсим сайт из self::$remoteSourse
		$this->parser(0);


		self::log(['echo "count(static::\$content) = " . count(static::$content)',], __FILE__, __LINE__);

		file_put_contents('log.txt', TG::$log);

		die('OK');

	} // init


	/**
	 * @ind - индекс в массиве self::$remoteSourse
	 */
	public function parser(int $ind)

	{
		self::log(['echo "static::\$content"', 'var_dump(static::$content)'], __FILE__, __LINE__);

		$doc = new DOMDocument();
		@$doc->loadHTMLFile(self::$remoteSourse[$ind]);
		// $docEl = $doc->documentElement;

		# Подключаем частный парсер
		// план - перенести частные в дочерний класс
		$parserName = "parser$ind";
		if(method_exists(get_class($this), $parserName))
		{
			# rewrite self::$content
			self::$content = [];
			self::{$parserName}($doc);
		}
		else return;

		/* $scripts = $doc->getElementsByTagName("script");
		var_dump($scripts);

		for ($i = 0; $i < $scripts->length; $i++){
			$docEl->removeChild($scripts->item($i));
		} */

		self::log([
			'echo "count(static::\$content) = " . count(static::$content)',
			'echo "self::\$lastBase = "',
			'var_dump(static::$lastBase)',
		], __FILE__, __LINE__);

		# Ислючаем дубли
		if(count($diff = array_diff(self::$content, self::$lastBase)))
		{
			// Пишем файл без редакции
			\H::json(self::$currentName . '.json', self::$content);

			# Чистим для MD
			$diff = array_filter($diff, function($i) {
				return strpos($i, 'читать дальше', -30) === false;
			});
			self::$log[]= '$diff = ';
			self::$log[]= $diff;

			shuffle($diff);

			$diff = str_replace(["\r", '\r', '_', '*', '=', ], ["\n", "\n", ' ', ''], $diff);

			# создаём .md
			file_put_contents(self::$currentName . '.md', array_map(function($i) {
				return $i . "\n\n---\n";
			}, $diff));

			$bus = '';
			$diffLength = count($diff);

			foreach($diff as $i) {
				--$diffLength;
				# Разбиваем на строки фикс. размера
				if(strlen($bus) + strlen($i) < self::$textLimit)
				{
					$bus .= "$i\n\n\n";
					if($diffLength) continue;
					// elseif(!strlen(trim($bus))) $bus= ;
				}

				# Отправляем в канал.
				$this->requestTG($bus);

				$bus = '';
				usleep(500);

			}

			# Test server response
			self::log(['echo "static::\$t = "', 'var_dump(static::$t )'], __FILE__, __LINE__);

		}

		# If not exist new content
		elseif ($data = $this->getCallback('callback_query'))		
		{
			return $this->request([
			'callback_query_id' => $data['id'],
			'text' => self::$noUdates,
			], 'answerCallbackQuery');

			// else $this->requestTG(self::$noUdates);
		}
	

	} // parser

	
	public function requestTG($bus)
	{
		# Отправляем в канал.
		return self::$t[]= $this->request([
			'chat_id' => $this->chat_id, // anekdoty | tome
			// 'chat_id' => $this->__test ? $this->id['tome'] : $this->id['anekdoty'], // anekdoty | tome
			'parse_mode' => 'markdown',
			'text' => $bus,
			'reply_markup' => $this->getInlineKeyboard([[
				# Row
				$this->getAdvButton(),
				[
					"text" => "Хочу ещё",
					"callback_data" => '/more',
				],
			]]),
		]);
	}

	public static function parser0($doc)

	{
		self::$DOMNodeList = $doc->getElementsByTagName("div");
		self::log(['echo "\static::\$DOMNodeList = "', 'var_dump(static::$DOMNodeList )'], __FILE__, __LINE__);

		if(!self::$DOMNodeList->length) return;

		foreach(self::$DOMNodeList as $t) {
			$class = $t->attributes->getNamedItem('class');

			// if(!is_object($class) || $class->nodeValue !== 'text') {
				// continue;
			if(is_object($class) && $class->nodeValue === 'text') {
				echo $t->textContent;
				self::$content[]= $t->textContent;
				echo "\n***********\n";
			}
		}
	} // parser0


	public function getAdvButton()
	{
		# Advert
		$text = array_keys(self::$adv);
		shuffle($text);

		return [
			"text" => $text[0],
			"url" => self::$adv[$text[0]],
		];
	}


	public function sendTG()
	{
		$first_name="";
		$last_name="";
		$username="";

		$chat_id=$this->inputData['chat']['id'];

		if ($this->inputData['chat']['type']=="private" || false)
		{
			if (isset($this->inputData['chat']['first_name']))
				$first_name=$this->inputData['chat']['first_name'];
			if (isset($this->inputData['chat']['last_name']))
				$last_name=$this->inputData['chat']['last_name'];
			if (isset($this->inputData['chat']['username']))
				$username=$this->inputData['chat']['username'];

			if ($first_name!="" AND $last_name!="")
				$text="<b>Здравствуйте, $first_name $last_name!</b>";
		} //private chat


		/* $text = iconv ( 'windows-1251' , 'utf-8' , $text );
		print_r($text);
		$text=strip_tags($text, '<b><strong><pre>'); */

		self::$postFields = [
			'chat_id' => $chat_id,
			'text' => self::$content[0],
			/* 'reply_markup' => $this->getKeyBoard(
				[["text" => "Голосовать"], ["text" => "Помощь"
				]
			) */
		];

		self::log(['echo "static::\$postFields = "', 'var_dump(static::$postFields)'], __FILE__, __LINE__);

		$this->request(self::$postFields);
	}

}

new AnekdotBot;