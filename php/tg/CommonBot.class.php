<?php
//* FIX cron
if(empty($_SERVER['DOCUMENT_ROOT']))
	$_SERVER = [
		'DOCUMENT_ROOT' => realpath(__DIR__ . '/../..'),
	];

require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Path.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";
# TG
require_once __DIR__ . "/tg.class.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Get_set.trait.php";


class CommonBot extends TG
{
	use Get_set {}

	private
		$is_owner = null;

	protected
		// $is_owner = false,
		$responseData,
		$license,
		$savedBase = [],
		//* from tg.class.php
		$botDir,
		# Счётчик обновлений
		$countDiff = 0,
		$protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором <b>@korniloff75</b>",
		$noUdatesText = "Обновлений пока нет. Попробуйте позже.";

	public function __construct()
	{
		parent::__construct();
		$GLOBALS['_bot'] = &$this;

		# Определяем владельца скрипта
		$this->is_owner = $this->set('is_owner', $this->cbn['from']['id'] === 673976740);

		if(!empty($this->cron))
		{
			// var_dump($this->cron, $this->cbn);
			// echo "\$this->cbn['from']['id'] = " . $this->cbn['from']['id'];
			// $this->is_owner = 1;
		}

		$this->responseData = [
			'chat_id' => $this->chat_id,
			'parse_mode' => 'html',
		];

		# Отсекаем inline
		if(isset($this->message['message_id']))
			$this->responseData['message_id'] = $this->message['message_id'];

		return $this->init();
	} // __construct


	private function init()
	{
		if(!$this->botFileInfo)
		{
			trigger_error('botFileInfo is empty', E_USER_WARNING);
			return $this;
		}

		$this->botDir = $this->botDir ?? $this->botFileInfo->getPathInfo()->getRealPath();
		$logFile = $this->botFileInfo->getBasename() . '.log';

		# Если не логируется из дочернего класса
		if(!$this->log)
		{
			require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";

			$this->log = new Logger($logFile ?? 'tg.class.log', $this->botDir ?? __DIR__);
		}

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
		$this->license = \H::json("{$this->botDir}/license.json");
		# Если нет лицензии, создаём ее
		if($this->get('is_owner') && !count($this->license))
		{
			$this->license = [$this->message['chat']['id'] => "3000-01-01"];
			\H::json("{$this->botDir}/license.json", $this->license);
		}

		$this->log->add("$this->botDir/license.json", null, [$this->license]);

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
				"{$this->botDir}/plagiarismBase.txt",
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
	 ** Кнопка с рекламой
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


		/**
	 * @param haystack
	 * @param string||array needles
	 * @param service posArr
	 * Возвращает вхождение первой подстроки из mixed @needles
	 */
	public static function stripos_array(string $haystack, $needles, ?int $offset= 0, $posArr= [])
	{
		if ( !is_array($needles) )
			return mb_stripos($haystack, $needles, $offset);
		elseif (!count($needles))
			return false;

		foreach ($needles as $str) {
			if ( is_array($str) ) {
				$pos = self::stripos_array($haystack, $str, $offset, $posArr);
			} else {
				$pos = mb_stripos($haystack, $str, $offset);
			}

			if ($pos !== false)
				$posArr[] = $pos;
		}

		sort($posArr, SORT_NATURAL);
		// ksort($posArr, SORT_NATURAL);
		return $posArr[0] ?? false;
		// return array_keys($posArr)[0] ?? false;
	}



	public function __destruct()
	{
		# Выводим логи
		if($this->__test) $this->log->print();
	}
} // CommonBot
