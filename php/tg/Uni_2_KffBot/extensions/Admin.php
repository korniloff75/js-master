<?php

class Admin extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../'.__CLASS__,
		BASE = self::FOLDER . '/base.json';


	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)
			->init()
			->routerCmd();
		// $this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveCurData();

	} //* __construct


	private function init()
	{
		// $this->getCurData();

		//* Ждем данные
		if(!empty($this->statement['wait data']))
		{
			//* Отправляем на приём данных
			$this->inputDataRouter($this->statement['dataName']);
			$this->UKB->setStatement([
				'wait data'=>0,
			]);
			die;
		}

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		// $o = parent::routerCmd($cmd) ?? [];

		if(!$cmd)
			$cmd = &$this->cmd[0];
		$opts = &$this->cmd[1];


		$o = method_exists(__CLASS__, $cmd)
		? $this->{$cmd}($opts) : [];

		if(!empty($this->cbn['reply_to_message']))
		{
			//* Сообщение является ответом
			$this->setComplaint();
		}
		else
		{
			$this->getAdmins();
			die;
		}

		if(count($o))
		{
			$this->send($o);
		}
		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

		return $this;
	}


	private function getAdmins($show=1)
	{
		$adms= $this->apiRequest([
			'chat_id'=> $this->chat_id
		], 'getChatAdministrators');

		// $this->log->add(__METHOD__.' inputData=',null,[$this->inputData, $adms]);

		if(!is_array($adms)) die;

		if($show)
		{
			$txt= "<u>Администраторы чата:</u>\n";

			foreach($adms as &$adm)
			{
				$txt.= "{$adm['status']} - " . $this->showUsername($adm['user'],'tag') . "\n";
			}

			$this->apiResponseJSON([
				'chat_id'=> $this->chat_id,
				'text'=> $txt
			]);
		}

		return $adms;
	}


	//*
	private function setComplaint()
	{
		$adms= $this->getAdmins(false);

		$reply= &$this->cbn['reply_to_message'];

		$complaint= "Жалоба от "
		. $this->showUsername($this->cbn)
		. "\nНа " . $this->showUsername($reply)
		. "\nЗа пост:\n\n"
		. $reply['text'];

		foreach($adms as &$adm)
		{
			$this->apiRequest([
				'chat_id'=> $adm['user']['id'],
				'text'=> $complaint,
			]);
		}

		/* $this->apiRequest([
			'chat_id'=> $this->chat_id,
			'message_id'=> $this->cbn['message_id'],
		], 'deleteMessage'); */
		die;
	}


	//* Приём и сохранение данных
	private function inputDataRouter($dataName)
	{
		$this->log->add(__METHOD__.' $this->message,$dataName',null,[$this->message,$dataName]);

		$txt= trim($this->message['text']);

		if(method_exists(__CLASS__, "w_$dataName"))
		{
			$this->{"w_$dataName"}(explode("\n",$txt));
		}
		else
			$this->log->add(__METHOD__." method w_$dataName is FAIL",E_USER_WARNING);
	}


	//note wait data
	// deprecated
	private function w_getAdmins()
	{
		file_put_contents(self::FOLDER.'/inputJson.txt', $this->inputJson);
	}

	public function __destruct()
	{
		return null;
	}
} //* Admin
