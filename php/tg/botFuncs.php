<?php
function curlToTG (array $postFields=[])
{
	$ch = curl_init();
	array_merge([
		// 'chat_id' => $chat_id,
		'parse_mode' => 'html',
		// 'text' => $text,
	], $postFields);
	curl_setopt_array(
		$ch,
		[
			CURLOPT_URL => "https://api.telegram.org/bot" . TOKEN . "/sendMessage",
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_POSTFIELDS => $postFields,
		]
	);
	return curl_exec($ch);
}