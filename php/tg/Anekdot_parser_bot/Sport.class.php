<?php
class Sport extends CommonBot
{
	//* Include Parser trait
	use Parser;

	protected
		# Test mode, bool
		$__test = 1 ,
		$baseSource = [],
		// $contentSum = [],
		$content = [],
		# Счётчик обновлений
		$countDiff = 0,
		$cron = [
			'chat'=> ['id' => -1001365592780],
			// 'chat'=> ['id' => 673976740],
			'from'=> ['id' => 673976740],
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
			'https://www.sport-express.ru/news/',
			// 'https://www.sports.ru/news/',
			// 'https://www.sport.ru/'
		];


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);
		$this->baseDir = __DIR__.'/sport_base';

		# Запускаем скрипт
		parent::__construct()->checkLicense()->init();

	} //* __construct

	/**
	 *
	 */
	private function init()
	{
		# Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->Parser();

		die('OK');

	} // init



	protected function parser_www_sport_ru($source, DOMDocument &$doc)

	{
		$xpath = new DOMXpath($doc);

		# Собираем ссылки с гл. страницы
		$mainLinks = $xpath->query("//div[contains(@class, 'chameleon-main')][1]//div[@class='itm']//a");

		$links = self::DOMcollectLinks($source, $mainLinks);
		tolog(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
			$links
		]);

		return $links;

	}


	protected function parser_www_sport_express_ru($source, DOMDocument &$doc)

	{
		$xpath = new DOMXpath($doc);

		# Собираем ссылки с гл. страницы
		$mainLinks = $xpath->query("//a[@class=\"se19-news-item__link\"]");

		$links = self::DOMcollectLinks($source, $mainLinks);
		tolog(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
			$links
		]);

		return $links;

	}


	/**
	 * Handlers
	 */
	protected function handler_www_sport_ru(array &$diff, $xpathToBlock = "//div[contains(@class, 'news-item__content')][1]")
	{
		$photos = [];
		$content = [];

		// $imgXpath = $this->imgXpath ?? $xpathToBlock;
		$imgXpath = $xpathToBlock;
		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			//* Trim the excess
			$link= str_replace('news/','',$link);
			$s = parse_url($link);
			$source = "{$s['scheme']}://{$s['host']}{$s['path']}/";
			$addContent = '';

			if(!$xpath= $this->_checkLink($link)){
				continue;
			}

			// tolog('$xpath', null, [$source, $xpath, $link, $xpath->query($xpathToBlock)->item(0)->textContent]);

			if(
				!is_object($xBlock = $xpath->query($xpathToBlock)->item(0))
			)
				continue;

			$xImg = $imgXpath === $xpathToBlock ? $xBlock : $xpath->query($imgXpath)->item(0);

			if(is_object($xImg))
			{
				$imgArr = self::ExtractImages($source, $xpath, $xImg, 'src', []);
				// tolog('$imgArr', null, [$imgArr]);
				$photos = array_merge_recursive($photos, $imgArr);
			}

			//* Собираем для добавления в $content
			$header = $xpath->query("//h1[1]")->item(0)->textContent;

			// $pgs= $xpath->query(".//p[not(@class)]",$xBlock);

			// tolog('source,xpath,pgs', null, [$source, $xpath, $pgs, /* $xpath->query($xpathToBlock)->item(0)->textContent, */ self::DOMinnerHTML($pgs)]);

			$addContent .= self::DOMinnerHTML(
				$pgs, []
			);

			if(strlen(trim($addContent)))
				$content[]= "<b>$header</b>" . PHP_EOL . $addContent;
		}

		tolog(__METHOD__.' content = ',null, [gzdecode($addContent)]);

		# На отсылку
		if(count($content))
			$out['sendMessage'] = $content;
		if(count($photos))
			$out['sendMediaGroup'] = $photos;

		// return $out;
		return null;

	} // handler_www_sports_ru


	protected function handler_www_sport_express_ru(array &$diff, $xpathToBlock = "//div[contains(@class, 'article_text')][1]")
	{
		$photos = [];
		$content = [];

		// $imgXpath = $this->imgXpath ?? $xpathToBlock;
		$imgXpath = "//div[contains(@id, 'slideshow')][1]";
		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$s = parse_url($link);
			$source = "{$s['scheme']}://{$s['host']}{$s['path']}/";
			$addContent = '';

			if(!$xpath= $this->_checkLink($link)){
				continue;
			}

			// tolog('$xpath', null, [$source, $xpath, $link, $xpath->query($xpathToBlock)->item(0)->textContent]);

			if(
				!is_object($xBlock = $xpath->query($xpathToBlock)->item(0))
			)
				continue;

			$xImg = $imgXpath === $xpathToBlock ? $xBlock : $xpath->query($imgXpath)->item(0);

			if(is_object($xImg))
			{
				$imgArr = self::ExtractImages($source, $xpath, $xImg, 'src', []);
				// tolog('$imgArr', null, [$imgArr]);
				$photos = array_merge_recursive($photos, $imgArr);
			}

			//* Собираем для добавления в $content
			$header = $xpath->query("//h1[1]")->item(0)->textContent;

			$pgs= $xpath->query(".//p[not(@class)]",$xBlock);

			// tolog('source,xpath,pgs', null, [$source, $xpath, $pgs, /* $xpath->query($xpathToBlock)->item(0)->textContent, */ self::DOMinnerHTML($pgs)]);

			$addContent .= self::DOMinnerHTML(
				$pgs, []
			);

			if(strlen(trim($addContent)))
				$content[]= "<b>$header</b>" . PHP_EOL . PHP_EOL . $addContent;
		}

		// tolog('content = ',null, [$addContent]);

		# На отсылку
		if(count($content))
			$out['sendMessage'] = $content;
		if(count($photos))
			$out['sendMediaGroup'] = $photos;

		return $out;

	} // handler_www_sport_express_ru


	private function _checkLink($link)
	{
		$xpath= null;
		$docLink = @DOMDocument::loadHTMLFile($link);

		if($docLink instanceof DOMDocument){
			$xpath = new DOMXpath($docLink);
		}
		else{
			tolog(__METHOD__,null,['$docLink'=>$docLink]);
		}

		return $xpath;
	}

} //* Sport