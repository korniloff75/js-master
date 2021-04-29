<?php
namespace core\api;
/**
* Обработчик изменений контента
*/

\Site::protectScript(basename(__FILE__), 'checkAdm');
if(\DEMO) die ("DEMO enabled!");

extract ($_REQUEST);

if(empty($path) || empty($action))
{
	var_dump($_REQUEST);
	die ("Не корректные входящие данные");
}

// *fix PHP code
$clean_arr = [
	'&lt;?' => '<?',
	'?&gt;' => '?>',
];


switch ($action) {
	case 'load':
		$cont = file_get_contents(\DR . "/$path");
		echo strtr($cont, array_flip($clean_arr));
	break;

	case 'save':
		echo "saved in $path";
		# FIX php in content
		$art = strtr($art, $clean_arr);

		if(!file_put_contents(\DR . "/$path", $art)) die ('<p class="core warning">Данные не сохранены!</p>');
	break;
}

die;