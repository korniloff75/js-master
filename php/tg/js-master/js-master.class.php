<?php
/**
 * https://imakebots.ru/article/delaem-bot-dlya-obratnoy-svyazi-v-telegram
 */

require_once __DIR__ . "/../CommonBot.class.php";

class jsMaster extends CommonBot
{

	public function __construct()
	{
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		parent::__construct()->init();

	} //__construct


	private function init()
	{
		//* Завершаем скрипт без входящего JSON
		if(
			empty($this->inputData)
		) die ('Нет входящего запроса');

		if($this->is_group) die;

		$this->log->add(__METHOD__.' $this->text',null,[$this->text]);

		/* $recipient= $this->user_id == self::OWNER
		?  */

		// file_put_contents('test.log.json', $this->inputJson, LOCK_EX);

		//* Intro
		if($this->text === '/start')
		{
			$from = &$this->cbn['from'];

			$this->apiResponseJSON([
				'chat_id'=> $from['id'],
				// 'chat_id'=> $reply['from']['id'],
				'text'=> "Привет, " . $this->showUsername($from, 'tag') . "!\n\nЕсли можно, давай без прелюдий, переходи сразу к делу. На сообщения из серии \"Привет, как дела?\" я могу не найти времени ответить.\n\nЕсли сообщение по делу, но я сразу не ответил, пожалуйста, подожди. Возможно, меня просто сейчас нет в сети. Спасибо за понимание.",
			]);
			die;
		}

		//* Если ответ
		if(!empty($reply= &$this->cbn['reply_to_message']))
		{
			$chat_id= $reply['forward_from']['id'];
		}
		else
		{
			$chat_id= $this->cbn['from']['id'];
		}

		//* Пишу я
		if($this->is_owner)
		{
			$this->apiResponseJSON([
				'chat_id'=> $chat_id,
				// 'chat_id'=> $reply['from']['id'],
				'text'=> $this->text,
			], 'sendMessage');
		}
		//* Пишут мне
		else
		{
			$this->apiResponseJSON([
				'chat_id'=> self::OWNER,
				'from_chat_id'=> $this->cbn['from']['id'],
				'message_id'=> $this->cbn['message_id'],
			], 'forwardMessage');
		}


		/* $this->apiResponseJSON([
			'chat_id'=> $this->cbn['data'] ?? self::OWNER,
			'text'=> $this->text,
			'reply_markup' => ["inline_keyboard" => [[
				[
					'text' => "Ответить",
					'callback_data'=> $this->user_id
				],
			],],]
		]); */

		die('OK');

	} // init


	private function showUsername(array &$user, $tag=null)
	{
		$arr= $user['from'] ?? $user;
		return "<b>"
		. ($arr['realName'] ?? $arr['first_name'])
		. ($arr['last_name'] ?? '')
		. "</b> " . ($tag?'@':'')
		. "{$arr['username']} ({$arr['id']})\n";
	}
}

new jsMaster;