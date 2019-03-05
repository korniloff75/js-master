<?php
// require_once HOME . 'CONST.php';
// require_once '../funcs.php';
require_once __DIR__ . '/MailPlain.php';

// var_dump($_REQUEST);

// var_dump($valid);

$subject = $_REQUEST['subject'] . ' - Обратная связь с ' . HOST;
$message = $_REQUEST['name'] . " пишет: \n\n{$_REQUEST['message']}";

$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

MailPlain::save($mailPlain->validated);

/* Optional

$mailPlain->Username  = "fb@js-master.ru",
$mailPlain->Password = "1975kp@1975",
$mailPlain->Host = "web01-cp.marosnet.net",
# In constant OWNER_EMAIL must contains string or array with emails
# If OWNER_EMAIL don't defined - can use
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
	echo "<div class=\"success\">Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email. </div>";
}
else
{
	# Fail
	echo "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>";

}

if(@$adm) var_dump($send_succ);
