<?php
namespace plugins;

function breadCrumbs($arr=null)
{
	$crumbs= '<div id="breadcrumbs" style="margin: 15px 0 -2em;">';

	$crumb_path = \DIR;

	while(file_exists($crumb_path.'/data.json') && ($data= \Page::getData($crumb_path))){
		$crumbs.= "<a href=\"$path\">{$data['title']}</a> >> ";
		$crumb_path = dirname($crumb_path);
		echo $crumb_path;
	}

	$crumbs.= '</div>';

	return $crumbs;

}

// \Plugins::$html.= breadCrumbs();