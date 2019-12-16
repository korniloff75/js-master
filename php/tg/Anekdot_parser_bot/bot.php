<?php
require_once "WebHook.php";

# Ловим входящий поток
$json = file_get_contents('php://input');
# Кодируем в массив
$obj = json_decode($json, TRUE)['channel_post'] ?? [];

$baseDir = 'base';
if(!is_dir($baseDir)) mkdir($baseDir);
# Имя текущего файла для парсера
$currentName = $baseDir . '/' . time();
$currentFile = $currentName . '.json';

# Сканируем базу в массив, убираем точки
# Имена файлов по убыванию - SCANDIR_SORT_DESCENDING
$base = scandir($baseDir, SCANDIR_SORT_DESCENDING);
$base = array_filter($base, function($i) {
	return is_file($i);
});
# Получаем содержимое последнего файла в массив
$content = \H::json($base[0]);

TG::log([
	'echo "\$json =" ',
	'var_dump($GLOBALS[\'json\'])',
	'echo "\$content =" ',
	'var_dump($GLOBALS[\'content\'])',
], __FILE__, __LINE__);

// ob_start();

# Парсим раз в сутки
if (time() - filemtime($currentFile) > 2 * 3600) {
  // file older than 2 hours
} else {
  // file younger than 2 hours
}

# Parse site
if(!count($content))
{
	TG::log(['echo "!count(\$content) === true"', 'var_dump($GLOBALS[\'content\'])'], __FILE__, __LINE__);
	// $text=file_get_contents('http://anekdotov.net/');
	$doc = new DOMDocument();
	@$doc->loadHTMLFile('http://anekdot.ru/');
	$docEl = $doc->documentElement;

	/* $scripts = $doc->getElementsByTagName("script");
	var_dump($scripts);

	for ($i = 0; $i < $scripts->length; $i++){
		$docEl->removeChild($scripts->item($i));
	} */

	$texts = $doc->getElementsByTagName("div");
	TG::log(['echo "\$texts = "', 'var_dump($GLOBALS[\'texts\'])'], __FILE__, __LINE__);

	if($texts->length)
	{
		foreach($texts as $t) {
			$class = $t->attributes->getNamedItem('class');

			// if(!is_object($class) || $class->nodeValue !== 'text') {
				// continue;
			if(is_object($class) && $class->nodeValue === 'text') {
				echo $t->textContent;
				$content[] = $t->textContent;
				echo "\n***********\n";
			}

		}

		\H::json($currentFile, $content);
		shuffle($content);
	}

} // !count($content)

/* if (array_key_exists('message', $obj)) {
	$chat_id = $obj['message']['chat']['id'];
	$message = $obj['message']['text'];

} elseif (array_key_exists('callback_query', $obj)) {
		$chat_id = $obj['callback_query']['message']['chat']['id'];
		$message = $obj['callback_query']['data'];
} */

if (isset($obj['message_id']))
{
	$first_name="";
	$last_name="";
	$username="";

	$chat_id=$obj['chat']['id'];

	if ($obj['chat']['type']=="private" || false)
	{
		if (isset($obj['chat']['first_name']))
			$first_name=$obj['chat']['first_name'];
		if (isset($obj['chat']['last_name']))
			$last_name=$obj['chat']['last_name'];
		if (isset($obj['chat']['username']))
			$username=$obj['chat']['username'];

		if ($first_name!="" AND $last_name!="")
			$text="<b>Здравствуйте, $first_name $last_name!</b>";
	} //private chat


	/* $text = iconv ( 'windows-1251' , 'utf-8' , $text );
	print_r($text);
	$text=strip_tags($text, '<b><strong><pre>'); */

	$postFields = [
		'chat_id' => $chat_id,
		'text' => $content[0],
		/* 'reply_markup' => $tg->getKeyBoard(
			[["text" => "Голосовать"], ["text" => "Помощь"
			]
		) */
	];

	TG::log(['echo "\$postFields = "', 'var_dump($GLOBALS[\'postFields\'])'], __FILE__, __LINE__);

	$tg->request($postFields);

} //message
else TG::log(['echo "Нет message!\n"',], __FILE__, __LINE__);

TG::log(['echo "count(\$content) = " . count($GLOBALS[\'content\'])',], __FILE__, __LINE__);

file_put_contents('4ControllerBot.md', array_map(function($i) {
	return $i . "\n\n";
}, $content));

// $log .= ob_get_clean(); // , FILE_APPEND
// json_encode(TG::$log, JSON_UNESCAPED_UNICODE) . "\n"
file_put_contents('log.txt', TG::$log);

die();
?>

<!-- <form action="">
	<textarea name="message">12321</textarea>
	<input type="submit">
</form> -->