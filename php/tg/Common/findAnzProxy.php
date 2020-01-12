<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('allow_url_fopen', 'On');
error_reporting(-1);

if (version_compare(PHP_VERSION, '7.0') < 0)
	die('Обнови PHP!');

if(!file_exists('php.info'))
{
	ob_start();
	phpinfo();
	$info = ob_get_clean();
	file_put_contents('php.info', $info);
}

/* function test_get_contents() {
  file_get_contents("http://example.com");
  var_dump($http_response_header);
} */

function findAnzProxy(?string $proxy=null, bool $stop=false)
{
	$proxyPath = __DIR__ . '/../db.proxy';
	$timeoutInSeconds = 1;

	if(file_exists($proxyPath))
	{
		$proxy = $proxy ?? file_get_contents($proxyPath);

		$p = parse_url($proxy);
		// $p['scheme'].'://'.

		# Если прокси из файла доступен - возвращаем его
		if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
		{
			trigger_error("Proxy $proxy - is AVAILABLE\n");
			return $proxy;
		}
		# Если недоступен - удаляем файл + рекурсия
		else
		{
			trigger_error("$proxy - ERROR: $errCode - $errStr", E_USER_WARNING);
			unlink($proxyPath);
			return findAnzProxy(null, false);
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
			file_put_contents($proxyPath, $proxy);

			# Рекурсия с новым прокси
			return findAnzProxy($proxy, true);
		}

	}
	# Полный провал
	return false;
}

echo PHP_EOL . PHP_EOL;
echo findAnzProxy();
echo PHP_EOL . PHP_EOL;