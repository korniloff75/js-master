<h1>Сервер получил данные:</h1>

<?php
var_dump ($_REQUEST);

echo "<h2>Расшифровка:</h2>";

if($_REQUEST['planet'] === 'moon')
{
	echo "Ты должен отдать данные по ЛУНЕ за {$_REQUEST['time']} время по Unix. Ферштейн?<hr>";
	echo json_encode(['Нужный', 'мне', 'массив'], JSON_UNESCAPED_UNICODE);
}

if(!empty($_REQUEST['range']))
{
	echo "<hr>Если получил параметр range - отдаёшь мне массив с массивами.<hr>";
	$out=['time'=>$_REQUEST['time']];
	$planets= ['moon', 'mars', 'sun'];

	foreach($planets as $p)
	{
		$out[$p]= "Нужные данные по планете $p";
	}

	echo json_encode($out, JSON_UNESCAPED_UNICODE);
}


// * Даты
echo "<h2>Даты</h2>";

$str_date= '15.02.2009';
echo "$str_date<br>";
$unix_date = DateTime::createFromFormat('j.m.Y', $str_date)->getTimestamp();
echo "$unix_date<br>";



