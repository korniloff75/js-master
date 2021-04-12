<!DOCTYPE html>

<html lang="ru">
	<head>
		<script src="./assets/js/konva.min.js"></script>
	</head>

	<button onclick="var pre= document.querySelector('#main_data'); pre.hidden = !pre.hidden; this.textContent= pre.hidden? 'Show data':'Hide data'">Show data</button>
<?php

echo "<pre id='main_data' hidden style=\"white-space: pre-wrap;\">";

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=" . getenv('PATH') . ":" . realpath('.'));

// var_dump(getenv('PATH'));

require_once './php/EntryPointGraph.class.php';

$Graph = new EntryPointGraph();
$JSON = $Graph->GetJSON();
$Graph->CollectToJson();
$ANGLES = $Graph->GetJSON($Graph->angles);
echo "</pre>";
?>

<script>
	let _json= <?=$JSON?>;
	let _angles= <?=$ANGLES?>;
	let _tss= <?=$Graph->GetJSON($Graph->tss)?>;
</script>
<script type="text/javascript" src="./main_kff.js"></script>

<style>
	div.konva_wrapper {width:100%; overflow:auto;}
	div#konva_container {position: relative;}
	div.konvajs-content {display:inline-block;}
</style>
<div class="konva_wrapper">
	<div id="konva_container" style="text-align:center;">
		<!--  -->
	</div>
</div>
<div id="konva_data">
	<!--  -->
</div>


<?php

// *Вывожу все файлы из текущей папки для скачивания
// require_once './php/Dload.class.php';