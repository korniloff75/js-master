<?php
#KFF
// error_reporting(-1);

require_once 'php/modules/Excel/reader.php';
// require_once 'php/modules/phpExcelReader/Excel/reader.php';

// http://www.kamaz.ru/prices/kamaz_serial.xls .'.xls'

// setcookie('testCook');


function upgrate_base($path_xls)	{
	$handle = fopen($path_to_base, "r");	$f_out='';
	if ($handle) {
	    while (!feof($handle)) {
	        $buffer = fgets($handle, 1024*8);
	        $f_out.= $buffer;
	    }
	    fclose($handle);

	} else echo "Файл $path_to_base не доступен";
}


function read_xls ($sheets=5)
{
	global $path_xls, $prices;

	// $path_xls= $_REQUEST['bname'] ?? 'kamaz_special';
	$path_xls= $_REQUEST['bname'] ?? 'kamaz_serial';
	$path_to_base = \H::$Dir  . "assets/$path_xls.xls";

	// $time_base= date ("d.m.y - H:i",filemtime($path_to_base));

	// var_dump($path_to_base);

	$data = new Spreadsheet_Excel_Reader();
	$data->setOutputEncoding('UTF-8');

	$data->read($path_to_base);

	switch($path_xls) {
		case 'kamaz_serial':
			$cm=1; $cp=2; $cpNDS=3; $cd=16;
			break;
		case 'kamaz_special':
			$cm=1; $cp=3; $cpNDS=4;  $cbm=5; $cd=6;
			break;
		case 'kamaz_kmu':
			$cm=1; $cp=2; $cpNDS=3; $cd=16;
			break;
		default:
			$cm=1; $cp=2; $cpNDS=3; $cd=16;
	}

	/* echo '<pre>';
	print_r($data->sheets[1]);
	echo '</pre>'; */

	$serial_gl = [];

	for($sh=0; $sh<=$sheets; $sh++) {

		if(isset($data->sheets[$sh])) $serial= [];
		else continue;

		// var_dump($data->sheets[$sh]['numRows']);

		for ($i = 1; $i <= $data->sheets[$sh]['numRows']; $i++) {
			if(!isset($data->sheets[$sh]['cells'][$i]))
			{
				continue;
			}

			$v_cm = $data->sheets[$sh]['cells'][$i][$cm] ?? 0;
			$v_cp = $data->sheets[$sh]['cells'][$i][$cp] ?? 0;
			if(!$v_cm || !$v_cp) continue;

			if(preg_match("/^.*\d+/", $v_cm) && preg_match("/\d{3,}/", $v_cp)) {
				$serial[$i]= $v_cm .'|'. $v_cp.'|'. $data->sheets[$sh]['cells'][$i][$cpNDS];
				$serial[$i].= isset($cbm) && isset($data->sheets[$sh]['cells'][$i][$cbm]) ? '|'. $data->sheets[$sh]['cells'][$i][$cbm] : '|';
				$serial[$i].= isset($data->sheets[$sh]['cells'][$i][$cd]) ?  '|'. preg_replace ("/\"/", "&quot;", $data->sheets[$sh]['cells'][$i][$cd]): "";
			}
		}

		// if(isset($serial[$sh-1]) && is_array($serial[$sh-1])) $serial = array_merge($serial, $serial[$sh-1]);
		$serial_gl = array_merge($serial_gl, $serial);

		if(count($serial)<5) break;
	}

	/* echo '<pre>';
	var_dump(
		count($serial_gl),
		array_values($serial_gl)
	);
	echo '</pre>'; */

	// Перенумеровываем массив и переводим в формат js
	return json_encode(array_values($serial_gl));
	// $prices= json_encode(array_values($serial[$sheets]));
}
?>

<script>
	var Prices_db = <?=read_xls();?>
</script>