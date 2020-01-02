<?php
# TG
require_once __DIR__ . "/tg.class.php";


class CommonBot extends TG
{
	protected
		$responseData,
		$protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором *@korniloff75*";

	public function __construct()
	{
		parent::__construct();

		$this->responseData = [
			'chat_id' => $this->message['chat']['id'],
			'message_id' => $this->message['message_id'],
			'parse_mode' => 'Markdown',
		];

		return $this->init();
	}

	private function init()
	{
		# Если не логируется из дочернего класса
		if(!$this->log)
		{
			require_once $_SERVER['DOCUMENT_ROOT'] . "/php/classes/Logger.php";
			if($this->botFileInfo)
			{
				// $path = $this->botFileInfo->getPathname();
				$path = $this->botFileInfo->getPathInfo()->getRealPath();
				$file = $this->botFileInfo->getBasename() . '.log';

				$this->license = \H::json("$path/license.json") ?? $this->license;
			}
			$this->log = new Logger($file ?? 'tg.class.log', $path ?? __DIR__);
		}

		if(strlen($this->inputJson))
			file_put_contents($this->botFileInfo->getPath() . '/inputData.json', $this->inputJson);
		else $this->log->add("Нет callback!");

		return $this;
	}

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
			!$this->message
			|| !($id = $this->message['chat']['id'])
			|| !$this->license
			|| !in_array($id, array_keys($this->license))
			|| new DateTime() > new DateTime($this->license[$id])
		)
		{
			$responseData = $responseData ?? $this->responseData;
			$responseData['text'] = $this->protecText;
			$this->apiResponseJSON($responseData);
			die;
		}

		return $this;

	}
} // CommonBot
