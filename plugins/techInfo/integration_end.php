<?php
// list($doc,$xpath)= \Plugins::getXpath();
extract(\Plugins::getDocXpath());

// tolog(['\Plugins::getXpath()'=>\Plugins::getXpath(), '$xpath'=>$xpath]);

$node= $xpath->query('//div[@id="ajax-content"]')->item(0);

if($node){
	$info= $doc->createElement('div');
	$info->setAttribute('class', 'core info');

	Site::setDOMinnerHTML($info, '<b>Technical Info: PHP-' . phpversion() . "</b> <p>Page generation - " . round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1e4)/10 . 'ms | Memory usage - now ( '. round (memory_get_usage()/1024) . ') max (' . round (memory_get_peak_usage()/1024) . ') kB</p>');

	$doc->normalize();

	$node->appendChild($info);

	\Plugins::$html= $doc->saveHTML();
}