<style type="text/css" media="screen">
	#calc_t {border-collapse:separate; border-spacing: 2px;}
	#calc_t th{background:#1D5B94; color:#ddd}
	#calc_t td{padding:7px; vertical-align:top; border:1px solid #aaa;}
	#calc_t input[type=text],select {background:#eef; border:1px solid #aaa;}
	#calc_t #mod_descr{background:#eee;}
</style>

<?php
#KFF 
require_once 'PHP/classes/Excel/reader.php';

error_reporting(E_ALL ^ E_NOTICE);

function upgrate_base($path_xls)	{
	global $isResBase ;
	$isResBase = file_exists("http://www.kamaz.ru/prices/".$path_xls.'.xls');
	$handle = $isResBase? fopen("http://www.kamaz.ru/prices/".$path_xls.'.xls', "r") : fopen("out_files/KamAZ/".$path_xls.'.xls', "r");
/*
	$handle = fopen("http://www.kamaz.ru/prices/".$path_xls.'.xls', "r");	$f_out='';
	if (!$handle) $handle= fopen("out_files/KamAZ/".$path_xls.'.xls', "r");
*/
	if ($handle) {
	    while (!feof($handle)) {
	        $buffer = fgets($handle, 1024*8);
	        $f_out.= $buffer;
	    }	
//	    $f_out = fread($handle, filesize("http://www.kamaz.ru/prices/".$path_xls.'.xls'));
	    fclose($handle);
/* ==========================
	    $f_in = fopen("out_files/KamAZ/".$path_xls.'.xls', "w");
	    fwrite($f_in,$f_out);
	    fclose($f_in);
============================= */
	} else die( 'Файлы не доступны');
	return $isResBase;
}

$f_data = $isResBase? file("http://www.kamaz.ru/prices/kamaz_serial.xls"): file("out_files/KamAZ/kamaz_serial.xls"); //   print_r($f_data);

function read_xls ($path_xls='kamaz_serial', $sheets=5) {
	$fdate=date ("ymd",filemtime('out_files/KamAZ/'.$path_xls.'.xls'));	$date = date("ymd");
	if ($date!=$fdata) { upgrate_base($path_xls); } 
	global $time_base, $isResBase, $prices; 

	$time_base= date ("d.m.y - H:i",filemtime('out_files/KamAZ/'.$path_xls.'.xls'));
	$data = new Spreadsheet_Excel_Reader();
	$data -> setOutputEncoding('UTF-8');
	
	$data->read('out_files/KamAZ/'.$path_xls.'.xls'); 
	switch($path_xls) {
		case 'kamaz_serial':	$cm=1; $cp=2; $cpNDS=3; $cd=16; break;
		case 'kamaz_special':	$cm=1; $cp=3; $cpNDS=4;  $cbm=5; $cd=6;	break;
		default:	$cm=1; $cp=2; $cpNDS=3; $cd=16;
	}
	for($sh=0; $sh<=$sheets; $sh++) {
	$serial[$sh]= array();
		for ($i = 1; $i <= $data->sheets[$sh]['numRows']; $i++) {
			if(preg_match("/^.*\d+/",$data->sheets[$sh]['cells'][$i][$cm]) && preg_match("/\d{3,}/",$data->sheets[$sh]['cells'][$i][$cp])) {
			$serial[$sh][$i]= $data->sheets[$sh]['cells'][$i][$cm] .'|'. $data->sheets[$sh]['cells'][$i][$cp].'|'. $data->sheets[$sh]['cells'][$i][$cpNDS];
			$serial[$sh][$i].= ($cbm)?  '|'. $data->sheets[$sh]['cells'][$i][$cbm]: '|';
			$serial[$sh][$i].= ($cd)?  '|'. preg_replace ("/\"/", "&quot;", $data->sheets[$sh]['cells'][$i][$cd]): "";
			}
		}

		if(is_array($serial[$sh-1])) $serial[$sh]= array_merge($serial[$sh], $serial[$sh-1]);
		if(count($serial[$sh])<5) break;
	}

	//print_r($serial[$sheets]);

	$prices= json_encode(array_values($serial[$sheets])); // Перенумеровываем массив и переводим в формат js
	
//	echo $prices;
////////////////////////
/*
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
*/
	return $prices;
} //== /read_xls
$path_xls= isset($_GET['razd'])? $_GET['razd']: 'kamaz_serial';

echo '<div id="ps" hidden>' . read_xls($path_xls) . '</div>';
# ?>
<p class=warning><?=$prices?></p>
<!--  -->
<table id="calc_t" width="590">
 <tr align="center">
  <th colspan=3> Выбрать раздел : 
    <select id="razd" onchange="location.href='/PHP скрипты/Калькулятор с подгрузкой внешнего файла.php?razd='+this.value;" size="1">
    	<option value="kamaz_serial">kamaz_serial</option>
    	<option value="kamaz_special">kamaz_special</option>
    	<option value="kamaz_kmu">kamaz_kmu</option>
    </select>
    <div class="" style="">
    Текущий - <u><?=$path_xls?></u>
    </div>
 <tr>
  <th rowspan=2>Выберите модель:<br />
  	<input type="text" id="search" value="" style="width:150px;" placeholder="Найти..." onkeyup="search(this.value);"/>
  <th colspan=2>Цена завода
 <tr>
  <th>без НДС:
  <th width="150">с НДС:
 <tr align="center">
  <td>
  <select id="model" onchange="price();" size="1" style="max-width:150px;">
  </select>
  <td id="priceZ">
  <td id="priceZ+NDS">
 <tr>
  <td id="mod_descr" colspan=3><!-- Описание -->
 <tr>
  <td colspan=3><b>Дилерская скидка :</b> 
   <select id="rub_pr" onchange="price();" size="1">
   	<option value="rub">Рубли</option>
   	<option value="pr" selected="selected">Проценты</option>
   </select>
   <input type="text" id="dil_sk" value="10" onkeyup="price();" />
 <tr>
  <td colspan=3><b>Дополнительное оборудование :</b> 
  <input type="text" id="dop_oborud" value="0" onkeyup="price();"/> руб.
 <tr>
  <td colspan=3><b>Стоимость хранения :</b> 
  <input type="text" id="hran" value="0" onkeyup="price();"/> руб.
 <tr>
  <td colspan=3><b>Стоимость доставки :</b> 
  <input type="text" id="dostavka" value="0" onkeyup="price();"/> руб.
 <tr>
  <td colspan=2><b>Дополнительные Наценки (Скидки*)</b>, 
  <select id="rub_pr1" onchange="price();" size="1">
   <option value="rub">Рубли</option>
   <option value="pr" selected="selected">Проценты</option>
  </select>
  <p style="font-size:12px; margin:5px;"><b>*</b> Скидки задавать отрицательными, например, <span style="background:#eee; padding:3px;"><b>-10000</b></span></p>
  <td id="nacenka" style="text-align:center;"><input type="button" value=" + " title="Еще" onclick="nac_plus();"/><br /></td>
 <tr>
  <th rowspan=2>Цена от дилера
  <th>без НДС:
  <th width="120">с НДС:
 <tr align="center">
  <td id="price">
  <td id="price+NDS">
 <tr>
  <td colspan=3><p style="font-size:12px; margin:0; text-align:right">Последнее обновление базы раздела : <b><?=$time_base?></b> &nbsp;&nbsp;</p>
</table>
<div id="prim" style=""></div>


<div class="" style="padding:20px;">
<p>Теперь ежедневное обновление баз!</p>
<p>Для автоматического обновления базы данных, если последнее обновление осуществлялось более суток назад (дата последнего обновления внизу таблицы), нужно перезагрузить страницу - клавиша F5. </p>
<p>Проверьте правильность работы калькулятора. Если обнаружите неточности работы или недостающий раздел, свяжитесь с нами, воспользовавшись <a href="/?mailform" title="Обратная связь">Формой Обратной связи</a>. Приятного пользования!</p>
</div>


<script type="text/javascript">
Glob_vars.str= JSON.stringify(_K.G('#ps').textContent) ;
_K.DR(function() { (Ajax.Get.razd)? _K.G('razd').value= Ajax.Get.razd: null});
</script>

<script src="/out_files/KamAZ/KamAZ_IC" type="text/javascript" charset="utf-8"></script>
