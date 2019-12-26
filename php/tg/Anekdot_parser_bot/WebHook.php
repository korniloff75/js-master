<?php
require_once "sts.php";

if(!file_exists("registered.trigger"))
{
	/* $url = "https://api.telegram.org/bot".TOKEN."/setWebhook?url=https://js-master.ru/" . DIR . "/bot.php";
	$l1=file_get_contents($url);
	$response = json_decode($l1); */

	$response = $tg->request([
		'url' => BASE_URL . DIR . "/bot.php"
	], 'setWebhook');
	$response = json_decode($response);

	print_r(
		[$response, ]
	);

	if($response)
		file_put_contents("registered.trigger",time());
}
else
{
	echo "<pre>Webhook уже зарегистрирован\n";
}



TG::log(['echo "END of ' . basename(__FILE__) . '"',], __FILE__, __LINE__);
