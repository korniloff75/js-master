<pre>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$ch = curl_init();


		curl_setopt_array(
			$ch,
			[
				// CURLOPT_URL => "https://api.telegram.org/bot{$this->token}/{$method}",
				CURLOPT_URL => "http://kfftg.22web.org/?bot=bot{$this->token}&method={$method}",
				// CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 30,

				// CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V6,
				// CURLOPT_INTERFACE => '2001:67c:4e8:f004::9',

				// CURLOPT_PROXY =>  "http://kfftg.22web.org/bot{$this->token}/{$method}", // Worked !

				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,

				CURLOPT_POSTFIELDS => $postFields,
			]
		);
		$r = curl_exec($ch);