<?php
function breadCrumbsRecurse($arr=null)
{
	$crumb_path = dirname($crumb_path ?? \DIR);
	if(trim($crumb_path,'/') === trim(\CONT,'/')) return;

	// var_dump($crumb_path);
	$data = \Page::getData($crumb_path);
	var_dump($data);
	$arr[$data['title']] = $crumb_path;

	// breadCrumbsRecurse($arr);
	die;


	$str = '<div id="breadcrumbs" style="margin: 15px 0 -2em;">';
	$path = '/' . \CONT;

	for ($i=1; $i < count($arr); $i++) {
		$c = $arr[$i];
		$path .= "$c/";
		$str .= "<a href=\"$path\" title=$c>$c</a> &middot; ";
	}

	return $str . '</div>';
}

echo breadCrumbsRecurse();