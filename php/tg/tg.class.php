<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

//note FIX cron
if(php_sapi_name() === 'cli' && empty($_SERVER['DOCUMENT_ROOT']))
{
	$_SERVER = array_merge($_SERVER, [
		'DOCUMENT_ROOT' => realpath(__DIR__ . '/../..'),
	]);
}

# Для дочерних классов
interface iBotTG
{
	# protected $botFileInfo;
	// protected function init();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/core/traits/Get_set.trait.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/core/traits/Curl.trait.php";


class TG
{
	// todo- define in adm panel
	const
		HOST= 'https://js-master.ru',
		OWNER= 673976740;

	public
		$webHook = true;
		// $cron=[];

	protected
		# Test mode, bool
		$__test = 0 ,
		$tg_startTime,
		$inputJson = null,
		$inputData = null,
		$tokens = [],
		$api,
		$headers = ["Content-Type:multipart/form-data"],
		# findAnzProxy()
		$proxy,
		# define in child classes
		$botFileInfo,
		$log, //* instanceof Logger
		$cron=[], //* cron start

		$botDir,
		$botDirFromRoot,
		//* take object message
		$message,
		$cbn,
		$user_id,
		$chat_id,
		$is_group;


	protected static
		$allowedTags = '<pre><b><strong><i><em><u><ins><s><strike><del><code>',
		$textLimit = 3900;

	use Get_set {}

	private
		$is_owner = null;

	use Curl;

	public function __construct($token=null)
	{
		$this->tg_startTime= new DateTime;
		$this->checkLog();
		tolog("tg.class.php started");

		if($this->botFileInfo)
		{
			$this->botDir = $this->botFileInfo->getPath();

			if($this->botFileInfo instanceof kffFileInfo)
			{
				# Relative from root
				$this->botDirFromRoot = $this->botFileInfo->getPathInfo()->fromRoot();
			}

			tolog("\$this->botDirFromRoot = {$this->botDirFromRoot}\n\$this->botDir = {$this->botDir}");
		}

		if(!count($this->tokens))
			$this->getTokens(null, $token);

		$this->api = "https://api.telegram.org/bot{$this->tokens['tg']}/";

		tolog(basename(__FILE__) . ' inited');

		//* Обрабатываем входящие данные
		$this->message = $this->webHook()->findCallback();
		return $this;
	} // __construct


	private function checkLog()
	{
		if($this->log) return;

		//* Если не логируется из дочернего класса
		require_once $_SERVER['DOCUMENT_ROOT'] . "/core/classes/Logger.php";
		if($this->botFileInfo)
		{
			// $path = $this->botFileInfo->getPathname();
			$path = $this->botFileInfo->getPathInfo()->getRealPath();
			$file = $this->botFileInfo->getBasename() . '.log';
		}
		$this->log = new Logger($file ?? 'tg.class.log', $path ?? __DIR__);
		tolog(__METHOD__.' botFileInfo= ',null,[$this->botFileInfo]);
	}


	protected function getTokens($file= null, $token= null)
	{
		$this->checkLog();

		if(!$file && !empty($this->botFileInfo))
			$file = $this->botFileInfo->getPath() . "/token.json";

		// $file = $file ?? "{$this->botDirFromRoot}/token.json";
		$this->tokens = $token ? (
			['tg' => $token]
		) : (file_exists($file) ? json_decode(
			file_get_contents($file), true
		) : ['tg' => $this->token]);

		tolog(__METHOD__ . ' $this->tokens', null, [$this->tokens]);


		if(!is_string($this->tokens['tg']))
		{
			tolog(__METHOD__ . " There is no TOKEN from child class to continue execution!", E_USER_ERROR, ['$this->tokens'=>$this->tokens]);
			$this->__destruct();
			die();
		}
	}


	public function getInputData()
	{
		tolog("getInputData() started = " . (is_null($this->inputData) ? 'TRUE' : 'FALSE'));
		if(is_null($this->inputData))
		{
			# Ловим входящий поток
			$this->inputJson = file_get_contents('php://input');

			# Кодируем в массив
			$this->inputData = @json_decode($this->inputJson, true) ?? false;

		}

		return $this;
	} // getInputData


	/**
	 * returned current callback array || false
	 */
	public function findCallback()
	{
		if(!$this->getInputData()->inputData)
		{
			//* Проверяем cron
			if(empty($this->cron))
			{
				tolog("inputData is EMPTY!", E_USER_WARNING, [$this->inputData]);
				return null;
			}
			else
			{
				$this->inputData['cron'] = $this->cron;
				$this->webHook=0;
				tolog("inputData from \$this->cron", E_USER_WARNING, [$this->inputData]);
			}
		}

		$cbn = array_values(
			array_intersect(['message', 'channel_post', 'inline_query', 'callback_query', 'result', 'cron'], array_keys($this->inputData))
		)[0] ?? false;

		$this->cbn = $this->inputData[$cbn];
		$this->cbn['query_name'] = $cbn;

		switch ($cbn) {
			case 'message':
			case 'channel_post':
			case 'inline_query':
			case 'cron':
				$cb = &$this->cbn;
				break;
			case 'callback_query':
				$cb = &$this->inputData['callback_query']['message'];
			break;

			default:
				$cb = &$this->inputData;
				break;
		}

		if($cbn === 'inline_query')
		{
			$this->chat_id = $cb['from']['id'];
			$cb['text'] = $this->text = trim($cb['query']);
			$cb['chat'] = $cb['from'];
		}
		else
		{
			$this->chat_id = $cb['chat']['id'];
			$this->text = trim($cb['text']);
		}

		$this->is_group= !is_numeric(substr($this->chat_id,0,1));

		//* Определяем пользователя
		$this->user_id= $this->cbn['from']['id'];

		//* Определяем владельца скрипта
		$this->is_owner = $this->set('is_owner', $this->user_id == self::OWNER);

		tolog(__METHOD__.": \$cbn = $cbn", null, ['$this->chat_id'=>$this->chat_id, '$this->user_id'=>$this->user_id, '$this->is_group'=>$this->is_group, '$this->cbn'=>$this->cbn]);

		return $cb;
	} // findCallback


	/**
	 * Singl
	 */
	protected function webHook()
	{
		//* path to bot is incorrect
		if(
			!$this->botFileInfo
			|| !file_exists($this->botFileInfo->getRealPath())
			|| !$this->webHook
			|| empty($this->botDirFromRoot)
		)
		{
			// $this->__destruct();
			tolog(__METHOD__ . " aborted with FAIL", E_USER_WARNING, ['$this->webHook'=>$this->webHook]);
			return $this;
		}

		# Full URI
		$botURL = self::HOST . "/{$this->botDirFromRoot}/" . $this->botFileInfo->getBaseName();
		$trigger = $this->botDir . "/webHookRegistered.trigger";

		tolog("\$trigger= $trigger",null, [file_exists($trigger)]);

		# Однократно запускаем webHook
		if(file_exists($trigger))
		{
			tolog("Webhook уже зарегистрирован.\nEND of webHook.", E_USER_WARNING);
		}
		else
		{
			$responseSetWebhook = $this->apiRequest([
				'url' => $botURL,
				'parse_mode' => null,
				// 'allowed_updates' => true,
			], 'setWebhook') ?? [];

			tolog("\$botURL = {$botURL}");
			tolog("response after setWebhook", null, [$responseSetWebhook]);

			if(
				$responseSetWebhook
				&& file_put_contents($trigger, json_encode($responseSetWebhook, JSON_UNESCAPED_UNICODE))
			)
			tolog("Был создан файл - $trigger", E_USER_WARNING);
		}

		return $this;
	} // webHook


	/**
	** Выводим JSON по запросу от TG
	** Работает без proxy
	*/
	protected function apiResponseJSON(array $postFields = [], string $method = 'sendMessage')
	{
		if(headers_sent() || !$this->inputData)
		{
			tolog("The headers were sent previously or not an external request. The request was made to TG.", E_USER_WARNING);
			return $this->apiRequest($postFields, $method);
		}

		$this->checkSendData($postFields);

		$postFields["method"] = $method;
		tolog('$postFields in ' . __METHOD__, null, [$postFields]);

		ob_start();
		header("Content-Type: application/json");
		echo json_encode($postFields, JSON_UNESCAPED_UNICODE);
		return ob_end_flush();
	}


	/**
	 * Make apiRequest to $this->api
	 */
	public  function apiRequest(array $postFields = [], string $method = 'sendMessage')
	{
		$this->checkSendData($postFields);

		tolog("URL - {$this->api}$method\n\$postFields in " . __METHOD__, null, ['$postFields'=>$postFields]);

		//* Выполняем Curl
		// $response = $this->CurlRequestProxy($this->api . $method, [
		// note Отключаем прокси
		$response = $this->CurlRequest($this->api . $method, [
			'sendMethod' => 'post',
			'headers' => $this->headers,
			'params' => $postFields
		]);

		//* Обрабатываем результаты
		return $this->apiExecCurl($response);
	} //* apiRequest


	//* Проверяем данные
	protected function checkSendData(&$o)
	{
		//* add keyboard options
		if(
			!empty($o['reply_markup']['keyboard'])
			&& empty($o['reply_markup']['resize_keyboard'])
		)
		{
			$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
		}

		//* Склеиваем текст
		if(is_array($o['text']))
		{
			$o['text'] = implode("\n\n", $o['text']);
		}

		$o = array_merge([
			'chat_id' => $this->message['chat']['id'],
			// 'text' => $text,
			'parse_mode' => 'html',
			'certificate' => '@' . realpath('/etc/ssl/certs/dhparam.pem'),

		], $o);
	}


	/**
	 ** Исполнение Curl, вывод и логирование результатов
	 */
	private function apiExecCurl($response)
	{
		if(
			!$response
			|| empty($this->curlInfo)
		)
		{
			tolog(__METHOD__ . ' $response = ', null, $response);
			return $response;
		}

		//* $this->curlInfo - определяется в $this->execCurl
		$http_code = intval($this->curlInfo['http_code']);

		if ($http_code != 200)
		{
			tolog(__METHOD__ . " has failed with error {$response['error_code']}: {$response['description']}", E_USER_WARNING);
			if ($http_code == 401)
			{
				tolog('Invalid access token provided', E_USER_WARNING);
			}
			return false;
		} else {
			if (isset($response['description']))
			{
				tolog(__METHOD__ . " was SUCCESSFUL: {$response['description']}");
				usleep(10);
			}
			return $response['result'];
		}
	} // apiExecCurl


	public function setKeyboard($data)
	{
	 $keyboard = [
		 "keyboard" => $data,
		 "one_time_keyboard" => false,
		 "resize_keyboard" => true
	 ];
	 return json_encode($keyboard);
	}

	public function setInlineKeyboard(array $data)
	: string
	{
		return json_encode( [
		 "inline_keyboard" => $data,
		], JSON_UNESCAPED_UNICODE);
	}


	/**
	 ** Wrapper 4 $this->apiRequest
	 * @param content - array with content strings
	 **optional:
	 * @param postFields - array with custom send data
	 * @param break - break between messages in bus
	 *
	 * Проверяет длину каждого элемента из @content
	 * Если превышает лимит - создаёт массив из строк и передаёт в рекурсию
	 * Если нет - собирает шину элементов до лимита и отправляет в ТГ
	 * Почему не отсылается последний элемент?
	 */
	public function sendMessage(array &$content, array $postFields= [], string $break="\n\n")
	{
		$postFields[] = array_merge([
			'disable_web_page_preview' => true,
		], $postFields);

		//* Делим на шины по self::$textLimit символов
		$bus = '';
		$diffLength = count($content);

		foreach($content as $i) {
			--$diffLength;
			if(empty(trim($i)))
				continue;
			// *Если один элемент больше лимита
			if(strlen($i) > self::$textLimit)
			{
				$strContent = array_filter(explode("\n", $i));
				if(count($strContent) > 1)
				{
					$this->sendMessage($strContent, $postFields, "\n");
					continue;
				}
			}

			// * Разбиваем на строки фикс. размера
			elseif((strlen($bus) + strlen($i)) < self::$textLimit)
			{
				$bus .= "{$i}{$break}";
				if($diffLength) continue;
			}

			$postFields['text'] = strip_tags($bus, self::$allowedTags);

			//* Отправляем в канал.
			$respTG[]= $this->apiRequest($postFields);

			$bus = "{$i}{$break}";

		}

		# Test server response
		tolog("\$respTG", null, [$respTG ?? null]);
	}

	/**
	 * @param media - https://core.telegram.org/bots/api#sendmediagroup
	 */
	public function sendMediaGroup(array $media)
	{
		tolog(__METHOD__ . 'count($media) = ', null, [count($media)]);

		if(count($media))
			$media = array_chunk($media, 10);
		else return;

		foreach ($media as $lim) {
			$this->apiRequest([
				'chat_id' => $this->message['chat']['id'],
				'media' => $lim,
			], 'sendMediaGroup');
		}

	}

	/**
	 * В разработке
	 */
	function sendPhoto(string $path)
	{
		return $this->apiRequest([
			'chat_id' => $this->chat_id,
			'photo' => new CURLFile(realpath($path))
		], 'sendPhoto');

	}


	public function __destruct()
	{
		// if(!$this->__test) return;

		tolog(__METHOD__.' EVALUATE');

		# Выводим логи
		// if($this->__test) $this->log->print();

	}

} // TG