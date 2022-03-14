<?php
// *Autoload
if(!class_exists('Path')){
	spl_autoload_register(function ($class)
	{
		$parts = explode('\\', $class);
		// tolog(__METHOD__,null,$parts);

		$className = end($parts);
		if(file_exists($path= $_SERVER['DOCUMENT_ROOT']."/core/classes/$className.php")){
			include_once $path;
		}
	});
}


require_once __DIR__ . "/../tg.class.php";



class jsMaster extends TG
{

	public function __construct()
	{
		# Set local data
		$this->botFileInfo = new kffFileInfo(__FILE__);

		# Запускаем скрипт
		parent::__construct()
			->init();

	} //__construct


	private function init()
	{
		// file_put_contents('test.log.json', $this->inputJson, LOCK_EX);

		//* Завершаем скрипт без входящего JSON
		if(
			empty($this->inputData)
			|| $this->is_group
		) die;

		tolog(__METHOD__,null,['$this->text'=>$this->text]);

		//* Intro
		if($this->text === '/start')
		{
			$this->_intro();
			die;
		}

		//* Если ответ
		if(!empty($reply= &$this->cbn['reply_to_message']))
		{
			// *I do debug
			if($this->is_owner && empty($reply['forward_from']))
			{
				$chat_id= $reply['chat']['id'];
			}
			else $chat_id= ($reply['forward_from'] ?? $reply['chat'])['id'];
		}
		else
		{
			$chat_id= $this->cbn['from']['id'];
		}

		tolog(__METHOD__,null,['$reply'=>$reply]);

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

		die('OK');
	} // init


	private function _showUsername(array &$user, $tag=null)
	{
		$arr= $user['from'] ?? $user;
		return "<b>"
		. ($arr['realName'] ?? $arr['first_name'])
		. ($arr['last_name'] ?? '')
		. "</b> " . ($tag?'@':'')
		. "{$arr['username']} ({$arr['id']})\n";
	}


	private function _intro()
	{
		$from = &$this->cbn['from'];

		$this->apiResponseJSON([
			'chat_id'=> $from['id'],
			// 'chat_id'=> $reply['from']['id'],
			'text'=> "Привет, " . $this->_showUsername($from, 'tag') . "!\n\nЕсли можно, давай перейдём сразу к делу. На сообщения из серии \"Привет, как дела?\" я могу не найти времени ответить. <a href=\"https://neprivet.ru\">Подробнее</a>\n\nЕсли сообщение по делу, но я сразу не ответил, пожалуйста, подожди. Возможно, меня просто сейчас нет в сети.\n\nСпасибо за понимание.\n=======\n\nПо кнопке под этим сообщением ты можешь, если есть возможность, оказать материальную поддержку моим бесплатным проектам, например:\n\nhttps://t.me/CrimeanNewss\nhttps://t.me/SportTimeNews\nhttps://t.me/smiles_me",
			'reply_markup'=> ['inline_keyboard'=>[
				[['text'=>'Поддержать бесплатные проекты', 'url'=>'https://sobe.ru/na/tg_bots_hosting']]
			]],
		]);
	}
}

new jsMaster;