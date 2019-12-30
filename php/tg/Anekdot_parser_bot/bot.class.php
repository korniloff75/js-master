<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

# TG
require_once __DIR__ . "/../tg.class.php";


class AnekdotBot extends TG implements iTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '1052237188:AAESfJXhbZLxBiZTb7m0CDy-3ZkGgoO9YrU',
		# Время обновления базы, s
		$addTime = 12 * 3600,
		$baseDir = 'base/',
		$lastBase = [],
		$base = [],
		$DOMNodeList,
		$content = [],
		$botDir,
		$id = [
			'anekdoty' => -1001393900792,
			 'tome' => 673976740,
		],
		$lastBaseItem,
		$respTG=[];

	public static
		# Input stream
		$json,
		// $contentMD,
		$postFields,
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
			'https://anekdot.ru/',
			'http://anekdotov.net/',
		];



	public function __construct()
	{
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);
		// $this->botDir = \Path::fromRootStat(__DIR__);
		require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";
		$this->log = new Logger('tg.log', $this->botFileInfo->getPathInfo()->getRealPath());

		parent::__construct()->init();

	} //__construct

	/**
	 *
	 */
	public function init()
	{
		if(!is_dir($this->baseDir))
			mkdir($this->baseDir);
		else
		{
			# Сканируем базу в массив из json-файлов
			foreach ((new DirectoryIterator($this->baseDir)) as $fileinfo) {
				// echo "{$fileinfo->getFilename()}\n";
				if($fileinfo->getExtension() === 'json')
					$this->base[$fileinfo->getMTime()] = $fileinfo->getFilename();
			}
			# Дата создания - по убыванию
			krsort($this->base);
			$this->base = array_values($this->base);

			$this->log->add('$this->base', null, [$this->base]);
		}


		# Имена файлов по убыванию - SCANDIR_SORT_DESCENDING
		// $this->base = scandir($this->baseDir);
		// natsort($this->base);

		// $this->base = array_reverse($this->base);

		/* $this->base = array_filter($this->base, function($i) use ($this->baseDir) {
			return pathinfo($this->baseDir . $i, PATHINFO_EXTENSION) === 'json';
		});
		$this->base = array_values($this->base); */


		# Получаем содержимое последнего файла в массив
		if(!empty($this->base[0]))
		{
			$this->lastBaseItem = $this->baseDir . $this->base[0];
			$this->content = $this->lastBase = \H::json($this->lastBaseItem);

		}

		$this->log->add('$lastBaseItem = ' . $this->lastBaseItem);

		# Парсим сайт из self::$remoteSourse
		$this->parser(0);

		$this->log->add("count(\$this->content) = " . count($this->content));

		die('OK');

	} // init


	/**
	 * @ind - индекс в массиве self::$remoteSourse
	 */
	public function parser(int $ind)

	{
		$doc = new DOMDocument();
		@$doc->loadHTMLFile(self::$remoteSourse[$ind]);

		// $docEl = $doc->documentElement;

		# Подключаем частный парсер
		// план - перенести частные в дочерний класс
		$parserName = "parser$ind";

		# use custom parser
		if(method_exists(get_class($this), $parserName))
		{
			# rewrite $this->content
			$this->content = [];
			$this->{$parserName}($doc);
		}
		else return;

		/* $scripts = $doc->getElementsByTagName("script");
		var_dump($scripts);

		for ($i = 0; $i < $scripts->length; $i++){
			$docEl->removeChild($scripts->item($i));
		} */

		$this->log->add("count(\$this->content) = " . count($this->content));

		# Ислючаем дубли
		if(count($diff = array_diff($this->content, $this->lastBase)))
		{
			// Пишем файл без редакции
			\H::json($this->baseDir . basename(self::$remoteSourse[$ind]) . '.json', $this->content);

			# Чистим для MD
			$diff = array_filter($diff, function($i) {
				return strpos($i, 'читать дальше', -30) === false;
			});

			$this->log->add("\$diff = " . json_encode($diff, JSON_UNESCAPED_UNICODE));

			shuffle($diff);

			$diff = str_replace(["\r", '\r', '_', '*', '=', ], ["\n", "\n", ' ', ''], $diff);

			# создаём .md
			file_put_contents($this->baseDir . time() . basename(self::$remoteSourse[$ind]) . '.md', array_map(function($i) {
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
				}

				# Отправляем в канал.
				$this->requestTG($bus);

				$bus = '';
				usleep(500);

			}

			# Test server response
			$this->log->add("\$this->respTG", null, [$this->respTG]);


		}

		# If not exist new content
		elseif ($data = $this->getCallback('callback_query'))
		{
			return $this->apiRequest([
			'callback_query_id' => $this->inputData['callback_query']['id'],
			'text' => self::$noUdates,
			], 'answerCallbackQuery');

		}


	} // parser


	public function requestTG($bus)
	{
		# Отправляем в канал.
		if(!$this->chat_id)
		{
			return $this->log->add("\$this->chat_id =", E_USER_WARNING, $this->chat_id);
		}

		return $this->respTG[]= $this->apiRequest([
			'chat_id' => $this->chat_id, // anekdoty | tome
			// 'chat_id' => $this->__test ? $this->id['tome'] : $this->id['anekdoty'], // anekdoty | tome
			'parse_mode' => 'markdown',
			'text' => $bus,
			'reply_markup' => $this->setInlineKeyboard([[
				# Row
				$this->setAdvButton(),
				[
					"text" => "Хочу ещё",
					"callback_data" => '/more',
				],
			]]),
		]);
	}

	public function parser0($doc)

	{
		$this->DOMNodeList = $doc->getElementsByTagName("div");

		$this->log->add("\$DOMNodeList", null, [$this->DOMNodeList]);

		if(!$this->DOMNodeList->length) return;

		foreach($this->DOMNodeList as $t) {
			$class = $t->attributes->getNamedItem('class');

			// if(!is_object($class) || $class->nodeValue !== 'text') {
				// continue;
			if(is_object($class) && $class->nodeValue === 'text') {
				$this->content[]= $t->textContent;
			}
		}
	} // parser0


	public function setAdvButton()
	{
		# Advert
		$text = array_keys(self::$adv);
		shuffle($text);

		return [
			"text" => $text[0],
			"url" => self::$adv[$text[0]],
		];
	}

	// Делаем АШИПКУ

	/* public function sendTG()
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

		self::$postFields = [
			'chat_id' => $chat_id,
			'text' => $this->content[0],
		];

		$this->apiRequest(self::$postFields);
	} */

	public function __destruct()
	{
		?>
		<meta charset="UTF-8">
		<style>
		pre {
			box-sizing: border-box;
			white-space: pre-wrap;
			border: inset 1px #eee;
		}
		</style>
		<?php
	}

}

new AnekdotBot;