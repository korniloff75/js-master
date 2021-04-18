<?php
// note deprecated
function note($i, $file = null, $line = null)

{
	global $notes;
	// \H::$notes = array_merge_recursive(\H::$notes, [$file => ($line ? [$line => $i] : $i)]);

	if(realpath('') !== realpath(\HOME))
		return;

	if($line)
		\H::$notes[$file][$line][] = $i;
	elseif($file)
		array_push(\H::$notes[$file], $i);
	else
		array_push(\H::$notes, [$i]);

	// $notes[] = $i;
}

function gettime()
{
	$part_time = explode(' ', microtime());
	$real_time = $part_time[1].substr($part_time[0], 1);
	return $real_time;
}



# Deprecated !!!

/* Возвращает многоуровневый массив, содержащий полную карту файлов из $dir
replaced
$dirIter = new RecursiveDirFilter($dir, '#\.(php|html?)$#i');
*/

function scan_recurse ($dir = null, $arr=[])

{
	global $H;

	$dir = $dir ?? \CONT;

	if(strrchr($dir, "/") !== '/') $dir .= '/';
	if(!is_dir($dir)) return;

	// var_dump($dir, $arr);
	$add_arr = array_filter(scandir($dir), function($i) {
		return strpos($i, '.') !== 0 && (in_array(strrchr($i, '.'), ['.php','.htm']) || is_dir($i));
	});

	var_dump(
		scandir($dir),
		$add_arr
	);

	$add_arr = array_map(function(&$i) use($H, $dir, &$arr) {
		if(is_dir($dir . $i)) {
			$r =  $H['scan_recurse']($dir . $i, $arr);
			unset($i);
		} else {
			$r =  $dir . $i;
		}

		return $r;
	}, $add_arr);

	// var_dump($add_arr);

	$arr = array_merge($arr, $add_arr);

	// var_dump($add_arr);

	return $arr;

};


/* function addData($path)
{
	$data_path = $path . '/data.json';
	var_dump($data_path);

	return isset($data_path ) ? json_decode(file_get_contents($data_path ), 1) : [];
} */

