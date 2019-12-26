<?php
# header('content-type: application/x-javascript; charset=utf-8');

extract($_REQUEST);

if(!$file) $file= 'Antispam_DB';
$fileDB= file_get_contents($file);

if(isset($cp1251) ) {
	$fileDB = iconv("UTF-8", 'cp1251' , $fileDB);
	header('content-type: application/x-javascript; charset=windows-1251');
}

echo $fileDB;
die;
?>