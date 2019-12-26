<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Обратная связь</title>
</head>
<body>
<?php

function _mail ($from, $to, $subj, $what)
{
mail($to, $subj, $what, 
"From: $from
Reply-To: $from
Content-Type: text/plain; charset=windows-1251
Content-Transfer-Encoding: 8bit"
);
}



$name=$_POST["name"];
$email=$_POST["email"];
$mess=$_POST["mess"];
if (isset ($name))
{
$name = substr($name,0,20); //Не может быть более 30 символов
if (empty($name))
{
echo "<center><b>Не указано имя!<p>";
echo "<a href=index.htm>Вернуться и правильно заполнить форму.</a>";
exit;
}
}
else
{
$name = "не указано";
}
if (isset ($email))
{
$email = substr($email,0,20); //Не может быть более 20 символов
if (empty($email))
{
echo "<center><b>Не указан e-mail!<p>";
echo "<a href=index.htm>Вернуться и правильно заполнить форму.</a>";
exit;
}
}
else
{
$email = "не указано";
}
if (isset ($mess))
{
$mess = substr($mess,0,1000); //Не может быть более 1000 символов
if (empty($mess))
{
echo "<center><b>Сообщение не написано!<p>";
echo "<a href=index.htm>Вернуться и правильно заполнить форму.</a>";
exit;
}
}
else
{
$mess = "не указано";
}
$i = "не указано";
if ($name == $i AND $email == $i AND $mess == $i)
{
echo "Ошибка! Скрипту не были переданы параметры";
exit;
}
$to = "Rina.65@mail.ru";
$subject = "Сообщение с вашего сайта";
$message = "Имя пославшего: $name, Электронный адрес: $email, Сообщение: $mess, IP-адрес:$REMOTE_ADDR";
_mail ("site@xopoco.ru", $to,$subject,$message);// or print "Не могу отправить письмо!";
echo "<center><b>Спасибо за отправку вашего сообщения<br><a href=index.htm>Нажмите</a>, чтобы вернуться на главную страницу";
exit;
?>
</body>
</html>
