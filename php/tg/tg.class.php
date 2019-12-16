<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

# Устанавливать в дочерних классах
// define("TG_TEST", 1);


class TG {
	static
		$log=[];

	protected static
		$textLimit = 3500;

	protected
		$token = '';

	public function __construct($token)
	{
		$this->token = $token;
		// echo "\$token = $token";
	}

	/**
	 * TG::log([], __FILE__, __LINE__);
	 */
	public static function log($data=[], $file='', $line='')
	{
		echo "<div class='log' style='max-height: 200px; overflow: auto;'>";
		ob_start();
		echo basename($file) . " : $line\n";
		foreach($data as $i) {
			eval($i . ';');
			echo "\nOK ";
		}
		$log= \TG_TEST ? ob_get_flush() : ob_get_clean();
		echo "</div>";

		self::$log[]= $log;

	}

	public  function request(array $postFields = [], string $method = 'sendMessage')
	{
		$ch = curl_init();
		$postFields = array_merge([
			// 'chat_id' => $chat_id,
			'parse_mode' => 'html',
			// 'text' => $text,
		], $postFields);
		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL => "https://api.telegram.org/bot{$this->token}/{$method}",
				// CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_POSTFIELDS => $postFields,
			]
		);
		// return curl_exec($ch);
		return json_decode(curl_exec($ch), true);
	}


	public function getKeyBoard($data)
	{
	 $keyboard = [
		 "keyboard" => $data,
		 "one_time_keyboard" => false,
		 "resize_keyboard" => true
	 ];
	 return json_encode($keyboard);
	}

	/* public function buildKeyboard()
	{ // (object)


		$keyboard = [
			'keyboard' => [
				//row1
				[
					['text' => 'button1' ]
				]
			],
			'one_time_keyboard' => true,
		];
		// [[["text" => "Власть советам!"], ["text" => "Сиськи!"]]]

		return json_encode($keyboard, JSON_UNESCAPED_UNICODE);
	} */
}