<?php
header('Access-Control-Allow-Origin: *');
?>

<h3>Ответ сервера сайта <?=?></h3>
<div id="answer">
	<p>Файл для тестирования ответа сервера на запросы от клиента.</p>
	<p>Это все безусловный вывод сервера на любой запрос. Далее рассмотрим параметры запроса.</p>
</div>
<?php
extract($_REQUEST);

$message = $message ?? $msg ?? null;

if(isset($message)) echo 'Message: ' .  $message;


echo '<pre>';

echo '<p>';
if(!empty($data)) echo 'Ответ сервера на $_POST[\'data\'].' ;
elseif(isset($data)) echo 'Проверьте валидность запроса.';

echo '</p>';

var_dump($_REQUEST);
echo '<hr>';

if (isset($file)) {
	echo '<hr>Входящие файлы: <br>';
	var_dump($_FILES["file"]);
}

$req = json_decode($json ?? null);

if($req)
{
	var_dump($req);

	if (isset($runScript) || $req->runScript ) {
		echo '<script type="text/javascript">alert("script runing!");</script>';
		echo '<script src="/js/Ing.js" type="text/javascript"></script>';
	}
}

