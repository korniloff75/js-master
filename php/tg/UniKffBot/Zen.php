<?php
require_once "UniConstruct.trait.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Parser.trait.php";

class Zen extends UniKffBot {
	use UniConstruct, Parser;

	protected static
	$remoteSource = [
		'https://zen.yandex.ru',
	];

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd);
		$this->baseDir = 'base_Zen/';

		// $this->init();
		$this->Parser();
	} //* __construct


	protected function parser_zen_yandex_ru($source, &$doc)
	:array
	{
		$xpath = new DOMXpath($doc);

		# Собираем ссылки с гл. страницы
		// $mainLinks = $xpath->query("//div[@class=\"card-image-view\"]//a[@class=\"card-image-view__clickable\"]");

		//tag[@class='clName' and starts-with(@attr, 'begin')]

		$mainLinks = $xpath->query("//a[@class='card-image-view__clickable' and starts-with(@href, 'https://zen.yandex.ru')]");

		/* $linksTest = self::DOMcollectLinks($source, $xpath->query("//a[@class='card-image-view__clickable']"));
		$this->log->add(__METHOD__ . " - \$linksTest, \$links", null, [
			$linksTest,
		]); */

		$links = self::DOMcollectLinks($source, $mainLinks);
		$this->log->add(__METHOD__ . " - \$mainLinks, \$links", null, [
			$mainLinks,
		]);

		return $links;
	} // parser_zen_yandex_ru


	protected function handler_zen_yandex_ru(array &$diff, $xpathToBlock = "//div[@itemprop=\"articleBody\"][1]")
	{
		$photos = [];
		$content = [];

		$imgXpath = $this->imgXpath ?? $xpathToBlock;
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
			$header = $xpath->query("//h1[1]")->item(0)->textContent;

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
	} //* handler_zen_yandex_ru

} //* Zen
