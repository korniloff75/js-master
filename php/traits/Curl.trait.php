<?php

/**
 *
 */
trait Curl
{
	protected static
		$proxyPath = 'db.proxy';

	/**
	 * ! Required
	 * @param proxy - полная строка для вывода в js
	 * @param stop - service stop recursion
	 */
	protected function findAnzProxy(?string $proxy=null, bool $stop=false)
	{
		$timeoutInSeconds = 1;
		$parsePath = 'https://cloudflare-ipfs.com/ipns/pacipfs2.antizapret.prostovpn.org/proxy-nossl.js';

		if(file_exists(self::$proxyPath))
		{
			$proxy = $proxy ?? file_get_contents(self::$proxyPath);

			preg_match("~PROXY\s+(.+); DIRECT~i", $proxy, $proxyURL);

			$proxyURL = "http://{$proxyURL[1]}";
			trigger_error("\$proxyURL = $proxyURL");

			$p = parse_url($proxyURL);
			// $p['scheme'].'://'.

			//* Если прокси из файла доступен - возвращаем его
			if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
			{
				$this->log->add("Proxy $proxyURL - is <font color=green size=4><b>AVAILABLE</b></font>\n");
				return $proxyURL;
			}
			//* Если недоступен - удаляем файл + рекурсия
			else
			{
				trigger_error("$proxyURL - ERROR: $errCode - $errStr", E_USER_WARNING);
				unlink(self::$proxyPath);
				return $this->findAnzProxy(null, $stop);
			}
		}
		//* Если нет файла
		else
		{
			if(
				//* Если повторная рекурсия - тормозим
				!$stop
				//* Ищем обновлённый прокси
				&& ($anz = file_get_contents($parsePath))
				&& preg_match(
					"~return \"(PROXY.+DIRECT)\";$~im", $anz, $proxy)
			)
			{
				$proxy = $proxy[1];
				file_put_contents(self::$proxyPath, $proxy);

				//* Рекурсия с новым прокси
				return $this->findAnzProxy($proxy, true);
			}

		}
		//* Полный провал
		return false;
	}


	private function CurlSetOpt(string $url, array $opts=[])
	{
		$opts = array_merge([
			'sendMethod' => 'post',
			'headers' => ["Content-Type:multipart/form-data"]
		], $opts);

		$this->log->add(__METHOD__ . " \$opts['sendMethod'] = ", null, [$opts['sendMethod'], 'post' === strtolower(@$opts['sendMethod'])]);
		$this->log->add(__METHOD__ . " \$opts = ", null, [$opts]);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		//* Все кодировки
		curl_setopt($ch, CURLOPT_ENCODING, '');

		if(is_array(@$opts['headers']))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $opts['headers']);

		}

		if('post' === strtolower(@$opts['sendMethod']))
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			if(is_array(@$opts['params']))
			{
				foreach ($opts['params'] as &$val)
				{
					# encoding to JSON not primitive parameters
					if (!is_numeric($val) && !is_string($val)) {
						$val = json_encode($val, JSON_UNESCAPED_UNICODE);
					}
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $opts['params']);
				$this->log->add(__METHOD__ . 'params= ', null, [$opts['params']]);
			}

		}
		else //* GET etc.
		{
			if(is_array(@$opts['params']))
				$url.= '?' . http_build_query($opts['params']);
		}

		// if(in_array(strtolower(@$opts['sendMethod']), ['post', 'get']))
		// 	unset($opts['sendMethod']);

		curl_setopt($ch, CURLOPT_URL, $url);

		// $response = curl_exec($ch);

		return $ch;
	}


	public function CurlRequest(string $url, array $opts=[])
	{
		$ch = $this->CurlSetOpt($url, $opts);

		return $this->execCurl($ch);
	}


	public function CurlRequestProxy(string $url, array $opts=[])
	{
		$ch = $this->CurlSetOpt($url, $opts);
		curl_setopt($ch, CURLOPT_PROXY, $this->findAnzProxy());

		return $this->execCurl($ch);
	}


	/**
	 ** Исполнение Curl, вывод и логирование результатов
	 */
	public function execCurl($ch)
	{
		$this->log->add(__METHOD__ . "Curl resourse: ", null, [$ch]);

		$response = curl_exec($ch);

		if ($response === false)
		{
			$errno = curl_errno($ch);
			$error = curl_error($ch);
			$this->log->add("Curl returned error $errno: $error", E_USER_WARNING);
			curl_close($ch);

			return false;
		}

		$this->curlInfo = curl_getinfo($ch);
		$http_code = intval($this->curlInfo['http_code']);
		curl_close($ch);

		$response = json_decode($response, true);

		$this->log->add(__METHOD__ . ' $response = ', null, [$response]);

		if ($http_code >= 500)
		{
			// do not wat to DDOS server if something goes wrong
			usleep(500);
			// return false;
		}
		elseif ($http_code != 200)
		{
			if ($http_code == 401)
			{
				$this->log->add('Invalid access token provided', E_USER_WARNING);
			}
		}
		else
		{
			$this->log->add(__METHOD__ . " was SUCCESSFUL", null, [$response]);
		}

		return $response;
	} // execCurl
}
