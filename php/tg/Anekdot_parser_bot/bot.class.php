<style>
.log {
  overflow-wrap: normal;
  word-wrap: break-word;
  word-break: keep-all;
  line-break: auto;
	hyphens: manual;
	border: inset 1px #eee;
}
</style>

<?php
define("TG_TEST", 1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";
require_once \HOME . "php/Path.php";
define("BOT_DIR", Path::fromRootStat(__DIR__));
# TG
require_once __DIR__ . "/../tg.class.php";


class AnekdotBot extends TG
{
	const
		TOKEN = '1052237188:AAFIh-yeUO05Qv--LfAGaJnFmo8vvT9jDjY';

	public static
		# Input stream
		$json,
		$base,
		$content = [],
		// $contentMD,
		$postFields,
		$DOMNodeList;

	protected static
		$remoteSourse = [
			'https://www.anekdot.ru/',
			'https://www.anekdot.ru/best/anekdot/1214/',
		],
		# Имя текущего файла для парсера
		$currentName = '',
		$lastBaseItem,
		$inputData = [],
		$lastBase = [],

		# TMP
		$t = [],
		$curHash,
		$lastHash;


	// private static

	protected
		# Время обновления базы, s
		$addTime = 12 * 3600,
		$id = [
			'anekdoty' => -1001393900792,
			 'tome' => 673976740,
		];

	public function __construct()
	{
		parent::__construct(self::TOKEN);

		# Однократно запускаем webHook
		if(!file_exists("registered.trigger"))
			$this->webHook();
		else
		{
			echo "<pre>Webhook уже зарегистрирован\n";
		}

		$this->init();

		self::log(['echo "END of __construct"'], null, __LINE__);

	} //__construct

	/**
	 * Singl
	 */
	protected function webHook()
	{
		/* $url = "https://api.telegram.org/bot".TOKEN."/setWebhook?url=https://js-master.ru/" . BOT_DIR . "/bot.php";
		$l1=file_get_contents($url);
		$response = json_decode($l1); */

		$response = $this->request([
			'url' => \BASE_URL . BOT_DIR . "/bot.php"
		], 'setWebhook');
		$response = json_decode($response);
		print_r(
			[$response, ]
		);
		if($response)
			file_put_contents("registered.trigger",time());

		self::log(['echo "END of webHook"'], __FILE__, __LINE__);

	}

	/**
	 *
	 */
	public function init()
	{
		$key = 'channel_post';
		$baseDir = 'base/';

		# Ловим входящий поток
		$json = file_get_contents('php://input');
		# Кодируем в массив
		self::$inputData = json_decode($json, TRUE)[$key] ?? [];

		if(!is_dir($baseDir)) mkdir($baseDir);

		self::$currentName = $baseDir . time();
		// self::$currentName = $baseDir . '/' . time();

		# Сканируем базу в массив, убираем точки
		# Имена файлов по убыванию - SCANDIR_SORT_DESCENDING
		self::$base = scandir($baseDir, SCANDIR_SORT_DESCENDING);

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

			# Hash of the last file
			// self::$lastHash = md5(implode('\n', self::$content));
		}


		self::log([
			'echo "\$lastHash =" ',
			'var_dump(static::$lastHash)',
			'echo "\$json =" ',
			'var_dump(static::$json)',
			'echo "static::\$content =" ',
			'var_dump(static::$content)',
			'echo "static::\$lastBaseItem =" ',
			'var_dump(static::$lastBaseItem)',
		], __FILE__, __LINE__);

		# Парсим с интервалом $this->addTime
		/* if(
			1 ||
			!count(self::$content)
			|| time() - filemtime(self::$lastBaseItem) > $this->addTime
		) */
		$this->parser(0);


		/* if (array_key_exists('message', self::$inputData)) {
		$chat_id = self::$inputData['message']['chat']['id'];
		$message = self::$inputData['message']['text'];

		} elseif (array_key_exists('callback_query', self::$inputData)) {
				$chat_id = self::$inputData['callback_query']['message']['chat']['id'];
				$message = self::$inputData['callback_query']['data'];
		} */

		if (isset(self::$inputData['message_id']))
			$this->sendTG();
		else self::log(['echo "Нет message!\n"',], __FILE__, __LINE__);

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
			'echo "\$lastHash = " . static::$lastHash',
			'echo "\$curHash = " . static::$curHash',
			'echo "count(static::\$content) = " . count(static::$content)',
			'echo "self::\$lastBase = "',
			'var_dump(static::$lastBase)',
		], __FILE__, __LINE__);

		// if(count(self::$content) && self::$curHash !== self::$lastHash)
		# Ислючаем дубли
		if(count($diff = array_diff(self::$content, self::$lastBase)))
		// if(count(self::$content = array_diff(self::$content, self::$lastBase)))
		{
			\H::json(self::$currentName . '.json', self::$content);

			# Чистим для MD
			$diff = array_filter($diff, function($i) {
				return strpos($i, 'читать дальше', -30) === false;
			});

			shuffle($diff);

			$diff = str_replace(["\r", '\r', '_', '*', '=', ], ["\n", "\n", ' ', ''], $diff);

			# создаём .md
			file_put_contents(self::$currentName . '.md', array_map(function($i) {
				return $i . "\n\n---\n";
			}, $diff));

			/* $diff = array_chunk($diff, 10);
			$diff = array_map($diff, 10); */


			# Отсылаем пост
			$bus = '';
			$arrayLength = count($diff);

			foreach($diff as $k => $i) {
				--$arrayLength;
				# Разбиваем на строки фикс. размера
				if(strlen($bus) + strlen($i) < self::$textLimit)
				{
					$bus .= "$i\n\n\n";
					if($arrayLength) continue;
				}

				# Отправляем в канал.
				self::$t[]= $this->request([
					'chat_id' => $this->id['tome'], // anekdoty | tome
					'parse_mode' => 'markdown',
					'text' => $bus,
					'reply_markup' => $this->getInlineKeyboard([[
						[
							"text" => "Хочу ещё",
							"callback_data" => 'myText',
							// "callback_data" => json_encode(["data" => "more"], JSON_UNESCAPED_UNICODE),
						],
						[
							"text" => "Хочу",
							"callback_data" => 'myText',
						],
					]]),
				]);
				$bus = '';
				sleep(.5);

			}


			# Test server response
			self::log(['echo "static::\$t = "', 'var_dump(static::$t )'], __FILE__, __LINE__);

		}

	} // parser


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


	/* public function autoPost()
	{
		self::$postFields = [
			'chat_id' => $chat_id,
			'text' => self::$content[0],
		];

		self::log(['echo "static::\$postFields = "', 'var_dump(static::$postFields)'], __FILE__, __LINE__);

		$this->request(self::$postFields);
	} */


	public function sendTG()
	{
		$first_name="";
		$last_name="";
		$username="";

		$chat_id=self::$inputData['chat']['id'];

		if (self::$inputData['chat']['type']=="private" || false)
		{
			if (isset(self::$inputData['chat']['first_name']))
				$first_name=self::$inputData['chat']['first_name'];
			if (isset(self::$inputData['chat']['last_name']))
				$last_name=self::$inputData['chat']['last_name'];
			if (isset(self::$inputData['chat']['username']))
				$username=self::$inputData['chat']['username'];

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