<?php
if(!defined('DR')) define('DR', realpath(__DIR__ . '/../..'));

//* FIX cron
if(php_sapi_name() === 'cli' && empty($_SERVER['DOCUMENT_ROOT'])){
	$_SERVER = array_merge($_SERVER, [
		'DOCUMENT_ROOT' => DR,
	]);
}

if(!class_exists('Logger')){
	// *Autoload
	spl_autoload_register(function ($class)
	{
		$parts = explode('\\', $class);
		// tolog(__METHOD__,null,$parts);

		$className = end($parts);
		if(file_exists($path= \DR."/core/classes/$className.php")){
			include_once $path;
		}
	});
}

# TG
require_once __DIR__ . "/tg.class.php";
// require_once $_SERVER['DOCUMENT_ROOT'] . "/php/traits/Get_set.trait.php";


class CommonBot extends TG
{
	/* use Get_set {}

	private
		$is_owner = null; */
	static $protecText = "Вы пытаетесь воспользоваться частным ботом.\nДля его разблокировки свяжитесь с автором <b>@js_master_bot</b>",
		$noUdatesText = "Обновлений пока нет. Попробуйте позже.";

	protected
		// $is_owner = false,
		$responseData,
		$objLicense = [],
		$savedBase = [],
		//* from tg.class.php
		$botDir;

	public function __construct()
	{
		parent::__construct();
		$GLOBALS['_bot'] = &$this;

		//* Определяем владельца скрипта
		// $this->is_owner = $this->set('is_owner', $this->user_id == self::OWNER);

		if(!empty($this->cron))
		{
			// var_dump($this->cron, $this->cbn);
			// echo "\$this->user_id = " . $this->user_id;
			// $this->is_owner = 1;
		}

		$this->responseData = [
			'chat_id' => $this->chat_id,
			'parse_mode' => 'html',
		];

		//* Отсекаем inline
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
			// require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/Logger.php";

			$this->log = new Logger($logFile ?? (__CLASS__.'.log'), $this->botDir);
		}

		return $this;
	} // init


	/**
	 * REQUIRES
	 * array child::license = [
	 * 	chat_id => "2025-04-07", [<string term>,<string name>, optional<bool is_blocked>] ...
	 * ]
	 */
	protected function checkLicense($responseData = null, $user_data=[])
	{
		$user_data = array_merge([
			'botProtect'=>true,
			'condition'=>false
		], $user_data);

		$this->addUserLicense($user_data);

		// $this->log->add(__METHOD__.' $this->license',null,[$this->license]);

		array_walk($this->license, function(&$data,$id){
			if(!is_numeric($id))
				return;

			if(is_array($data))
			{
				$data['term'] = &$data[0];
				$data['name'] = &$data[1];
				$data['blocked'] = &$data[2];
				// list($data['term'], $data['name'], $data['blocked']) = $data;
			}
			else
			{
				$data = ['term'=>$data];
			}

			/* $this->log->add("\$data",null,[
				$data,
				$data['term'],
				(new DateTime() < new DateTime($data['term'])),
				new DateTime(), new DateTime($data['term'])
			]); */

			if (
				//* Remove olds
				new DateTime() > new DateTime($data['term'])
			) $data['blocked']= 1;
		}); //walk

		/* $this->log->add(__METHOD__." $this->botDir/license.json ===", null, [
			$this->message['chat']['id'],
			$this->license,
		]); */

		// todo
		if(
			// && !$this->is_owner
			$this->message
			&& ($id = $this->message['chat']['id'])
			&& (
				!array_key_exists($id, $this->license)
				|| !empty($this->license[$id]['blocked'])
				// || new DateTime() > new DateTime($this->license[$id]['term'])
			)
		)
		{
			if(
				!$user_data['botProtect'] && !$this->is_group
			)
			{
				$this->log->add(__METHOD__." \$user_data['botProtect'] ===", null, [
					$user_data['botProtect'],
				]);
				return $this;
			}

			$responseData = $responseData ?? $this->responseData;
			$responseData['text'] = self::$protecText;
			$responseData['disable_web_page_preview'] = false;
			$this->apiResponseJSON($responseData);

			file_put_contents(
				"{$this->botDir}/plagiarism_base.txt",
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
	 ** Добавляем запись в лицензию
	 */
	private function addUserLicense($user_data=[])
	{
		if(
			empty($this->license)
			&& ($this->objLicense = new DbJSON("{$this->botDir}/license.json"))
		)
		{
			$this->license = $this->objLicense->get();
			// $this->license = json_decode($license,1);
		}

		if(
			empty($user_data)
			|| !$user_data['condition']
			|| array_key_exists($this->user_id, $this->license)
		) return;

		$this->objLicense->set([$this->user_id=> [
			$user_data['term'] ?? "3000-01-01",
			"{$this->message['from']['first_name']} "
			. ($this->message['from']['last_name']??'')
			. " {$this->message['from']['username']}"
		]]);

	}


	/**
	 ** Кнопка с рекламой
	 */
	public static function setAdvButton()
	{
		$_adv= new DbJSON(__DIR__ . '/Common/Adv.json');
		# Advert
		if(!count($db= $_adv->get()))
		{
			$this->log->add('realpath Common/Adv.json = ' . realpath(__DIR__ . '/Common/Adv.json'), E_USER_WARNING, ['__DIR__'=>__DIR__, '$db'=>$db]);
			return false;
		}

		$text = array_keys($db);
		shuffle($text);

		return [
			"text" => $text[0],
			"url" => $db[$text[0]],
		];
	}


	/**
	 * @param haystack
	 * @param needles {string|array}
	 * @param posArr service
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


	//* Общая рассылка
	protected function sendToAll($o)
	{
		$this->checkSendData($o);
		$txt = str_replace(
			['!','синий','рыжий'],
			['❗️','синий🔷','рыжий🔶'],
			$txt
		);

		$o['text']= "❗️❗️❗️\n{$o['text']}";

		foreach($this->license as $id=>$data)
		{
			if(!empty($data['blocked']) || !is_numeric($id))
				continue;

			$o['chat_id']= $id;
			$this->apiRequest($o);
		}
	}

	//* Рассылка в зарегистрированные чаты
	protected function sendToChats($o)
	{
		$this->checkSendData($o);
		$o['text']= "❗️❗️❗️\n{$o['text']}";

		//* Отсылаем в бот
		$o['chat_id']= $this->user_id;
		$this->apiResponseJSON($o);

		if(empty(static::CHATS))
		{
			return;
		}

		if(!empty($o['reply_markup']['keyboard']))
		{
			$keyboard= &$o['reply_markup']['keyboard'];
			unset($keyboard);
		}

		foreach(static::CHATS as $id)
		{
			if(!is_numeric($id))
				continue;

			$o['chat_id']= $id;
			$this->apiRequest($o);
		}
	}


	public function __destruct()
	{
		if( !empty($this->objLicense->change) )
		{
			array_walk($this->license, function(&$data,$id){
				$data= [$data['term'],$data['name'],$data['blocked']];
			});
		}

		// $this->log->add(__METHOD__,null,$this->license);

	}
} // CommonBot
