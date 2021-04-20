<?php
// *Получаем $doc и $xpath
extract(\Plugins::getDocXpath());

// tolog(['\Plugins::getXpath()'=>\Plugins::getXpath(), '$xpath'=>$xpath]);

if($node= $xpath->query('//div[@id="ajax-content"]')->item(0)){
	$info= $doc->createElement('div');
	$info->setAttribute('class', 'core info');

	Site::setDOMinnerHTML($info, '<b>'.\VERSION.' Engine Info: PHP-' . phpversion() . "</b> <p>Page generation (with all plugins) - " . round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1e4)/10 . 'ms | Memory usage - now ( '. round (memory_get_usage()/1024) . ') max (' . round (memory_get_peak_usage()/1024) . ') kB</p>');

	$doc->normalize();

	$node->appendChild($info);

	// *fix cyrillic utf-8
	\Plugins::$html= html_entity_decode($doc->saveHTML());
}