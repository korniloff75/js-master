<!DOCTYPE html>

<html lang="ru">

<?php

if(version_compare(PHP_VERSION, '7.0') < 0)
{
	die("<h2>Требуется версия РНР выше 7.0 !!!</h2>");
}

echo "<pre>";

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=PATH:" . realpath('.'));

$P= getenv('PATH');

require_once './php/Graph.class.php';
require_once './php/PlAngles.class.php';
$Graph = new PlAngles();
$JSON = $Graph->GetJSON();
$Graph->CollectToJson();
?>

<script>
	let _json= <?=$JSON?>;
</script>
<script type="text/javascript" src="./main_kff.js"></script>

<?php
echo "</pre>";
// *Вывожу все файлы из текущей папки для скачивания
require_once('./php/Dload.class.php');