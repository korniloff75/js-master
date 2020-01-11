<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

// ob_start();

# Для дочерних классов
interface iBotTG
{
	# protected $botFileInfo;
	// protected function init();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/Helper.php';


class TG {
	protected
		# Test mode, bool
		$__test = 0 ,
		$inputJson = null,
		$token,
		$api,
		$headers = ["Content-Type:multipart/form-data"],
		/* $proxyList = [
			"http://proxy-nossl.antizapret.prostovpn.org:29976",
			"http://CCAHIHA.antizapret.prostovpn.org:3128",
			"http://190.242.119.194:3128",
			"http://89.165.218.82:47886",
			"https://118.140.150.74:3128",
			"http://185.186.77.244:80",
			"http://88.199.21.75:80",
			"socks5://139.99.104.233:15895",
			"socks4://89.190.120.116:52941",
		], */
		# findAnzProxy()
		$proxy,
		# define in child classes
		$botFileInfo,
		$log, # instanceof Logger

		$botDir,
		# take object message
		$message,
		$inputData = null,
		$cbn,
		$chat_id;


	protected static
		$proxyPath = __DIR__ . '/Common/db.proxy',
		$textLimit = 3900;


	public function __construct($token=null)
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
			}
			$this->log = new Logger($file ?? 'tg.class.log', $path ?? __DIR__);
		}
		$this->log->add("tg.class.php started");

		$this->token = $token ?? $this->token;
		if(!is_string($this->token))
		{
			$this->log->add("There is no TOKEN from child class to continue execution!", E_USER_WARNING);
			$this->__destruct();
			die();
		}

		if($this->botFileInfo)
		{
			# Relative from root
			$this->botDir = $this->botFileInfo->getPathInfo()->fromRoot();

			$this->log->add("\$this->botDir = {$this->botDir}");
		}

		$this->api = "https://api.telegram.org/bot{$this->token}/";

		$this->log->add("Init bot.class.php");

		if(
			!$this->proxy = $this->findAnzProxy()
		)
		{
			$this->log->add("Available proxy NOT found!", E_USER_WARNING);
			die('Прокси не найден!');
		}

		# Обрабатываем входящие данные
		$this->message = $this->webHook()->findCallback();
		return $this;
	} // __construct


	public function getData()
	{
		$this->log->add("getData() started = " . (is_null($this->inputData) ? 'TRUE' : 'FALSE'));
		if(is_null($this->inputData))
		{
			# Ловим входящий поток
			$this->inputJson = file_get_contents('php://input');

			# Кодируем в массив
			$this->inputData = @json_decode($this->inputJson, true) ?? false;

		}

		return $this;
	} // getData


	/**
	 * returned current callback array || false
	 */
	public function findCallback()
	{
		if(!$this->getData()->inputData)
		{
			$this->log->add("inputData is EMPTY!", E_USER_WARNING, [$this->inputData]);
			return null;
		}

		$cbn = array_values(
			array_intersect(['message', 'channel_post', 'inline_query', 'callback_query', 'result'], array_keys($this->inputData))
		)[0] ?? false;

		$this->cbn = $this->inputData[$cbn];

		switch ($cbn) {
			case 'message':
			case 'channel_post':
			case 'inline_query':
				$cb = $this->cbn;
				break;
			case 'callback_query':
				$cb = $this->inputData['callback_query']['message'];
			break;

			default:
				$cb = $this->inputData;
				break;
		}

		if($cbn === 'inline_query')
		{
			$this->chat_id = $cb['from']['id'];
			$cb['text'] = $this->text = $cb['query'];
			$cb['chat'] = $cb['from'];
		}
		else
		{
			$this->chat_id = $cb['chat']['id'];
			$this->text = $cb['text'];
		}

		$this->log->add("
		\$cbn = $cbn\n
		\$this->chat_id = {$this->chat_id}\n\$this->cbn= ", null, [$this->cbn]);

		return $cb;

	} // findCallback


	/**
	 * Singl
	 */
	protected function webHook()
	{
		# path to bot is incorrect
		if(!$this->botFileInfo || !file_exists($this->botFileInfo->getRealPath()))
		{
			// $this->__destruct();
			$this->log->add("\$botURL is NOT exist! - ", E_USER_WARNING, $botURL);
			return $this;
		}

		# Full URI
		$botURL = \BASE_URL . $this->botDir . '/' . $this->botFileInfo->getBaseName();
		$trigger = \HOME . $this->botDir . "/webHookRegistered.trigger";

		# Однократно запускаем webHook
		if(file_exists($trigger))
		{
			$this->log->add("Webhook уже зарегистрирован.\nEND of webHook.", E_USER_WARNING);
		}
		else
		{
			$responseSetWebhook = $this->apiRequest([
				'url' => $botURL,
				'parse_mode' => null,
			], 'setWebhook') ?? [];

			$this->log->add("\$botURL = {$botURL}");
			$this->log->add("response after setWebhook", null, [$responseSetWebhook]);

			if(
				$responseSetWebhook
				&& file_put_contents($trigger, json_encode($responseSetWebhook, JSON_UNESCAPED_UNICODE))
			)
			$this->log->add("Был создан файл - $trigger", E_USER_WARNING);
		}

		return $this;
	} // webHook


	public function browsEmul()
	{
		return [
			// "Content-Type: application/json; charset=utf-8",
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Encoding: gzip, deflate',
			'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
			'Cache-Control: no-cache',
			'Connection: keep-alive',
			'DNT: 1',
			'Host: kfftg.22web.org',
			'Pragma: no-cache',
			"Cookie: __test=4dff14837c8240634dc1565329ba5274",
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:68.0) Gecko/20100101 Firefox/68.0',
		];

	}

	/* public function findProxy()
	{
		$timeoutInSeconds = 1.5;

		foreach($this->proxyList as $proxy) {
			$p = parse_url($proxy);
			// $p['scheme'].'://'.
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
			{
				$this->log->add("Proxy $proxy - is AVAILABLE\n");
				return $proxy; break;
			} else {
				$this->log->add("$proxy - ERROR: $errCode - $errStr", E_USER_WARNING);
			}
			// fclose($fp);
		}

		return false;
	} */

	/**
	 * new
	 */
	public function findAnzProxy(?string $proxy=null, bool $stop=false)
	{
		$timeoutInSeconds = 1;

		if(file_exists(self::$proxyPath))
		{
			$proxy = $proxy ?? file_get_contents(self::$proxyPath);

			$p = parse_url($proxy);
			// $p['scheme'].'://'.

			# Если прокси из файла доступен - возвращаем его
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
			{
				$this->log->add("Proxy $proxy - is AVAILABLE\n");
				return $proxy;
			}
			# Если недоступен - удаляем файл + рекурсия
			else
			{
				$this->log->add("$proxy - ERROR: $errCode - $errStr", E_USER_WARNING);
				unlink(self::$proxyPath);
				return $this->findAnzProxy(null, false);
			}
		}
		# Если нет файла
		else
		{
			if(
				# Если повторная рекурсия - тормозим
				!$stop
				# Ищем обновлённый прокси
				&& ($anz = file_get_contents('https://cloudflare-ipfs.com/ipns/pacipfs2.antizapret.prostovpn.org/proxy-nossl.js'))
				&& preg_match(
				"~return \"PROXY\s+(.+); DIRECT\";$~im", $anz, $proxy)
			)
			{
				$proxy = "http://{$proxy[1]}";
				file_put_contents(self::$proxyPath, $proxy);

				# Рекурсия с новым прокси
				return $this->findAnzProxy($proxy, true);
			}

		}
		# Полный провал
		return false;
	}


	# Выводим JSON по запросу от TG
	# Работает без proxy
	protected function apiResponseJSON(array $postFields = [], string $method = 'sendMessage')
	{
		if(headers_sent() || !$this->inputData)
		{
			$this->log->add("The headers were sent previously or not an external request. The request was made to TG.", E_USER_WARNING);
			return $this->apiRequest($postFields, $method);
		}

		$postFields["method"] = $method;
		$this->log->add('$postFields in ' . __METHOD__, null, [$postFields]);

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
		$ch = curl_init();

		$postFields = array_merge([
			// 'chat_id' => $chat_id,
			// 'text' => $text,
			'parse_mode' => 'html',
			'certificate' => '@' . realpath('/etc/ssl/certs/dhparam.pem'),

		], $postFields);

		// if(!strlen($postFields))

		foreach ($postFields as &$val) {
			# encoding to JSON not primitive parameters
			if (!is_numeric($val) && !is_string($val)) {
				$val = json_encode($val, JSON_UNESCAPED_UNICODE);
			}
		}

		$this->log->add("URL - {$this->api}$method\n\$postFields in " . __METHOD__, null, [$postFields]);

		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL => $this->api . $method,
				CURLOPT_HEADER => 0,

				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 30,

				CURLOPT_PROXY =>  $this->proxy, // Worked !
				CURLOPT_HTTPHEADER => $this->headers,
				// CURLOPT_SSL_VERIFYPEER => false,
				// CURLOPT_SSL_VERIFYHOST => false,

				CURLOPT_POSTFIELDS => $postFields,
			]
		);

		return $this->execCurl($ch);
	} // apiRequest


	function execCurl($ch) {
		$response = curl_exec($ch);

		if ($response === false) {
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			$this->log->add("Curl returned error $errno: $error", E_USER_WARNING);
			curl_close($ch);

			return false;
		}

		$http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		curl_close($ch);

		$response = json_decode($response, true);

		if ($http_code >= 500) {
			// do not wat to DDOS server if something goes wrong
			sleep(5);
			return false;
		} else if ($http_code != 200) {
			$this->log->add("apiRequest has failed with error {$response['error_code']}: {$response['description']}", E_USER_WARNING);
			if ($http_code == 401) {
				$this->log->add('Invalid access token provided', E_USER_WARNING);
			}
			return false;
		} else {
			if (isset($response['description'])) {
				$this->log->add("apiRequest was SUCCESSFUL: {$response['description']}");
			}
			$response = $response['result'];
		}

		return $response;
	} // execCurl


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
	 * Wrapper 4 $this->apiRequest
	 */
	public function sendMessage(array $content)
	{
		$postFields = [
			'chat_id' => $this->chat_id,
			'parse_mode' => 'html',
			'disable_web_page_preview' => true,
		];

		# Делим на шины по self::$textLimit символов
		$bus = '';
		$diffLength = count($content);

		foreach($content as $i) {
			--$diffLength;
			# Разбиваем на строки фикс. размера
			if(strlen($bus) + strlen($i) < self::$textLimit)
			{
				$bus .= "$i\n\n\n";
				if($diffLength) continue;
			}

			if(!strlen(trim($bus)))
				continue;

			$postFields['text'] = $bus;

			if(class_exists('CommonBot'))
			{
				$postFields['reply_markup'] = $this->setInlineKeyboard([[
					# Row
					CommonBot::setAdvButton(),
					[
						"text" => "More",
						"callback_data" => '/more',
					],
				]]);
			}

			# Отправляем в канал.
			$respTG[]= $this->apiRequest($postFields);

			$bus = '';
			usleep(10);

		}

		# Test server response
		$this->log->add("\$respTG", null, [$respTG ?? null]);
	}

	/**
	 * @photos - https://core.telegram.org/bots/api#sendmediagroup
	 */
	public function sendMediaGroup(array $photos)
	{
		$this->log->add('$photos = ', null, [$photos]);

		if(count($photos))
			$photos = array_chunk($photos, 10);
		else return;

		foreach ($photos as $lim) {
			$this->apiRequest([
				'chat_id' => $this->message['chat']['id'],
				'media' => $lim,
			], 'sendMediaGroup');
			usleep(10);
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

		$this->log->add('EVALUATE __destruct');

		/* $this->bufferLog = ob_get_clean();
		file_put_contents($this->botFileInfo->getPath() . '/buffer.log', strip_tags($this->bufferLog)); */

		# Выводим логи
		// if($this->__test) $this->log->print();

	}

} // TG