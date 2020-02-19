<?php
class Anekdot extends CommonBot
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
			'chat'=> ['id' => -1001393900792,],
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
			'https://anekdot.ru/',
			'http://anekdotov.net/',
			'https://shutok.ru/',
		];


	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);
		$this->baseDir = __DIR__.'/base/';

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



	protected function parser_shutok_ru($source, DOMDocument &$doc)

	{
		$xpath = new DOMXpath($doc);

		# Собираем ссылки с гл. страницы
		$mainLinks = $xpath->query("//div[@id=\"dle-content\"][1]//div[@class=\"story_tools\"]/a");

		$links = self::DOMcollectLinks($source, $mainLinks);
		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
			$links
		]);

		return $links;

	} // parser_shutok_ru


	protected function parser_anekdot_ru($source, DOMDocument &$doc)

	{
		$xpath = new DOMXpath($doc);
		$content = [];

		$xpathBlock = "//div[@class=\"texts\"][1]";
		$xpathText = ".//div[@class=\"text\"]";

		$xBlock = $xpath->query($xpathBlock)->item(0);
		$xTexts = $xpath->query($xpathText, $xBlock);

		// $this->log->add(__METHOD__ . " - \$xTexts, xImgs = ", null, [$xTexts, $xImgs]);

		foreach($xTexts as $node) {
			$content[]= self::DOMinnerHTML($node);
		}

		$imgArr = self::DOMcollectImgs($source, $xpath, $xBlock, 'data-src');

		$out = array_merge($content, $imgArr);

		$this->log->add(__METHOD__ . " count(\$imgArr) = ", null, [count($imgArr)]);
		// $this->log->add(__METHOD__ . " - \$out = ", null, [$out]);

		# Required
		return $out;
	} // parser_anekdot_ru


	protected function parser_anekdotov_net($source, &$doc)

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

		$this->log->add(__METHOD__ . " - \$xTexts", null, [$xTexts]);

		// if(!$this->DOMNodeList->length) return;

		foreach($xTexts as $node) {
			$content[]= self::DOMinnerHTML($node);
		}

		# Required
		return $content ?? [];
	} // parser_anekdotov_net


	/**
	 * Handlers
	 */
	protected function handler_shutok_ru(array &$diff)
	{
		$photos = [];
		$content = [];
		$out = [];

		# Перебираем все новые ссылки и грузим из них в контент
		foreach ($diff as &$link) {
			$s = parse_url($link);
			$source = "{$s['scheme']}://{$s['host']}/";

			$docLink = DOMDocument::loadHTMLFile($link);
			$xpath = new DOMXpath($docLink);

			// if(!is_object($xBlock = $xpath->query("//article[@class=\"fullstory\"][1]")->item(0)))
			if(!is_object($xBlock = $xpath->query("//article[1]")->item(0)))
				continue;

			# Extract text
			$xTexts = $xpath->query(".//div[@class=\"text\"][1]", $xBlock);
			$text = $xTexts->item(0);
			// $this->log->add(__METHOD__ . " - \$xTexts = ", null, [$xTexts]);

			if(
				strlen($text->textContent) > 30
			)
			{
				$content[]= self::DOMinnerHTML($text, [
					'Комментарии', 'Карикатуры', 'Анекдоты в картинках', 'Картинки'
				]);
			}

			//* Extract images
			$imgArr = self::ExtractImages($source, $xpath, $xBlock, 'src', ['Podrobnee.png', 'Istochnik.png', 'Default', 'top-fwz1.mail.ru', 'counter', 'mc.yandex.ru']);

			$photos = array_merge_recursive($photos, $imgArr);
			// $this->log->add(__METHOD__ . " - \$imgArr = ", null, [$imgArr]);

		}

		// $this->log->add(__METHOD__ . " - \$photos = ", null, [$photos]);

		if(count($content))
			$out['sendMessage'] = $content;
		if(count($photos))
			$out['sendMediaGroup'] = $photos;

		return $out;

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
			'sendMessage' => array_filter($diff, function($i) {
				return preg_match('/Показать полностью|читать дальше|а н е к д о т о в \. n е t|\s+\d+(>|&gt;)/iu', $i) === 0;
			}),

			'sendMediaGroup' => $photos ?? [],
		];
	} //* handler_anekdot_ru

	protected function handler_anekdotov_net(array &$diff)
	{
		return $this->handler_anekdot_ru($diff);
	}

}

new Anekdot;