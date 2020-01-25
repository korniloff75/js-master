<?php

function js_compress($dir)
{
	$fArr = glob($dir . '/*.js');
	$output = __DIR__ . '/'. basename($dir) . '.js';
	$upd = !file_exists($output);

	// if(DEV) var_dump(filemtime($output));

	if(!$upd) foreach($fArr as &$f)
	{
		// if(DEV) var_dump(filemtime($f));
		if(filemtime($f) > filemtime($output))
		{
			$upd = true;
			break;
		}
	}

	// if(DEV) echo "\$upd = $upd";
	if(!$upd) return;

	$script = '';

	foreach($fArr as &$f)
	{
		$script.= file_get_contents($f) . ';';
	}

	// var_dump($script);

	$script = preg_replace([
		"/^.+use strict.+?$/mu",
		"~(?<=[^:]|(?!data:.+?))//.+$~mu",
		'/(\s){2,}?/',
		'/\s*([=:\?{}|()\+\-\*,\/;])\s*/',
		'/([)}])(function)/',
		"/console\.log\(.+?\);?/",
		"~/\*.+?\*/~",
		"/[\r\n\t]+/",
		// "/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/",
	], ["", "", "$1", "$1", "$1;$2", ""], $script);

	$script = "'use strict';" . $script;

	file_put_contents($output, $script);

}

// js_compress();