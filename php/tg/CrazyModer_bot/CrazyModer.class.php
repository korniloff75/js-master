<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Path.php";

# TG
require_once __DIR__ . "/../tg.class.php";


class CrazyModer extends TG implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$token = '915001667:AAEmu9aYr6d6iRSKJ2JVSHA7jbF7i3AdEFE',
		$protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором *@korniloff75*";

	private static
		# Input stream
		$allowed = [-1001251056203,],
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
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		parent::__construct()->init();

	} //__construct

	/**
	 *
	 */
	protected function init()
	{
		/* if(empty($this->inputData) || !$this->message)
		{
			$this->log->add('Нет входящего запроса');
			die ('Нет входящего запроса');
		}
		else $this->log->add('$this->message', null, [$this->message]); */

		#
		$responseData = [
			'chat_id' => $this->message['chat']['id'],
			'message_id' => $this->message['message_id'],
			'parse_mode' => 'Markdown',
		];

		# Protect
		/* if(!in_array($this->message['chat']['id'], self::$allowed))
		{
			$responseData['text'] = $this->protecText;
			return $this->apiResponseJSON($responseData);
		} */

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

		$responseData['text'] = $censure;

		if($base[$user]['count'] > 3)
		{
			$this->log->add("*{$user} was locked!*");
		}
		else
		{
			\H::json('base.json', $base);

			$this->log->add("apiRequest = ", null, [$this->apiRequest($responseData)]);

		}

		$this->log->add("apiResponseJSON = ", null, [$this->apiResponseJSON($responseData, 'deleteMessage')]);

		die('OK');

	} // init


	public function __destruct()
	{
		$now = new DateTime(); // текущее время на сервере
		$date = DateTime::createFromFormat("d.m.y H:i", '2014-09-12 23:59'); // задаем дату в любом формате
		$p = new DateInterval('P1M');
		$lim = $date->add($p);
		if(new DateTime() > $lim)
		{
			// time over
		}
		$interval = $now->diff($date); // получаем разницу в виде объекта DateInterval
		echo $interval->y, "\n"; // кол-во лет
		echo $interval->d, "\n"; // кол-во дней
		echo $interval->h, "\n"; // кол-во часов
		echo $interval->i, "\n"; // кол-во минут
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

} // CrazyModer

new CrazyModer;