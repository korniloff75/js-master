<!DOCTYPE html>

<html lang="ru">

<?php

echo "<pre>";

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=PATH:" . realpath('.'));

$P= getenv('PATH');

require_once './php/Graph.class.php';
require_once './php/PlAngles.class.php';
$Graph = new PlAngles('10 day');
$JSON = $Graph->GetJSON();
?>

<script>
	let _json= <?=$JSON?>;
</script>
<script type="text/javascript" src="./main_kff.js"></script>

<?php
echo "</pre>";
// *Вывожу все файлы из текущей папки для скачивания
require_once('dload.php');