<?php
/**
 * @param proxy - полная строка для вывода в js
 * @param stop - service stop recursion
 */
ob_start();
echo '<pre style="white-space: pre-line;">';
function findAnzProxySSL(?string $proxyWpad=null, bool $stop=false)
{
	$timeoutInSeconds = 1;
	$parsePath = 'https://cloudflare-ipfs.com/ipns/pacipfs2.antizapret.prostovpn.org/proxy-ssl.js';
	$proxyPath = 'ssl.db.proxy';

	if(file_exists($proxyPath))
	{
		$proxyWpad = $proxyWpad ?? file_get_contents($proxyPath);
		preg_match("~HTTPS\s+(.+); PROXY~i", $proxyWpad, $proxyURL);

		$proxyURL = "https://{$proxyURL[1]}";
		trigger_error("\$proxyURL = $proxyURL");

		$p = parse_url($proxyURL);
		// $p['scheme'].'://'.

		# Если прокси из файла доступен - возвращаем его
		if($fp = fsockopen($p['host'], $p['port'], $errCode, $errStr, $timeoutInSeconds))
		{
			trigger_error("Proxy $proxyURL - is <font color=green size=4><b>AVAILABLE</b></font>\n");
			return $proxyWpad;
		}
		# Если недоступен - удаляем файл + рекурсия
		else
		{
			trigger_error("$proxyURL - ERROR: $errCode - $errStr", E_USER_WARNING);
			unlink($proxyPath);
			return findAnzProxySSL(null, false);
		}
	}
	# Если нет файла
	else
	{
		if(
			# Если повторная рекурсия - тормозим
			!$stop
			# Ищем обновлённый прокси
			&& ($anz = file_get_contents($parsePath))
			&& preg_match(
			"~return \"(HTTPS.+DIRECT)\";$~im", $anz, $proxyWpad)
		)
		{
			$proxyWpad = $proxyWpad[1];
			file_put_contents($proxyPath, $proxyWpad);

			# Рекурсия с новым прокси
			return findAnzProxySSL($proxyWpad, true);
		}

	}
	# Полный провал
	return false;
}

$proxySSL = findAnzProxySSL();
$proxy = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/php/tg/Common/db.proxy");
trigger_error("\$proxySSL = $proxySSL\n\$proxy = $proxy");

if(isset($_GET['_dev']))
	ob_end_flush();
else
	file_put_contents('wpad.log', strip_tags(ob_get_clean()));

if(!$proxySSL) die;

// * https://js-master.ru/assets/wpad/proxy.wpad.pac
// * Оборачивать в <script></script> нельзя
// * Комменты работе не мешают
ob_start();
?>

<script>
	var enHosts = [
		'code.google',
		'.googleapis',
		'telegram', 't.me',
		'kinorai',
		'spotify',
		// test https://suip.biz/ru/?act=proxy-checker
		'suip\.biz',
		'nvidia.ru',
		// ВадимZ
		'kinozal', 'pornolab', 'erokomiksixxx', 'svscomics', 'abook-club', 'nnm\-club', 'rutracker', '4pna', 'rutor', '7\-zip',
	];

	function FindProxyForURL(url, host)
	{
		// !isPlainHostName(host) ||
		var find = enHosts.some( i=>{
			return (new RegExp(i, "i")).test(host);
		});

		if ( !find )
			return "DIRECT";
		else if(/^https/i.test(url))
		// else
			return "<?=$proxySSL?>";
		else
			return "<?=$proxy?>";

	}
</script>

<?php
echo preg_replace("~</?script>~iu", '', ob_get_clean());