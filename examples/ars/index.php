<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->

<script src="./assets/js/jquery.min.js"></script>

	<link href="styles.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/scripts/css/PTSansNarrow/PTSans-Narrow.ttf">

		<link rel="stylesheet" href="./assets/css/main.css" />
		<link rel="stylesheet" href="./css/a.css" />
		<link rel="stylesheet" href="./css/prognoz.css" type="text/css" />

</head>


<div class="content1">

	<!-- График -->

<a name="f"></a>
<section id="two" class="wrapper style1 special">



<div class="PanelGrafik1">

<div id="Panel">


	<a onclick="SetTema(0)" id="tema0" title="Прогноз по всем сферам, все транзиты"><img src="./img/icons/chart.png"></a>
	<a onclick="SetTema(1)" id="tema1" title="Работа, карьера, бизнес"><img src="./img/icons/business.png"></a>
	<a onclick="SetTema(2)" id="tema2" title="Личные финансы"><img src="./img/icons/money.png"></a>
	<a onclick="SetTema(3)" id="tema3" title="Любовь, партнерские отношения"><img src="./img/icons/love.png"></a>
	<a onclick="SetTema(6)" id="tema6" title="Здоровье"><img src="./img/icons/red-cross.png"></a>
	<a onclick="SetVes(0)" id="v0" title="Все транзиты"><span><img src="./img/icons/sila20.png"></span></a>
	<a onclick="SetVes(1)" id="v1" title="Показать только транзиты 3 звезды и выше"><img src="./img/icons/star.png"><img src="img/icons/star.png"><br><img src="img/icons/star.png"></a>
	<a onclick="SetVes(2)" id="v2" title="Показать только транзиты 4 звезды и выше"><img src="./img/icons/star.png"><img src="img/icons/star.png"><br><img src="img/icons/star.png"><img src="img/icons/star.png"></a>
	<a onclick="SetVes(3)" id="v3" title="Показать только самые значимые транзиты - 5 звезд"><img src="./img/icons/sila34.png"></a>
	<a onclick="NewDateEnter()" title="Изменить начальную дату прогноза"><img src="./img/icons/today.png"></a>
	<a onclick="MoveInterval(-1)"  title="Предыдущий период"><img src="./img/icons/previous.png"></a>
	<a onclick="SetInterval(3)" id="in3" title="Прогноз на 3 дня"><span>3</span></a>
	<a onclick="SetInterval(7)" id="in7" title="Прогноз на неделю"><span>7</span></a>
	<a onclick="SetInterval(30)" id="in30" title="Прогноз на месяц"><span>30</span></a>
	<!--a onclick="SetInterval(100)" id="in100" title="Прогноз на 100 дней"><span>&nbsp;100&nbsp;</span></a-->
	<!--a onclick="SetInterval(366)" id="in366" title="Прогноз на год"><span>&nbsp;1год&nbsp;</span></a-->
	<!--a onclick="SetInterval(731)" id="in731" title="Прогноз на 2 года"><span>&nbsp;2г&nbsp;</span></a-->
	<a onclick="MoveInterval(1)"  title="Следующий период"><img src="./img/icons/next.png"></a>

	<a onclick="SetTarget()" id="trg" title="Основные"><span><img src="./img/icons/target.png"></span></a>
	<a href="#help" title="Помощь"><img src="./img/icons/help.png"></a>

</div>


<div id="PanelR" style="width: 100%; height: 600px;">

	<canvas id="PR"  style="z-index: 1;"></canvas>

	<div id="Grafik" style="background-color: transparent; position: absolute; top:0px; width:100%; height:600px; z-index: 2;"></div>

	<div id="calend_text" style="background-color: transparent; position: absolute; top:0px; left:250px; width:270px; height:500px; z-index: 3;"></div>
	<div id="NewDate">	</div>



	<script type="text/javascript">
		var natal = [101.763008923,73.7827109618,94.2913378743,89.0762641247,59.4997151133,180.54853596,182.984480102,237.430854158,263.766604795,201.740985129,,];

		var Dom = [0,,,,,,,,,,,,];
		var Ess = [19,2,-2,4,1,-1,2,-2,9,-14];
		var Acc = [4,0,5,4,1,1,0,0,0,0];
		var AsQ = [5,4,9,3,5,14,7,2,-6,4];
		var kp = [1,1,-1,1,1,1,1,-1,-1,-1];
		var B = [[0,3,3,3,3,3,3,3,3,3,3,3,3],[1,3,3,3,3,3,3,3,3,3,3,3,3],[2,3,3,3,3,3,3,3,3,3,3,3,3],[3,3,3,3,3,3,3,3,3,3,3,3,3],[4,3,3,3,3,3,3,3,3,3,3,3,3],[5,3,3,3,3,3,3,3,3,3,3,3,3],[6,3,3,3,3,3,3,3,3,3,3,3,3],[7,3,3,3,3,3,3,3,3,3,3,3,3],[8,3,3,3,3,3,3,3,3,3,3,3,3],[9,3,3,3,3,3,3,3,3,3,3,3,3],[10,6,0,0,0,0,0,6,0,0,0,0,0],[11,0,0,0,0,0,0,0,0,0,6,0,0]];

	</script>




	<script type="text/javascript" src="./info.php"></script>


<script type="text/javascript" src="./gantt.js"></script>

<div class="asp_text" id="asp_text"><div id="but_close"></div></div>

<div class="asp_text" id="asp_text"><div id="but_close"></div></div>

<a name="help"></a>
</section>


<!-- График конец -->
</div>


<hr>

<h2>Used files:</h2>

<ol>
<?php
$path = scandir(".");

foreach($path as $k) {
	if(is_dir($k)) continue;
	// echo $k."<br>";
	echo "<li><a href=\"/examples/ars/dload.php?f=$k\">$k</a> - Last update: " . date('Y-m-d H:i:s', filemtime($k)) . "</li>";
}
?>
</ol>
<hr>