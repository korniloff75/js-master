<?php

class SiteMap_RSS

{
	public
		$sitemap = '<?xml version="1.0" encoding="UTF-8"?>
		<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n",
		$rss = '<?xml version="1.0" encoding="UTF-8"?>
		<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
		version="2.0">
		<channel>
		<title>' . \SITENAME . '</title>
		<link>' . \BASE_URL . '</link>
		<description>' . \DESCRIPTION . '</description>
		<language>' . \LANG . '</language>
		<turbo:analytics type="LiveInternet"></turbo:analytics>' . "\n";


	public function __construct()
	{
		$this->path = \SITEMAP['path'] ?? 'sitemap.xml';
		$this->RSSpath = \SITEMAP['RSSpath'] ?? 'rss.xml';

		if(
			!\AJAX && (
				!file_exists($this->path)
				|| !file_exists($this->RSSpath)
				|| empty(\SITEMAP['expires'])
				|| (ceil(time() - filemtime($this->path))/3600/24 > \SITEMAP['expires'])
			)
		)
		{
			$this->build();
		}
	} // __construct


	public function build()

	{
		global $Nav;

		foreach($Nav->map_flat as &$page) {
			// var_dump(filemtime($page));
			$data = \H::json($page . '/data.json');
			// var_dump($page . '/data.json', $data);

			// *Скрытые
			if(!empty($data['hidden'])) continue;

			// $page = \Path::fromRootStat($page);
			$this->sitemap .= "<url>\n"
			. "<loc>" . \BASE_URL . $page . "</loc>\n"
			. "<lastmod>" . date ('Y-m-d', filemtime($page)) . "</lastmod>\n"
			. "<changefreq>weekly</changefreq>\n"
			. "<priority>0.7</priority>\n"
			. "</url>\n";

			# RSS
			# Собираем контент
			$itemContent = php\classes\Render::contentCollect($page);

			# Чистка
			/* $itemContent = preg_replace_callback_array([
				"#<(=|\s*\d+)#ui" => function($matches) {
				return htmlspecialchars($matches[0]);
				},
				# FIX html in title
				"#(title)=\"(.*?)\"#ui" => function($matches) {
				return "{$matches[1]}=\"" . htmlspecialchars($matches[2]) . '"';
				},
				# FIX html in code
				'#<(code)>(.+?)</\1>#ui' => function($matches) {
				return "<{$matches[1]}>" . htmlspecialchars($matches[2]) . "</{$matches[1]}>";
				},

			], $itemContent); */


			$this->rss .= '<item turbo="true">'
			. "\n<link>" . \BASE_URL . $page . "</link>\n"
			. "\n<turbo:content>
				<![CDATA[\n"
			. $this->_addToRss($itemContent)
			. "\n]]>
				</turbo:content>\n"
			. "</item>\n\n";

		} // foreach($Nav->map_flat as &$page)

		$this->sitemap .= "\n</urlset> ";
		$this->rss .= "\n</channel>\n</rss>";

		# Compress
		if( \SITEMAP['gzip'])
		{
			file_put_contents($this->path . '.gz', gzencode($this->sitemap,  \SITEMAP['gzip']));
		}

		file_put_contents($this->path, $this->sitemap);
		file_put_contents($this->RSSpath, $this->rss);

		return $this->sitemap;
	} // build


	/**
	 * *Добавляем элемент в RSS
	 * обязательный header добавляется при генерации контента
	 */
	private function _addToRss($itemContent)
	{
		// $doc = new DOMDocument('1.0','utf-8');
		$doc = new DOMDocument();
		@$doc->loadHTML($itemContent);

		$doc->normalizeDocument();

		$xpath= new \DOMXPath($doc);

		// $body= $xpath->query('//body/descendant::*');
		$scripts= $xpath->query('//script|//code|//pre|//style');

		foreach($scripts as $s){
			$s->parentNode->removeChild($s);
		}

		$body= $xpath->query('//body')->item(0);

		$xml= utf8_decode($doc->saveXML($body));
		// $xml= $doc->saveXML($body);

		return preg_replace('~<body>([\s\S]+)</body>~u', '$1', $xml, 1);
	}

} // SiteMap_RSS


// exit ((new SiteMap_RSS)->build());
