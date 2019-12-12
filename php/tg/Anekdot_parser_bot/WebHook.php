<?php
require_once "sts.php";

if(!file_exists("registered.trigger"))
{
	$url = "https://api.telegram.org/bot".TOKEN."/setWebhook?url=https://js-master.ru/" . DIR . "/bot.php";
	$l1=file_get_contents($url);
	$response = json_decode($l1);

	echo '<pre>';
	print_r(
		[$url, $response, ]
	);

	if($response)
		file_put_contents("registered.trigger",time());
}
else
	echo "<pre>Webhook уже зарегистрирован\n";

if (\TEST)
{
	echo $_SERVER['DOCUMENT_ROOT'] . "/Helper.php\n";
	$path = new kffFileInfo($_SERVER['DOCUMENT_ROOT'] . "/Helper.php");
	// echo "$path\n";
	echo $path->getPathname() . "\n";
	echo $path->getRealPath() . "\n";
}
