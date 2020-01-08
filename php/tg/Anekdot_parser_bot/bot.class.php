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
			'https://shutok.ru/',
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
		// $this->baseSource = $this->CollectBaseArray(__DIR__ . "/{$this->baseDir}");

		# Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$this->Parser();

		$this->log->add("count(\$this->content) = " . count($this->content));

		die('OK');

	} // init


	protected function parser_shutok_ru($source, $doc)

	{
		$xpath = new DOMXpath($doc);

		$xpathBlock = "//div[@id=\"dle-content\"][1]";
		$xpathText = "//div[@class=\"box_in\"]";
		// $xpathText = "//div[@class=\"text\"]";

		$xBlock = $xpath->query($xpathBlock)->item(0);

		$xTexts = $xpath->query($xpathText, $xBlock);

		$this->log->add(__METHOD__ . " - \$xTexts = ", null, [$xTexts]);

		foreach($xTexts as $node) {
			$title = $xpath->query("h2", $node)->item(0)->textContent;
			$text = $xpath->query("div[@class=\"text\"]", $node)->item(0);
			if(strlen($text->textContent) < 30)
				continue;

			$this->content[]= "<b>$title</b>\n\n" . CommonBot::DOMinnerHTML($text);
		}

		$imgArr = CommonBot::DOMcollectImgs($source, $xpath, $xBlock, 'src');
		$imgArr = array_filter($imgArr, function(&$img) {
			return CommonBot::stripos_array($img, ['Podrobnee.png']) === false;
		});

		$this->log->add(__METHOD__ . " - \$imgArr = ", null, [$imgArr]);

		# Required
		return array_merge($this->content, $imgArr);
	} // parser_shutok_ru


	protected function parser_anekdot_ru($source, $doc)

	{
		$xpath = new DOMXpath($doc);

		$xpathBlock = "//div[@class=\"texts\"][1]";
		$xpathText = ".//div[@class=\"text\"]";

		$xBlock = $xpath->query($xpathBlock)->item(0);
		$xTexts = $xpath->query($xpathText, $xBlock);

		// $this->log->add(__METHOD__ . " - \$xTexts, xImgs = ", null, [$xTexts, $xImgs]);

		foreach($xTexts as $node) {
			$this->content[]= CommonBot::DOMinnerHTML($node);
		}

		$imgArr = CommonBot::DOMcollectImgs($source, $xpath, $xBlock, 'data-src');

		$out = array_merge($this->content, $imgArr);

		$this->log->add(__METHOD__ . " - \$imgArr = ", null, [$imgArr]);
		// $this->log->add(__METHOD__ . " - \$out = ", null, [$out]);

		# Required
		return $out;
	} // parser_anekdot_ru


	protected function parser_anekdotov_net($source, $doc)

	{
		$xpath = new DOMXpath($doc);

		$xpathBlock = "//td[@rowspan='2'][1]";
		$xpathText = ".//div[@align=\"justify\"]";

		$xBlock = $xpath->query($xpathBlock)->item(0);

		$xTexts = $xpath->query($xpathText, $xBlock);

		if(
			!$xTexts->length
		)
			return [];


		// $this->DOMNodeList = $doc->getElementsByTagName("div");

		$this->log->add(__METHOD__ . " - \$xTexts", null, [$xTexts]);

		// if(!$this->DOMNodeList->length) return;

		foreach($xTexts as $node) {
			if(CommonBot::stripos_array($node->textContent, 'а н е к д о т о в . n е t') !== false) continue;

			$content[]= CommonBot::DOMinnerHTML($node);
		}

		# Required
		return $content ?? [];
	} // parser_anekdotov_net


	/**
	 * Handlers
	 */
	protected function handler_shutok_ru(array &$diff)
	{
		foreach($diff as $k=>&$str) {
			if(
				strpos($str, 'http') === 0
			)
			{
				$img = explode('|||', $str);
				$src = $img[0];

				if(!strlen($src)) continue;

				$photos[]= [
					'type' => 'photo',
					'media' => $src,
					'caption' => $img[1],
				];
				unset($diff[$k]);
			}

			if(CommonBot::stripos_array($str, [
				'Комментарии', 'Карикатуры', 'Анекдоты в картинках', 'Картинки'
			]) !== false)
				unset($diff[$k]);
		}

		return [
			'sendMessage' => $diff,
			'sendMediaGroup' => $photos ?? [],
		];
	} // handler_shutok_ru


	protected function handler_anekdot_ru(array &$diff)
	{
		foreach($diff as $k=>&$str) {
			if(
				strpos($str, 'http') === 0
			)
			{
				$img = explode('|||', $str);
				$src = $img[0];
				if(!strlen($src)) continue;
				$photos[]= [
					'type' => 'photo',
					'media' => $src,
					'caption' => $img[1],
				];
				unset($diff[$k]);
			}
		}

		return [
			'sendMessage' => preg_replace(['/^.*(Показать полностью|читать дальше).*$|\s+\d+(>|&gt;)/ium'], '', $diff),
			'sendMediaGroup' => $photos ?? [],
		];
	}

	protected function handler_anekdotov_net(array &$diff)
	{
		return $this->handler_anekdot_ru($diff);
	}

	// Делаем АШИПКУ

	public function __destruct()
	{

	}

}

new AnekdotBot;