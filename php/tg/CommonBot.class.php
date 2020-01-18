<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Path.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Parser.trait.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";
# TG
require_once __DIR__ . "/tg.class.php";


class CommonBot extends TG
{
	protected
		$is_owner = false,
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

		# Определяем владельца скрипта
		$this->is_owner = $this->cbn['from']['id'] === 673976740;

		$this->responseData = [
			'chat_id' => $this->message['chat']['id'],
			'parse_mode' => 'html',
		];

		# Отсекаем inline
		if($this->message['message_id'])
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

		$this->pathBotFolder = $this->botFileInfo->getPathInfo()->getRealPath();
		$logFile = $this->botFileInfo->getBasename() . '.log';

		# Если не логируется из дочернего класса
		if(!$this->log)
		{
			require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";

			$this->log = new Logger($logFile ?? 'tg.class.log', $this->pathBotFolder ?? __DIR__);
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
		$this->license = \H::json("$this->pathBotFolder/license.json");
		# Если нет лицензии, создаём ее
		if($this->is_owner && !count($this->license))
		{
			$this->license = [$this->message['chat']['id'] => "3000-01-01"];
			\H::json("$this->pathBotFolder/license.json", $this->license);
		}

		$this->log->add("$this->pathBotFolder/license.json", null, [$this->license]);

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

	//* Include Parser trait
	use Parser;


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


	public function __destruct()
	{
		# Выводим логи
		if($this->__test) $this->log->print();
	}
} // CommonBot
