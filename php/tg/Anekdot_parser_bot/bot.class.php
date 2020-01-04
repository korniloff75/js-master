<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

require_once "../CommonBot.class.php";


class AnekdotBot extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '1052237188:AAESfJXhbZLxBiZTb7m0CDy-3ZkGgoO9YrU',
		$baseDir = 'base/',
		$lastBase = [],
		$baseId = [],
		$baseSource = [],
		$DOMNodeList,
		// $contentSum = [],
		$content = [],
		# Счётчик обновлений
		$countDiff = 0,
		$botDir,
		$id = [
			'anekdoty' => -1001393900792,
			 'tome' => 673976740,
		],
		$currentBaseItem,
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
		];

	protected static
		$remoteSource = [
			'https://anekdot.ru/',
			'http://anekdotov.net/',
		];



	public function __construct()
	{
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);
		// $this->botDir = \Path::fromRootStat(__DIR__);

		# Запускаем скрипт
		parent::__construct()->checkLicense()->init();

	} //__construct

	/**
	 *
	 */
	public function init()
	{
		# Collect $this->baseSource
		$this->baseSource = $this->CollectBaseArray(__DIR__ . "/{$this->baseDir}");

		# Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->parser();

		$this->log->add("count(\$this->content) = " . count($this->content));

		die('OK');

	} // init


	/**
	 * @ind - индекс в массиве self::$remoteSource
	 */
	public function parser()

	{
		foreach (self::$remoteSource as $source) {
			$bSource = basename($source);
			$base = $this->baseSource[$bSource] ?? [];

		// foreach ($this->baseSource as $source => $base) {
			# Получаем файл для текущего chat_id
			if(isset($base[$this->chat_id]))
			{
				$currentItem = $this->baseDir . $base[$this->chat_id];
				$this->log->add('$currentItem = ' . $currentItem);
				$this->content = $this->lastBase = \H::json($currentItem);

			}

			$s = str_replace('.', '_', $bSource);
			$parserName = "parser_$s";

			# use custom parser if EXIST =====
			if(!method_exists(get_class($this), $parserName)) continue;

			$this->log->add("\$bSource, \$base = ", null, [$bSource, $base]);

			# Парсим сайт из self::$remoteSource
			$doc = new DOMDocument();
			@$doc->loadHTMLFile($source);

			# Обнуляем контент
			$this->content = [];

			# Подключаем локальный парсер
			// план - перенести частные в дочерний класс
			$this->{$parserName}($doc)->_findUnical($bSource);

			$this->log->add("count(\$this->content) - $source = " . count($this->content));
		}

		# If not exist new content
		if (!$this->countDiff && array_key_exists('callback_query', $this->inputData))
		{
			$r = $this->apiResponseJSON([
			// return $this->apiRequest([
			'callback_query_id' => $this->inputData['callback_query']['id'],
			'text' => $this->noUdatesText,
			], 'answerCallbackQuery');

			$this->log->add("NOT exist new content.", null, $r);
			return $r;

		}

		/* $scripts = $doc->getElementsByTagName("script");
		var_dump($scripts);

		for ($i = 0; $i < $scripts->length; $i++){
			$docEl->removeChild($scripts->item($i));
		} */

	} // parser


	/**
	 * Получаем неопубликованный контент
	 */
	private function _findUnical($bSource)
	{
		# $this->content предварительно пропущен через локальный парсер
		if(count($diff = array_diff($this->content, $this->lastBase)))
		{
			# Пишем файл без редакции
			\H::json($this->baseDir . "{$this->chat_id}.$bSource.json", $this->content);


			# Чистим для MD
			$diff = array_filter($diff, function($i) {
				return strpos($i, 'читать дальше', -30) === false;
			});

			// $this->log->add("\$diff = " . json_encode($diff, JSON_UNESCAPED_UNICODE));
			$this->log->add("\$this->countDiff = " . ++$this->countDiff);

			shuffle($diff);

			$diff = str_replace(["\r", '\r', '_', '*', '=', ], ["\n", "\n", ' ', ''], $diff);

			# создаём .md
			/* file_put_contents($this->baseDir . time() . basename(self::$remoteSource[$ind]) . '.md', array_map(function($i) {
				return $i . "\n\n---\n";
			}, $diff)); */

			$this->sendMessage($diff);

		}

	} // _findUnical


	protected function parser_anekdot_ru($doc)

	{
		$this->DOMNodeList = $doc->getElementsByTagName("div");

		$this->log->add(__METHOD__ . " - \$DOMNodeList", null, [$this->DOMNodeList]);

		if(!$this->DOMNodeList->length) return;

		foreach($this->DOMNodeList as $t) {
			$class = $t->attributes->getNamedItem('class');

			if(is_object($class) && $class->nodeValue === 'text') {
				$this->content[]= $t->textContent;
			}
		}

		# Required
		return $this;
	} // parser_anekdot_ru

	protected function parser_anekdotov_net($doc)

	{
		$this->DOMNodeList = $doc->getElementsByTagName("div");

		$this->log->add(__METHOD__ . " - \$DOMNodeList", null, [$this->DOMNodeList]);

		if(!$this->DOMNodeList->length) return;

		foreach($this->DOMNodeList as $t) {
			$class = $t->attributes->getNamedItem('align');

			if(is_object($class) && $class->nodeValue === 'justify') {
				if(strpos($t->textContent, 'а н е к д о т о в . n е t') !== false) continue;
				$this->content[]= $t->textContent;
			}
		}

		# Required
		return $this;
	} // parser_anekdotov_net


	// Делаем АШИПКУ

	public function __destruct()
	{

	}

}

new AnekdotBot;