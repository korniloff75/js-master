<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Path.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";
# TG
require_once __DIR__ . "/tg.class.php";


class CommonBot extends TG
{
	protected
		$responseData,
		$license,
		$savedBase = [],
		# realpath к папке с ботом
		$pathBotFolder,
		# Счётчик обновлений
		$countDiff = 0,
		$protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором <b>@korniloff75</b>",
		$noUdatesText = "Обновлений пока нет. Попробуйте позже.";

	public function __construct()
	{
		parent::__construct();

		$this->responseData = [
			'chat_id' => $this->message['chat']['id'],
			'parse_mode' => 'html',
		];

		# Отсекаем inline
		if($this->message['message_id'])
			$this->responseData['message_id'] = $this->message['message_id'];

		return $this->init();
	}

	private function init()
	{
		if(!$this->botFileInfo)
		{
			trigger_error('botFileInfo is empty', E_USER_WARNING);
			return $this;
		}

		$this->pathBotFolder = $this->botFileInfo->getPathInfo()->getRealPath();
		$logFile = $this->botFileInfo->getBasename() . '.log';

		# Если не логируется из дочернего класса
		if(!$this->log)
		{
			require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";

			$this->log = new Logger($logFile ?? 'tg.class.log', $this->pathBotFolder ?? __DIR__);
		}

		# Если нет лицензии, создаём ее
		$this->license = \H::json("$this->pathBotFolder/license.json");
		if(!count($this->license))
		{
			$this->license = [$this->message['chat']['id'] => "3000-01-01"];
			\H::json("$this->pathBotFolder/license.json", $this->license);
		}

		$this->log->add("$this->pathBotFolder/license.json", null, [$this->license]);

		if(strlen($this->inputJson))
			file_put_contents($this->botFileInfo->getPath() . '/inputData.json', $this->inputJson);
		else $this->log->add("Нет callback!");

		return $this;
	} // init

	/**
	 * REQUIRES
	 * array child::license = [
	 * 	chat_id => "25-04-07", ...
	 * ]
	 */
	protected function checkLicense($responseData = null)
	{
		/* $this->log->add("checkLicense ===", null, [
			($id = $this->message['chat']['id']),
			new DateTime(),
			new DateTime($this->license[$id])
		]); */

		if(
			$this->message
			&& ($id = $this->message['chat']['id'])
			&& (
				!$this->license
				|| !in_array($id, array_keys($this->license))
				|| new DateTime() > new DateTime($this->license[$id])
			)
		)
		{
			$responseData = $responseData ?? $this->responseData;
			$responseData['text'] = $this->protecText;
			$responseData['disable_web_page_preview'] = false;
			$this->apiResponseJSON($responseData);

			file_put_contents(
				"{$this->pathBotFolder}/plagiarismBase.txt",
				(new DateTime('now'))->format('Y/M/d H:i:s')
				. " username - {$this->message['chat']['username']}; id - {$this->message['chat']['id']}"
				. PHP_EOL,
				FILE_APPEND
			);

			die;
		}

		return $this;

	}

	/**
	 * ПАРСЕР
	 */
		/**
	 *
	 */
	public function Parser()

	{
		$baseDir = "{$this->pathBotFolder}/" . basename($this->baseDir);

		# Collect $this->baseSource
		$this->baseSource = $this->CollectBaseArray($baseDir);

		# Перебираем ссылки
		foreach (static::$remoteSource as $source) {
			$bSource = basename($source);
			$base = $this->baseSource[$bSource] ?? [];

			$this->log->add(__METHOD__ . ' - $base = ', null, $base);
			# Получаем файл для текущего chat_id
			if(isset($base[$this->chat_id]))
			{
				$currentItem = "$baseDir/" . $base[$this->chat_id];
				$this->log->add(__METHOD__ . ' - $currentItem = ' . $currentItem);
				$this->savedBase = \H::json($currentItem);

			}

			if(!$this->AddLocalParser($source))
				continue;

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

	} // Parser


	/**
	 * @bSource
	 * Для каждого $bSource в дочернем классе требуются методы parse_bSource и handler_bSource
	 */
	public function AddLocalParser(string $source)
	:bool
	{
		$bSource = basename($source);

		$name4Local = str_replace(['.', '-'], '_', $bSource);
		$parserName = "parser_$name4Local";

		# use custom Parser if EXIST =====
		if(!method_exists($this, $parserName))
			return false;

		$this->log->add("\$bSource = $bSource");

		# Парсим сайт из self::$remoteSource
		$doc = new DOMDocument();
		@$doc->loadHTMLFile($source);

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
		\H::json($this->baseDir . "{$this->chat_id}.$bSource.json", $this->definedBase);

		$diff = array_unique($diff);

		$handlerName = "handler_$name4Local";

		# use custom handler if EXIST =====
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
	 * Чистим для MD
	 */
	private function Send(array &$toSend)
	{
		// disable_web_page_preview
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

		// $this->log->add(__METHOD__ . " - \$this->savedBase = ", null, [$this->savedBase]);
		// $this->log->add(__METHOD__ . " - \$this->definedBase = ", null, [$this->definedBase]);

		// return $toSend;
	} // Send


	/**
	 * Строим дерево файлов из $baseDir
	 */
	public function CollectBaseArray(string $baseDir)
	:array
	{
		$baseArray = [];

		if(!is_dir($baseDir))
			mkdir($baseDir);
		else
		{
			# Сканируем базу в массив из json-файлов
			$it = new FilesystemIterator($baseDir, FilesystemIterator::SKIP_DOTS);
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
	 * @sourse - current parsing url
	 * @xpath - DOMXpath from DOMDocument
	 * @xBlock - parent node for parsing
	 * optional @srcName - img attribute name
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
			// $this->log->add("\$toCont = $toCont");
		}
		return $toCont ?? [];
	}


	/**
	 * https://core.telegram.org/bots/api#html-style
	 */
	public static function DOMinnerHTML(DOMNode $element, array $remove= [])
	{
		$innerHTML = "";
		$children  = $element->childNodes;

		foreach ($children as $child)
		{
			# Remove scripts & empty
			if(
				$child->nodeName === 'script'
				|| $child->nodeName !== 'br' && !strlen(trim($child->textContent))
			)
				continue;
			$innerHTML .= $element->ownerDocument->saveHTML($child);
		}

		# Filter
		$innerHTML = str_ireplace($remove, '', $innerHTML);
		# FIX 4 TG
		$innerHTML = preg_replace("/(\n*<br\.*?>|<br\.*?>\n*)+/iu", PHP_EOL, $innerHTML);

		return strip_tags($innerHTML, '<pre><b><strong><i><em><u><ins><s><strike><del><code>');
	}


	/* if(
		CommonBot::stripos_array($p->nodeValue, ['Полная версия сайта', 'Обратная связь', 'Политика конфидициальности', 'Отказ от ответственности',]) !== false
	) continue; */
	/**
	 * Возвращает вхождение первой подстроки из mixed @needles
	 */
	public static function stripos_array(string $haystack, $needles, ?int $offset= 0, $posArr= [])
	{
		if ( !is_array($needles) )
			return mb_stripos($haystack, $needles, $offset);

		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = self::stripos_array($haystack, $str, $offset, $posArr);
			} else {
				$pos = mb_stripos($haystack, $str, $offset);
			}
			if ($pos !== false) {
				$posArr[$pos] = $str;
			}
		}

		ksort($posArr, SORT_NATURAL);
		return array_keys($posArr)[0] ?? false;
			/* !count($posArr) ? false
			: array_keys($posArr)[0]; */
	}

	// ПАРСЕР


	/**
	 * Кнопка с рекламой
	 */
	public static function setAdvButton()
	{
		# Advert
		$adv = \H::json(__DIR__ . '/Common/Adv.json');
		if(!count($adv))
		{
			$this->log->add('realpath Common/Adv.json = ' . realpath(__DIR__ . '/Common/Adv.json') . "\nDIR = " . __DIR__, E_USER_WARNING, [$adv]);
			return false;
		}

		$text = array_keys($adv);
		shuffle($text);

		return [
			"text" => $text[0],
			"url" => $adv[$text[0]],
		];
	}


	public function __destruct()
	{
		# Выводим логи
		if($this->__test) $this->log->print();
	}
} // CommonBot
