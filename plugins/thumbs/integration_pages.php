<?php
require_once __DIR__.'/Thumb.class.php';

$Thumb= new Thumb;

$Data= &\Page::$Data;

//todo *Add thumbs
$images = (new \DirFilter(\Site::$Page->kfi->getPathname(), "#\.(jpe?g|png)$#"))->natSort();

if(\MODULES['Thumb']['enable'] && (!isset($Data['thumb']) || $Data['thumb'] == true) && $images)
	echo $Thumb->toPage();
