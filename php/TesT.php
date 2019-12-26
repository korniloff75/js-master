<?php
header('Access-Control-Allow-Origin: *');
?>

<h3>Ответ сервера сайта <?=$_SERVER['HTTP_HOST']?></h3>
<div id="answer">
	<p>Файл для тестирования ответа сервера на запросы от клиента.</p>
	<p>Это все безусловный вывод сервера на любой запрос. Далее рассмотрим параметры запроса.</p>
</div>
<?php
$_REQUEST = array_filter(
	$_REQUEST,
	function (&$key) {
		return !in_array($key, ['__cfduid', 'PHPSESSID', 'diz_alt_Img', 'diz_alt', 'page_ind', 'cecFont', 'cecColor', 'cecImg']);
	}, ARRAY_FILTER_USE_KEY
);

// extract($_REQUEST);

echo '<pre>';
var_dump($_REQUEST);
echo '<hr>';

if (isset($file)) {
	echo '<hr>Входящие файлы: <br>';
	var_dump($_FILES["file"]);
}

