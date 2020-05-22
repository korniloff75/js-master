<!DOCTYPE html>

<html lang="ru">

<?php

if(version_compare(PHP_VERSION, '7.0') < 0)
{
	die("<h2>Требуется версия РНР выше 7.0 !!!</h2>");
}

define('LOCAL', ($_SERVER['HTTP_HOST'] === "js-master"));

echo "<pre>";

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=" . getenv('PATH') . ":" . realpath('.'));

// var_dump(getenv('PATH'));

require_once './php/EntryPointGraph.class.php';

$Graph = new EntryPointGraph();
$JSON = $Graph->GetJSON();
// $Graph->CollectToJson();
?>

<script>
	let _json= <?=$JSON?>;
</script>
<script type="text/javascript" src="./main_kff.js"></script>

<?php
echo "</pre>";
// *Вывожу все файлы из текущей папки для скачивания
require_once './php/Dload.class.php';