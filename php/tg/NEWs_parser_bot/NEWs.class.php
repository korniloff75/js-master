<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

require_once "../CommonBot.class.php";

class KorniloFF_news extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '943870959:AAFdJgZhROOVsevhaXEUYxGr-BL-yorNueY',

		$baseDir = 'base/',
		$savedContent = [],
		$savedBase = [],
		$baseId = [],
		$baseSource = [],
		$DOMNodeList,
		// $contentSum = [],
		// $content = [],
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
		$postFields;

	protected static
		$remoteSource = [
			'https://crimea-news.com/',
			'http://m.allcrimea.net/',
		];



	public function __construct()
	{
		// \H::profile('base');
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		# Protect from CommonBot
		parent::__construct()->checkLicense()->init();

	} //__construct

	/**
	 *
	 */
	public function init()
	{
		# Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->Parser();

		die('OK');

	} // init


	protected function parser_crimea_news_com($source, $doc)
	:array
	{
		$links = [];
		$xpath = new DOMXpath($doc);

		$xpathToNode = "//div[@class=\"top-day\"][1]//a";

		# Собираем ссылки с гл. страницы
		if(!is_object($mainLinks = $xpath->query($xpathToNode)))
			return [];

		// $mainLinks = $doc->getElementById("container")->getElementsByTagName("a");

		// if(!$mainLinks->length) return $this;

		# array_unique($mainLinks)
		foreach($mainLinks as $link) {
			$href = $link->getAttribute("href");
			if(
				strlen($href)
				&& strpos($href, 'http') === false
				// && strcasecmp($href, $source)
			)
			{
				$links []= $source . substr($href, 1);
			}

			$this->log->add("href = $href", null, $href);
		}

		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			// $doc,
			$mainLinks, $links
		]);

		# Required $this->definedBase in CommonBot
		return array_unique($links);
	} // parser_crimea_news_com


	protected function parser_m_allcrimea_net($source, $doc)
	:array
	{
		# Собираем ссылки с гл. страницы
		$mainLinks = $doc->getElementById("container")->getElementsByTagName("a");
		$links = [];

		$this->log->add(__METHOD__ . " - \$mainLinks", null, [
			// $doc,
			$mainLinks
		]);

		// if(!$mainLinks->length) return $this;

		# array_unique($mainLinks)
		foreach($mainLinks as $link) {
			$href = $link->getAttribute("href");
			if(
				strlen($href)
				&& strpos($href, 'http') !== 0
				&& strcasecmp($href, $source)
			)
				$links []= $source . $href;
		}

		# Required $this->definedBase in CommonBot
		return array_unique($links);
	} // parser_m_allcrimea_net


	/**
	 * Обрабатываем неопубликованный контент
	 */
	protected function handler_crimea_news_com(array $diff)
	{
		$photos = [];
		$content = [];
		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$docLink = new DOMDocument();
			@$docLink->loadHTMLFile($link);
			$xpath = new DOMXpath($docLink);

			$xpathToNode = "//*[@class=\"news_c\"][1]";

			if(!is_object($xnews = $xpath->query($xpathToNode)))
				continue;

			# Собираем для добавления в $content
			$addContent = $docLink->getElementsByTagName("h1")->item(0)->textContent;

			// $this->log->add('$imgs', null, [$news->getElementsByTagName("img")->item(0)]);

			if(
				is_object($img = $xpath->query($xpathToNode . "//img")->item(0))
				&& strlen($src = $img->getAttribute('src'))
			)
			{
				$photos[]= [
					'type' => 'photo',
					'media' => $src,
					'caption' => $addContent,
				];
			}

			$addContent = "<b>$addContent</b>" . PHP_EOL . PHP_EOL;

			// $addContent .= $xnews->item(0)->textContent;
			$addContent .= $this->DOMinnerHTML(
				$xnews->item(0)
			);

			$content[]= $addContent;

		}

		$this->log->add('count($photos) = ' . count($photos));

		# На отсылку
		return [
			'sendMediaGroup' => $photos,
			'sendMessage' => $content,
		];

	} // handler_crimea_news_com


	protected function handler_m_allcrimea_net(array $diff)
	{
		$photos = [];
		$content = [];
		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$docLink = new DOMDocument();
			@$docLink->loadHTMLFile($link);
			$xpath = new DOMXpath($docLink);

			$xpathToNode = "//*[@id=\"newscont\"]";

			if(!is_object($xnews = $xpath->query($xpathToNode)->item(0)))
				continue;

			# Собираем для добавления в $content
			$addContent = $docLink->getElementsByTagName("h1")->item(0)->textContent;

			// $this->log->add('$imgs', null, [$news->getElementsByTagName("img")->item(0)]);

			if(
				is_object($img = $xpath->query($xpathToNode . "//img")->item(0))
				&& strlen($src = $img->getAttribute('src'))
			)
			{
				$photos[]= [
					'type' => 'photo',
					'media' => $src,
					'caption' => $addContent,
				];
			}

			$addContent = "<b>$addContent</b>" . PHP_EOL . PHP_EOL;

			$pars = $xpath->query($xpathToNode . '//p[@class="txtnews"]');

			foreach($pars as $p) {
				if($p->getElementsByTagName('a')->length)
					continue;

				# Filter
				$filter = str_ireplace(['Полная версия сайта', 'Обратная связь', 'Политика конфидициальности', 'Отказ от ответственности',], '', $p->nodeValue);
				// $this->log->add('$p = ', null, [$p]);
				if(strlen($filter))
					$addContent .= "{$filter}\n";
			}

			$content[]= $addContent;

		}

		$this->log->add('count($photos) = ' . count($photos));

		# На отсылку
		return [
			'sendMediaGroup' => $photos,
			'sendMessage' => $content,
		];

	} // handler_m_allcrimea_net



	public function __destruct()
	{
		// $this->log->add("Profile:\n" . strip_tags(\H::profile('base')), null);
	}

}

new KorniloFF_news;