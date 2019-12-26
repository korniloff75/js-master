<style>
pre {
	box-sizing: border-box;
	white-space: pre-wrap;
	border: inset 1px #eee;
}
</style>

<pre>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);


interface iTG
{
	public function init();
}


class TG {
	protected
		# Test mode, bool
		$__test = 0 ,
		$inputJson = null,
		$token,
		$api,
		$headers = [],
		$proxyList = [
			"socks4://89.190.120.116:52941",
			"socks4://188.93.238.17:51565",
			"socks4://159.224.226.164:40856",
			"http://190.242.119.194:3128",
			"http://89.165.218.82:47886",
			"http://183.91.33.41:91",
			"http://183.91.33.41:8081",
		],
		# define in child classes
		$botDir,
		$botURL,
		# define callback
		$callback,
		$inputData = null,
		$chat_id;

	static
		$log=[];

	protected static
		$textLimit = 3500;


	public function __construct($token=null)
	{
		$this->token = $this->token ?? $token;
		if(!is_string($this->token))
			throw new Exception("\$token must be string!", 1);

		$this->api = "https://api.telegram.org/bot{$this->token}/";
		// var_dump($this->api);

		# Обрабатываем входящие данные
		$this->callback = $this->findCallback();
		return $this;
	}


	/**
	 * TG::log([], __FILE__, __LINE__);
	 */
	public static function log($data=[], $file='', $line='')
	{
		ob_start();
		echo '<h4>' . basename($file) . " : $line</h4>\n";
		foreach($data as $i) {
			eval($i . ';');
			echo "\n===\n ";
		}
		// $log= $this->__test ? ob_get_flush() : ob_get_clean();
		self::$log[]= ob_get_clean();

		// self::$log[]= $log;

	} // log


	public function getData()
	{
		if(is_null($this->inputData))
		{
			# Ловим входящий поток
			$this->inputJson = file_get_contents('php://input');

			# Кодируем в массив
			$this->inputData = @json_decode($this->inputJson, true);

			if(strlen($this->inputJson)) file_put_contents('inputData.json', $this->inputJson);
			else self::log(['echo "Нет callback!\n"',], __FILE__, __LINE__);
		}

		return $this;
	} // getData


	public function getCallback($key)
	{
		if(!$this->getData()->inputData) return;

		return array_key_exists($key, $this->inputData) ? $this->inputData[$key] : false;

	}


	/**
	 * returned current callback array || false
	 */
	public function findCallback()
	{
		if(!$this->getData()->inputData) return;
		$cbn = array_values(
			array_intersect(['message', 'callback_query'], array_keys($this->inputData))
		)[0] ?? false;

		self::$log[]= basename(__FILE__) . ': ' . __LINE__ . " - \$cbn= " . $cbn. "\n\n";

		$cb = $this->getCallback($cbn);

		switch ($cbn) {
			case 'message':
				$this->chat_id = $cb['chat']['id'];
				$this->text = $cb['text'];
				break;
			case 'callback_query':
				$this->chat_id = $cb['message']['chat']['id'];
				$this->text = $cb['message']['text'];
				break;

			default:
				$this->chat_id = null;
				break;
		}

		self::$log[]= "\$this->chat_id = {$this->chat_id}\n";

		return $cb;

	}


	/**
	 * Singl
	 */
	protected function webHook()
	{
		# Однократно запускаем webHook
		$trigger = \HOME . $this->botDir . "/webHookRegistered.trigger";
		if(file_exists($trigger))
		{
			echo "<pre>Webhook уже зарегистрирован\n";
		}
		else
		{
			$responseSetWebhook = $this->request([
				'url' => $this->botURL,
				'parse_mode' => null,
			], 'setWebhook') ?? [];

			echo __FILE__ . __LINE__ . " - \$this->url = {$this->botURL}\n";
			echo "response after setWebhook = ";
			var_dump($responseSetWebhook);


			if(count($responseSetWebhook))
				file_put_contents($trigger, json_encode($responseSetWebhook, JSON_UNESCAPED_UNICODE));
		}

		echo __FILE__ . __LINE__ . "\n";
		echo "\$trigger = $trigger\n";
		echo "END of webHook\n\n";
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

	public function findProxy()
	{
		$timeoutInSeconds = 1.5;

		foreach($this->proxyList as $proxy) {
			$p = parse_url($proxy);
			// $p['scheme'].'://'.
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
			{
				self::$log[]= __FILE__.': '.__LINE__ . " Proxy $proxy - is AVAILABLE\n";
				return $proxy; break;
			} else {
				self::$log[]= __FILE__.': '.__LINE__ . " $proxy - ERROR: $errCode - $errStr\n";
			}
			// fclose($fp);
		}

		return false;
	}


	/**
	 * Make request to $this->api
	 */
	public  function request(array $postFields = [], string $method = 'sendMessage')
	{
		# Find available proxy from proxyList
		if (!$proxy = $this->findProxy())
		{
			self::$log[]= __FILE__.': '.__LINE__ . " Available proxy NOT found!\n";
			return;
		}

		$ch = curl_init();

		$postFields = array_merge([
			// 'chat_id' => $chat_id,
			// 'text' => $text,
			'parse_mode' => 'html',
			'certificate' => '@' . realpath('/etc/ssl/certs/dhparam.pem'),

		], $postFields);

		if($this->__test) {
			echo '$postFields = ';
			var_dump($postFields);
			echo 'URL - ' . $this->api . $method . "\n";
		}

		// echo "Content: \n" . file_get_contents('http://kfftg.22web.org/') . '<hr>';

		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL => $this->api . $method,
				// CURLOPT_URL => "https://api.telegram.org/bot{$this->token}/{$method}",
				CURLOPT_HEADER => 0,

				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 30,

				// CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6,
				// CURLOPT_INTERFACE => '2001:67c:4e8:f004::9',

				CURLOPT_PROXY =>  $proxy, // Worked !
				// CURLOPT_PROXY =>  "http://190.242.119.194:3128", // Worked !
				/* CURLOPT_COOKIESESSION => true,
				CURLOPT_COOKIEJAR => 'cookies',
				CURLOPT_COOKIEFILE => is_file('cookies') ? 'cookies' : '', */
				CURLOPT_HTTPHEADER => $this->headers,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,

				CURLOPT_POSTFIELDS => $postFields,
			]
		);

		// $r = gzdecode(curl_exec($ch));
		$r = curl_exec($ch);

		// Проверяем наличие ошибок
		if($errno = curl_errno($ch)) {
			$error_message = curl_strerror($errno);
			echo "<b>cURL error ({$errno}):</b>\n {$error_message}";
		}

		if($this->__test) {
			// echo "response = " . gzdecode($r) . "<hr>";
			var_dump($r, curl_getinfo($ch));
			echo '<hr>';
		}

		// Закрываем дескриптор
		curl_close($ch);

		return json_decode($r, true);
	}


	public function getKeyboard($data)
	{
	 $keyboard = [
		 "keyboard" => $data,
		 "one_time_keyboard" => false,
		 "resize_keyboard" => true
	 ];
	 return json_encode($keyboard);
	}

	// not use
	public function getInlineKeyboard(array $data)
	: string
	{
		return json_encode( [
		 "inline_keyboard" => $data,
		], JSON_UNESCAPED_UNICODE);
	}


	/**
	 * В разработке
	 */
	function sendPhoto($chat_id, $url) {

		$this->headers = ["Content-Type:multipart/form-data"];

		file_put_contents(basename($url), file_get_contents($url));

		return $this->request([
			'chat_id' => $chat_id,
			'photo' => new CURLFile(realpath("image.jpg"))
		], 'sendPhoto');

	}


	public function __destruct()
	{
		if(!$this->__test) return;

		self::log(['echo "EVALUATE __destruct"'], __FILE__, __LINE__);

		# Выводим логи
		foreach(self::$log as $log) {
			print_r ("<pre class='log' style='max-height: 200px; overflow: auto;'>$log</pre>");
		}

	}

} // TG