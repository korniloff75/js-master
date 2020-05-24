<!DOCTYPE html>

<html lang="ru">

<?php

echo "<pre>";

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=" . getenv('PATH') . ":" . realpath('.'));

// var_dump(getenv('PATH'));

require_once './php/EntryPointGraph.class.php';

$Graph = new EntryPointGraph();
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
require_once './php/Dload.class.php';