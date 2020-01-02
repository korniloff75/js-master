<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Path.php";

# TG
require_once __DIR__ . "/../tg.class.php";
require_once __DIR__ . "/../CommonBot.class.php";


class AntimatKFF extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '1017974908:AAERu1qQlpZCVEgmK8BIsEZRyNgIsMaS3fk',
		$license = [
			-1001251056203 => "3000-12-31",
		],

		$protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором *@korniloff75*";

	protected static
		$patterns = [
			'~(?:\\s|^|>|\\]).{0,4}?[хx][\\s_]*?[уy](?![бж])[\\s_]*?[ийеёюя](?![з])~iu',
			'~п(?![ор]).?[еёиі].{0,2}?[зz3].{0,2}?д[а@]?~iu',
			'~(?:[^аеор]|\\s|^)[бм]и?ля[дт]ь?|п[еи][дg][ао]?р~iu',
			'~г[ао]вно?|г[оа]ндон|жоп[аеу]|[^о]мандав?[^лрт]|\\bауе\\b~iu',
			'~(?:[^вджл-нр-тчш]|^|\\s)[ьъ]?[еёїє]б\\W*?[^ы\\s]~iu',
			'~сра[лт]ь?|залупа?|дроч~iu',
			// фразы
			'~сос[иу] (?:член|хуй|хер)|(?:член|хуй|хер) сос[иу]~iu',
			# Test
			'~123~',
		];


	public function __construct()
	{
		// \H::json('license.json', $this->license);
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		# Protect from CommonBot
		// $this->checkLicense();
		parent::__construct()->checkLicense()->init();

	} //__construct

	/**
	 *
	 */
	protected function init()
	{
		if(empty($this->inputData) || !$this->message)
		{
			$this->log->add('Нет входящего запроса');
			die ('Нет входящего запроса');
		}
		else $this->log->add('$this->message', null, [$this->message]);

		$text = $this->message['text'];
		$censure = preg_replace(self::$patterns, "\[[цензура](https://js-master.ru/content/5.Razrabotki/Antimat_plus)]", $text);

		# Мата нет
		if(!strcmp($text, $censure))
		{
			$this->log->add("censure FAIL", null, [$text, $censure]);
			return;
		}
		else
		{
			$this->log->add("censure SUCCESS");
		}

		$user = $this->message['from']['username'];
		$base = \H::json('base.json');
		$base[$user] = $base[$user] ?? ['count'=>0];

		if(++$base[$user]['count'] > 3)
			$censure = "Всё, пиздец тебе, *@{$user}*!\n\nВсе твои посты с матом впредь будут удаляться. Переходи на литературный язык.";
		else
			$censure = "Отредактировано сообщение от @{$user}\n\n$censure\n\nПользователю *@{$user}* выдано {$base[$user]['count']} предупреждение от администрации.";

		$this->responseData['text'] = $censure;

		if($base[$user]['count'] > 3)
		{
			$this->log->add("*{$user} was locked!*");
		}
		else
		{
			\H::json('base.json', $base);

			$this->log->add("apiRequest = ", null, [$this->apiRequest($this->responseData)]);

		}

		$this->log->add("apiResponseJSON = ", null, [$this->apiResponseJSON($this->responseData, 'deleteMessage')]);

		die('OK');

	} // init


	public function __destruct()
	{
		?>
		<meta charset="UTF-8">
		<style>
		pre {
			box-sizing: border-box;
			white-space: pre-wrap;
			border: inset 1px #eee;
		}
		</style>
		<?php
	}

}

new AntimatKFF;