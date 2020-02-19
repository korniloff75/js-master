<?php

trait Parser {

	protected
		$baseDir = 'base/',
		$botDir;


	/**
	 * $opts = []
	 */
	public function Parser(array $opts=[])

	{
		$opts = array_merge([
			'onlyOwner'=>1,
			'browsEmul'=>null,
			'chunked'=>null
		], $opts);

		$this->log->add(__METHOD__.' $opts',null,[$opts]);

		if($opts['onlyOwner'] && !$this->is_owner)
		{
			$this->NoUpdates();
			$this->log->add(__METHOD__.' not OWNER',E_USER_ERROR);
			die;
		}

		$this->botDir = $this->botDir ?? __DIR__;

		if(substr($this->baseDir, 0, 1) !== DIRECTORY_SEPARATOR)
			$this->baseDir = "{$this->botDir}/" . basename($this->baseDir);
		// $baseDir = "{$this->botDir}/" . basename($this->baseDir);

		# Collect $this->baseSource
		$this->baseSource = $this->CollectBaseArray();

		# Перебираем ссылки
		foreach (static::$remoteSource as $source) {
			$bSource = parse_url($source, PHP_URL_HOST);
			// $bSource = basename($source);
			$base = $this->baseSource[$bSource] ?? [];

			$this->log->add(__METHOD__ . ' - $base = ', null, [$base]);
			# Получаем файл для текущего chat_id
			if(isset($base[$this->chat_id]))
			{
				$currentItem = "{$this->baseDir}/" . $base[$this->chat_id];
				$this->log->add(__METHOD__ . ' - $currentItem = ' . $currentItem);
				$this->savedBase = \H::json($currentItem);

			}


			if(!$this->AddLocalParser($source, $opts))
				continue;
			// $this->log->add(__METHOD__ . " - \$this->savedBase = ", null, [$this->savedBase]);
		}

		# If not exist new content

		if (!$this->countDiff)
			return $this->NoUpdates();
	} // Parser


	/**
	 * @source
	 * Для каждого $source в дочернем классе требуются методы parser_$name4Local и handler_$name4Local
	 */
	public function AddLocalParser(string $source, array $opts=[])
	:bool
	{
		$bSource = parse_url($source, PHP_URL_HOST);
		// $bSource = basename($source);

		$name4Local = str_replace(['.', '-'], '_', $bSource);
		$parserName = "parser_$name4Local";

		//* use custom Parser if EXIST =====
		if(!method_exists($this, $parserName))
		{
			$this->log->add("$parserName DO NOT exist!");
			return false;
		}

		$this->log->add("$parserName is exist\n\$bSource = $bSource");

		//* Парсим сайт из self::$remoteSource
		$doc = new DOMDocument();

		if($opts['browsEmul'] || $opts['chunked'])
		{
			$opts = array_merge(['sendMethod' => 'get', 'json' => 0], $opts);
			@$doc->loadHTML($this->CurlRequestBrows($source, $opts));
		}
		else @$doc->loadHTMLFile($source);

		# Обнуляем контент
		$this->content = [];

		/* Подключаем локальный парсер
			parser_parserName(current url, DOMDocument)
			получаем
			$this->definedBase
		*/
		$this->definedBase = $this->{$parserName}($source, $doc);

		if(!count($this->definedBase))
			return false;

		# Ищем различия
		if(
			!count($diff = array_diff($this->definedBase, $this->savedBase))
		)
		{
			$this->log->add('$diff is EMPTY !!!', E_USER_WARNING);
			return false;
		}

		# Пишем файл без редакции
		\H::json("{$this->baseDir}/{$this->chat_id}.$bSource.json", $this->definedBase);

		$this->log->add(__METHOD__ . " \$this->baseDir = {$this->baseDir}/{$this->chat_id}.$bSource.json");

		$diff = array_unique($diff);

		$handlerName = "handler_$name4Local";

		//* use custom handler if EXIST =====
		if(!method_exists($this, $handlerName))
			return false;
		$this->log->add("method $handlerName is exist");

		if(
			!$toSend = $this->{$handlerName}($diff)
		)
		{
			$this->log->add("method $handlerName returns ", E_USER_WARNING, [$toSend]);
			return false;
		}

		++$this->countDiff;

		// $this->log->add("\$this->countDiff = {$this->countDiff}\n\$diff = ", null, [$diff]);

		$this->Send($toSend);
		return true;
	} // AddLocalParser


	/**
	 * Чистка и отправка
	 * todo вынести фильтры в локал
	 */
	private function Send(array &$toSend)
	{
		if(!empty($toSend['sendMessage']))
		{
			shuffle($toSend['sendMessage']);

			$toSend['sendMessage'] = str_ireplace(["\r", '\r', '_', '*', '=', 'Происшествия', 'Власть', 'Курорт', 'Отдых' ], [PHP_EOL, PHP_EOL, PHP_EOL, ' ', ''], $toSend['sendMessage']);

			$this->sendMessage($toSend['sendMessage']);
		}

		if(!empty($toSend['sendMediaGroup']))
		{
			$this->sendMediaGroup($toSend['sendMediaGroup']);
		}

		// return $toSend;
	} // Send


	/**
	 * Строим дерево файлов из $this->baseDir
	 */
	public function CollectBaseArray()
	:array
	{
		$baseArray = [];

		if(!is_dir($this->baseDir))
			mkdir($this->baseDir);
		else
		{
			# Сканируем базу в массив из json-файлов
			$it = new FilesystemIterator($this->baseDir, FilesystemIterator::SKIP_DOTS);
			$it = new RegexIterator($it, "/\.json$/iu");

			foreach ($it as $fileinfo) {
				$name = $tmp = explode('.', $fileinfo->getBasename());
				# Удаляем chat_id & .json
				array_shift($tmp);
				array_pop($tmp);

				$source = implode('.', $tmp);
				$baseArray[$source] = $baseArray[$source] ?? [];
				$baseArray[$source][$name[0]] = $fileinfo->getFilename();
			}

			$this->log->add('$baseArray', null, [$baseArray]);

		}
		return $baseArray;
	} // CollectBaseArray


	/**
	 * @param sourse - current parsing url
	 * @param xpath - DOMXpath from DOMDocument
	 * @param xBlock - parent node for parsing
	 * optional @param srcName - img attribute name
	 */
	public static function DOMcollectImgs(string $source, DOMXpath &$xpath, DOMNode &$xBlock, string $srcName = 'src')
	:array
	{
		$xImgs = $xpath->query(".//img[@$srcName]", $xBlock);

		if(!$xImgs->length)
			return [];

		foreach($xImgs as $img) {
			if(!strlen($src = $img->getAttribute($srcName)))
				continue;
			if(strpos($src, 'http') !== 0)
			{
				$src = preg_replace("~^/+~", '', $src);
				$src = "$source$src";
			}

			$toCont []= "$src|||" . ($img->getAttribute('alt') ?? '');
			// trigger_error(__METHOD__ . " \$toCont = $toCont");
		}
		return $toCont ?? [];
	}


	/**
	 * @param sourse - current parsing url
	 * @param xpath - DOMXpath from DOMDocument
	 * @param xBlock - parent node for parsing
	 * optional:
	 * @param srcName - img attribute name
	 * @param excludes - array with excludes words in src
	 * Возвращает массив, пригодный для отправки в ТГ методом sendMediaGroup
	 */
	public static function ExtractImages(string $source, DOMXpath &$xpath, DOMNode &$xBlock, string $srcName = 'src', array $excludes=[])
	:array
	{
		$imgArr = self::DOMcollectImgs($source, $xpath, $xBlock, $srcName);

		if(count($excludes))
			$imgArr = array_filter($imgArr, function(&$img) use($excludes) {
				return self::stripos_array($img, $excludes) === false;
			});

		return array_map(function($i) {
			$img = explode('|||', $i);
			$src = $img[0];

			$imgToSend = [
				'type' => 'photo',
				'media' => $src,
			];
			if(!empty($img[1]))
				$imgToSend['caption'] = $img[1];

			return $imgToSend;
		}, $imgArr);
	} //* ExtractImages


	/**
	 * @param sourse - current parsing url
	 * @param mainLinks - DOMNodeList with links
	 * optional @param excludes
	 */
	public static function DOMcollectLinks(string $source, DOMNodeList &$mainLinks, array $excludes = [])
	:array
	{
		if(!is_object($mainLinks))
			return [];

		$links = [];
		foreach($mainLinks as $link) {
			$href = $link->getAttribute("href");
			if(!strlen($href))
				continue;

			$href = (stripos($href, 'http') === false) ? $source . preg_replace("~^/+~", '', $href) : $href;

			if(!self::stripos_array($href, $excludes))
			$links []= $href;
		}

		# Required $this->definedBase in CommonBot
		return array_unique($links);
	}


	protected function NoUpdates()
	{
		if (!array_key_exists('callback_query', $this->inputData)) return;

		$text = ($this->is_owner ? "Хозяин! \n" : '') . $this->noUdatesText;

		$r = $this->apiResponseJSON([
		// return $this->apiRequest([
		'callback_query_id' => $this->cbn['id'],
		'text' => $text,
		], 'answerCallbackQuery');

		$this->log->add("NOT exist new content.", null, $r);

		die;
	}


	/**
	 * https://core.telegram.org/bots/api#html-style
	 */
	public static function DOMinnerHTML(DOMNode $element, array $excludes= [])
	{
		$innerHTML = "";
		$children  = $element->childNodes;
		// trigger_error(__METHOD__);

		foreach ($children as $child)
		{
			//* Filter
			if(
				//* Скрипты
				$child->nodeName === 'script'
				//* комменты
				|| $child->nodeType === 12
			)
			{
				$rm = $element->removeChild($child);
				trigger_error(__METHOD__ . ' = ' . $rm->nodeName . ' =!= ' . $rm->textContent);
				continue;
			}

			//* Удаляем пустые текстовые узлы, etc...
			$child->normalize();

			if(
				//* Текстовые узлы с $excludes
				$child->nodeType === 3 && (
					self::stripos_array($child->textContent, $excludes) !== false
				)
			)
			{
				$rm = $element->removeChild($child->parentNode);
				trigger_error(__METHOD__ . ' = ' . $rm->nodeName . ' =!= ' . $rm->textContent);
				continue;
			}


			$innerHTML .= $element->ownerDocument->saveHTML($child);
		}

		// $innerHTML = str_ireplace($remove, '', $innerHTML);
		//* FIX 4 TG
		$innerHTML = preg_replace(
			["/^\s*\d+\s*$/m", "/\s*[\r\n]{2,}/"
		], ['', PHP_EOL], $innerHTML);
		// trigger_error(__METHOD__ . ' $innerHTML= ' . $innerHTML);

		return strip_tags($innerHTML, self::$allowedTags);
	}
}