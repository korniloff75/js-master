<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once $_SERVER['DOCUMENT_ROOT'] . "/Helper.php";

require_once \HOME . "php/Path.php";

require_once "../CommonBot.class.php";

class KffBatonBot extends CommonBot implements iBotTG
{
	protected
		# Test mode, bool
		$__test = 1 ,
		$baseDir = 'base/',
		$rhyme = [
			'батон' => 'Натяни себе гандон!',
			'да' => 'Пизда!',
			'нет' => 'Сделай сам себе минет!',
			'ладно' => 'В пизде прохладно',
			'давай' => 'Пошёл нахуй, или наливай!',
			'пойду' => 'Иди в пизду!',
			'пошел' => 'На хуй?',
			'пошёл' => 'На хуй?',
			'ок' => 'У пиндоса хуй промок!',
			'бал' => 'Пиши правильно, ты заебал!',
			'пока' => 'Пиздуй, пока не намяли бока!',
			'поздравляю' => 'С Новым Годом, пошёл нахуй!',
		],
		$replace = ['а'=>'Хуя','я'=>'Хуя','о'=>'Хуё','ё'=>'Хуё','е'=>'Хуе','э'=>'Хуе','и'=>'Хуи','ы'=>'Хуи','у'=>'Хую','ю'=>'Хую'];

	protected static
		$exclude_id = [
			# Гриша
			773015696,
			#
			// 478262295
		],
		$defaultText = 'Я хуй его знает, о чём ты пиздишь.';

	private
		$firstWord;

	public function __construct()
	{
		//* Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		//* Запускаем скрипт
		# Protect from CommonBot checkLicense()->
		parent::__construct()->init();

	} //*__construct

	/**
	 *
	 */
	private function init()
	{
		//* Завершаем скрипт без входящего JSON
		if(empty($this->inputData)) die ('Нет входящего запроса');

		$text = $this->message['text'] = trim($this->message['text']);
		$textArr = explode(' ', $text);
		$this->firstWord = $textArr[0];

		$this->glas = array_keys($this->replace);

		$this->log->add('$text, $firstWord = ', null, [$text, $this->firstWord]);

		$this->Router($text);

		die('OK');

	} // init


	private function Router(string $text)
	{
		$responseData = $this->responseData;
		# Commands
		switch ($text) {
			case '/start':
				$mess = 'Здарова, я Батон!';
				break;

			default:

				break;
		}

		$this->log->add('!!!===', null, [
			mb_substr($text, 0, 1),
		]);

		# Пишем за Батона
		if(strpos($text, 'Батон=') === 0)
		{
			$mess = str_replace('Батон=', '', $text);
			$this->apiResponseJSON($this->responseData, 'deleteMessage');
		}

		$mess = $mess ?? $this->Handler($text);

		$responseData['text'] = $mess;

		if(
			$this->chat_id == 673976740
			&& $this->__test
		)
		{
			$content = $this->log->get();
			$content []= "\n\$mess = $mess";
			return $this->sendMessage($content);
		}

		// if($mess) $this->Request($content);
		if($mess) $this->apiRequest($responseData);
	}


	private function Handler($text)
	:?string
	{
		# Обрабатываем сообщение из 1 слова
		if(strcasecmp($this->firstWord, $text) !== 0)
			return null;

		# Очищаем слово от символов, смайлов, ...
		$clearText = preg_replace("/[^a-zа-яё]+/ui", "", $text);
		$this->log->add('$clearText = ', null, [$clearText]);

		if(
			# Если есть рифма
			in_array(mb_strtolower($clearText), array_keys($this->rhyme))
		)
			return $this->rhyme[mb_strtolower($clearText)];
		# Если нет букв
		elseif(!strlen($clearText))
			return null;

		elseif(
			# Отключаем на женских именах _beta
			($fl = mb_substr($text, 0, 1))
			&& mb_strtoupper($fl) === $fl
			&& in_array(mb_substr($text, -1), $this->glas)

			# Отключаем на исключениях
			|| in_array($this->message['from']['id'], self::$exclude_id)
		)
			return null;

		# Коверкаем очищенный текст
		$posLetter = CommonBot::stripos_array($clearText, $this->glas);
		$fLetter = mb_strtolower(mb_substr($clearText, $posLetter, 1));

		// $this->log->add('$posLetter = ', null, [ $posLetter, ]);

		$repl = $this->replace[$fLetter];

		$rest = mb_substr($clearText, $posLetter);

		// $this->log->add("1 Гласная = " . CommonBot::stripos_array($rest, $this->glas));

		while (CommonBot::stripos_array($rest, $this->glas) === 0) {
			// $this->log->add("1 Гласная = " . CommonBot::stripos_array($rest, $this->glas));

			# Удаляем дублирование
			$rest = preg_replace("/^.(ху)?/iu", '', $rest);
		}


		$result = "$repl$rest!";

		$this->log->add(' $posLetter, $fLetter, $repl, $rest, $result, = ', null, [ $posLetter, $fLetter, $repl, $rest, $result, ]);

		return $result;
	}


	public function __destruct()
	{

	}

} // KffBatonBot

new KffBatonBot;