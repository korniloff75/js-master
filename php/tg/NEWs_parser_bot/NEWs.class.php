<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once __DIR__ . "/../CommonBot.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Parser.trait.php";

class KorniloFF_news extends CommonBot
{
	//* Include Parser trait
	use Parser;

	protected
		# Test mode, bool
		$__test = 1 ,

		$baseDir = 'base/',
		$cron = [
			'chat'=> ['id' => -1001223951491,],
			// 'chat'=> ['id' => 673976740,],
			'from'=> ['id' => 673976740],
		],
		// Specify headers
		$stream_context_options = [
			'www_yalta_24_ru' => [
				'http' => [
					"Accept"=> "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
					"Accept-Encoding"=> "gzip, deflate",
					"Accept-Language"=> "ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3",
					"Cache-Control"=> "no-cache",
					"Connection"=> "keep-alive",
					"User-Agent"=> "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:72.0) Gecko/20100101 Firefox/72.0",

					'Host' => 'www.yalta-24.ru',
					'Upgrade-Insecure-Requests'=> 1,

				]
			]
		],
		$baseSource = [],

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
			'http://www.yalta-24.ru/',
		];

	private
		$imgXpath;



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
		//* Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->Parser();

		die('OK');

	} // init


	protected function parser_crimea_news_com($source, &$doc)
	:array
	{
		$xpath = new DOMXpath($doc);

		# Собираем ссылки с гл. страницы
		$mainLinks = $xpath->query("//div[@class=\"top-day\"][1]//a");

		$links = self::DOMcollectLinks($source, $mainLinks);
		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
		]);

		return $links;
	} // parser_crimea_news_com


	protected function parser_m_allcrimea_net($source, &$doc)
	:array
	{
		$xpath = new DOMXpath($doc);

		$mainLinks = $xpath->query("//div[@id=\"container\"][1]//a");

		$links = self::DOMcollectLinks($source, $mainLinks);
		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
		]);

		return $links;
	} // parser_m_allcrimea_net


	protected function _parser_www_yalta_24_ru($source, &$doc)
	:array
	{
		$xpath = new DOMXpath($doc);

		$mainLinks = $xpath->query("//p[@class=\"readmore\"]/a[1]");
		// $main = $xpath->query("//div[contains(@class, \"content-top-in\")][3]");

		// todo START
		foreach($xpath->query("//p[1]") as $p) {
			@$t .= "{$p->textContent}\n\n";
		}
		// todo END

		$links = self::DOMcollectLinks($source, $mainLinks, ['za-predelami-yalty']);
		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			mb_detect_encoding($t),
			// $t,
			$mainLinks,
			count($links),
			// $xpath->query("//p[@class=\"readmore\"]"),
		]);

		return $links;
	} // parser_www_yalta_24_ru


	/**
	 ** Обрабатываем неопубликованный контент
	 */
	protected function handler_crimea_news_com(array &$diff, $xpathToBlock = "//div[@class=\"js-mediator-article\"][1]")
	{
		$photos = [];
		$content = [];

		$imgXpath = $this->imgXpath ?? "//div[@class=\"news_c\"][1]";
		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$s = parse_url($link);
			$source = "{$s['scheme']}://{$s['host']}/";
			$addContent = '';

			$docLink = @DOMDocument::loadHTMLFile($link);
			$xpath = new DOMXpath($docLink);

			if(
				!is_object($xBlock = $xpath->query($xpathToBlock)->item(0))
			)
				continue;

			$xImg = $imgXpath === $xpathToBlock ? $xBlock : $xpath->query($imgXpath)->item(0);

			if(is_object($xImg))
			{
				$imgArr = self::ExtractImages($source, $xpath, $xImg, 'src', ['crimeanews.jpg', 'size100/']);
				// $this->log->add('$imgArr', null, [$imgArr]);
				$photos = array_merge_recursive($photos, $imgArr);
			}

			//* Собираем для добавления в $content
			$header = $xpath->query(".//h1[1]")->item(0)->textContent;

			$addContent .= self::DOMinnerHTML(
				$xBlock, ['Новости за:', 'Читайте:', 'Новости Крыма', 'сообщали ранее:', 'Источник:']
			);

			if(strlen(trim($addContent)))
				$content[]= "<b>$header</b>" . PHP_EOL . PHP_EOL . $addContent;
		}

		$this->log->add('count($photos) = ' . count($photos));

		# На отсылку
		if(count($content))
			$out['sendMessage'] = $content;
		if(count($photos))
			$out['sendMediaGroup'] = $photos;

		return $out;
	} //* handler_crimea_news_com


	protected function handler_m_allcrimea_net(array &$diff)
	{
		//* На сайте 2 id=newscont В первом - изображение, во втором - текст.
		$this->imgXpath = "//div[@id=\"newscont\"]/..";
		return $this->handler_crimea_news_com($diff, "//div[@id=\"newscont\"][2]");

	} // handler_m_allcrimea_net


	protected function handler_www_yalta_24_ru(array &$diff)
	{
		$photos = [];
		$content = [];
		$xpathToBlock = $imgXpath = "//div[@class=\"item-page\"][1]";
		//* Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$s = parse_url($link);
			$source = "{$s['scheme']}://{$s['host']}/";
			// $docLink = new DOMDocument();

			// @$docLink->loadHTMLFile($link);

			if($chunkedContent = $this->getChunkedContent($link))
				$docLink = @DOMDocument::loadHTML($chunkedContent);
			else
				continue;

			$xpath = new DOMXpath($docLink);

			if(!is_object($xBlock = $xpath->query($xpathToBlock)->item(0)))
				continue;

			//* Собираем для добавления в $content
			$header = 'Ялта: ' . $xpath->query(".//h1[1]", $xBlock)->item(0)->textContent;

			// $addContent .= $xBlock->item(0)->textContent;
			$addContent = self::DOMinnerHTML(
				// div[@itemprop='articleBody']
				// $xpath->query("./*", $xBlock)->item(0),
				$xBlock,
				['Опубликовано']
			);

			if(strlen(trim($addContent)))
				$content[]= "<b>$header</b>" . PHP_EOL . PHP_EOL . $addContent;

			$imgArr = self::ExtractImages($source, $xpath, $xBlock, 'src');
			// $this->log->add('$imgArr', null, [$imgArr]);
			$photos = array_merge_recursive($photos, $imgArr);
		}

		$this->log->add(__METHOD__ . ' count($content) = ' . count($content));
		$this->log->add(__METHOD__ . ' count($photos) = ' . count($photos));

		# На отсылку
		if(count($content))
			$out['sendMessage'] = $content;
		if(count($photos))
			$out['sendMediaGroup'] = $photos;

		return $out;

	} // handler_www_yalta_24_ru



	public function __destruct()
	{
		// $this->log->add("Profile:\n" . strip_tags(\H::profile('base')), null);
	}

}

new KorniloFF_news;