<?php
require_once "WebHook.php";

$json = file_get_contents('php://input');
// $obj = $_REQUEST;
$obj = json_decode($json, TRUE)['channel_post'];
$content = [];


print_r($json);
echo "\n";
print_r($obj);

ob_start();

if (isset($obj['message_id']))
{
	$first_name="";
	$last_name="";
	$username="";
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
	// print_r($texts);

	/* $text = iconv ( 'windows-1251' , 'utf-8' , $text );
	print_r($text);
	$text=strip_tags($text, '<b><strong><pre>'); */



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

	shuffle($content);

	curlToTG([
		'chat_id' => $chat_id,
		'text' => $content[0],
	]);

}; //message

$text = ob_get_clean();

echo "<pre>$text</pre>";

	// print_r($text);
	// $text=urlencode ($text);



file_put_contents('log.txt', $text . "\n *** END *** \n");
// file_put_contents('text.txt', json_encode($text, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
?>

<!-- <form action="">
	<textarea name="message">12321</textarea>
	<input type="submit">
</form> -->