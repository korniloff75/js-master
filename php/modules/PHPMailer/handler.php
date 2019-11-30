<?php
require_once __DIR__ . '/MailPlain.php';

/* var_dump(
	$_REQUEST
); */


$subject = "{$_REQUEST['subject']} - Обратная связь с " . HOST;
$message = "{$_REQUEST['name']} пишет: \n\n{$_REQUEST['message']} \n\nTelegram - {$_REQUEST['tg']}";

$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

MailPlain::save($mailPlain->validated);

/* Optional

$mailPlain->Username  = "fb@js-master.ru",
$mailPlain->Password = "1975kp@1975",
$mailPlain->Host = "web01-cp.marosnet.net",
# In constant OWNER['email'] must contains string or array with emails
# If OWNER['email'] don't defined - can use
$mailPlain->to_emails = [
	// aray with emails
],
*/

if(@$_REQUEST['captcha'] != $_SERVER['REMOTE_ADDR'])
{
	echo $_REQUEST['captcha'] . '<br>';
	echo $_SERVER['REMOTE_ADDR'] . '<br>';
	// echo $_SESSION['captcha'] . '<br>';
	echo "Невидимая каптча не пройдена. Попробуйте ещё раз.";
}
elseif($send_succ = $mailPlain->TrySend())
{
	# Success
	echo "<div class=\"success\">Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email или Telegram. </div>";
}
else
{
	# Fail
	echo "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>";

}

if(@$adm) var_dump($send_succ);


/* Telegram
# To Me
$sendToTelegram = MailPlain::toTG(673976740);
# To Cat
MailPlain::toTG(677430081);
*/

$sendToTelegramGroup = MailPlain::toTG();

/* var_dump(
	$sendToTelegramGroup
); */
