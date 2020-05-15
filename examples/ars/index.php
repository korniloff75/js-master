<!DOCTYPE html>

<html lang="ru">

<?php

// putenv(realpath('.'));
// putenv("PATH=PATH;" . realpath('.'));
putenv("PATH=PATH:" . realpath('.'));

$P= getenv('PATH');

echo "<pre>";


// *set date range
$deltaDate= '10 day';
$rangeDate = [
	(new DateTime())->modify("-$deltaDate")->getTimestamp(),
	(new DateTime())->modify("+$deltaDate")->getTimestamp()
];

$data= [];
$json= [
	'columns'=> [
		['x']
	]
];

$start_time= microtime(true);

define('SWETEST_PATH', __DIR__ . "/swetest.exe");

$cols= &$json['columns'];

for($ts=$rangeDate[0]; $ts<=$rangeDate[1]; $ts+=3600*48)
{
	$date = date('d.m.Y', $ts);
	$time = date('H:i:s', $ts);

	$cols[0][]= $ts;

	exec(SWETEST_PATH . " -edir\"./sweph/\" -b$date -ut$time -p0123456789 -eswe -fPls -g, -head", $outExec, $status);

	// var_dump($outExec);

	// var_dump($ts);
}

$outExec= array_values($outExec);


// *Парсим вывод из программы

foreach ($outExec as $line) {

	$row = preg_split('/\s*,\s*/', $line);

	if(count($row)<3)
		continue;

	// *Абсолютные углы, град.
	$cols[$row[0]][]= (float) $row[1];

};


// *Интерполируем промежуточные значения

$interCols= [];
$interParts= 8;

foreach($cols as $name=>&$v)
{
	foreach($v as $n=>&$cur)
	{
		$interCols[$name][]= $cur;

		if(
			empty($next= @$v[$n+1])
			|| !is_numeric($cur)
		) continue;


		// *Фиксим переход 360->0
		if($cur > 350 && $next < 50)
		{
			$prev= $v[$n-1];
			$delta= ($cur - $prev) / $interParts;

			for ($i=1; $i < $interParts; $i++)
			{
				// if(!is_numeric($name) && abs($delta) < 180)
					$interCols[$name][]= $delta * $i;
			}
		}
		else
		{
			$delta= ($next - $cur) / $interParts;

			for ($i=1; $i < $interParts; $i++)
			{
				// if(!is_numeric($name) && abs($delta) < 180)
					$interCols[$name][]= $cur + $delta * $i;
			}
		}


		// var_dump($cur, $inter);

	}

}

$cols = $interCols;

// *Timing
$delta_time= (microtime(true) - $start_time) * 1000;
echo "<h4>TimeExec = $delta_time ms</h4>";

// *Controls
var_dump(
	// $outExec,
	// $cols,
	// $interCols,
	SWETEST_PATH
	, file_exists(SWETEST_PATH)
	, $start_time
	, $delta_time
	, realpath('.')
	, $rangeDate
);



?>

<script>
	let _json= <?=json_encode($json)?>;
</script>
<script type="text/javascript" src="./main_kff.js"></script>

<?php
// *Вывожу все файлы из текущей папки для скачивания
require_once('dload.php');