<form action="" method="GET">
	<input type="submit" name="Update" value="Update">
</form>

<pre>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

if(empty($_GET['Update'])) die('NOT update');

$channels = [
	// "-1001418293454" => 'php/tg/Anekdot_parser_bot/bot.class.php',
	"-1001418293454" => realpath('Anekdot_parser_bot/bot.class.php'),
];

foreach($channels as $id => &$ch) {
	$json = json_encode([
		'callback_query' => [
			'chat' => ['id' => $id],
		]
	], JSON_UNESCAPED_UNICODE);

	/* $opts = [
		'http' => [
			'method' => 'POST',
			'header'  => 'Content-Type: application/json',
			'content' => $json,
		]
	]; */



	// $context = $context  = stream_context_create($opts);
	echo "$id<hr>\n";
	// echo file_get_contents($ch, false, $context);
	echo file_get_contents($ch . '?Update=Update&json=' . $json);
	var_dump(file_exists($ch . '/?Update=Update&json=' . $json));
	echo "<hr>\n";
	// echo file_get_contents($ch);
}