<?php
require_once 'reader.php';
// ExcelFile($filename, $encoding);
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('UTF-8');

$data->read('kamaz_serial.xls');

 error_reporting(E_ALL ^ E_NOTICE);

$serial= array();
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	if(preg_match("/^\d+/",$data->sheets[0]['cells'][$i][1])) {
	$serial[$i]= $data->sheets[0]['cells'][$i][1] .'|'. $data->sheets[0]['cells'][$i][2].'|'. $data->sheets[0]['cells'][$i][3];
	}
	echo "\"".$data->sheets[0]['cells'][$i][1]."\","."\n";
$prices= json_encode(array_values($serial)); // Перенумеровываем массив и переводим в формат js
}
//print_r($prices);

// Выводим всю таблицу
echo '<hr />Выводим всю таблицу 1 лист<hr /><table>';
for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
  echo '<tr>';
  for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
    echo '<td>'.$data->sheets[0]['cells'][$i][$j].'</td>';
  }
  echo '</tr>';
}
echo '</table>';
echo '<hr />Выводим всю таблицу 2 лист<hr /><table>';
for ($i = 1; $i <= $data->sheets[2]['numRows']; $i++) {
  echo '<tr>';
  for ($j = 1; $j <= $data->sheets[2]['numCols']; $j++) {
    echo '<td>'.$data->sheets[2]['cells'][$i][$j].'</td>';
  }
  echo '</tr>';
}
echo '</table>';
?>
<script type="text/javascript">
// alert(<?=$prices?>);
</script>