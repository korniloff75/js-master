<?php
class GisMeteo extends CommonBot {
	// private


	public function __construct()
	{
		if(!$tokemGM = $this->tokens['gismeteo'])
		{
			$this->log->add(__METHOD__ . '$this->tokens = ', null, [$this->tokens]);
			die;
		}



		$this->requestGM($latitude, $longitude);

	}

	private function requestGM($latitude, $longitude)
	{
		return $this->CurlRequest('https://api.gismeteo.net/v2/weather/current/', [
			'method' => 'get',
			'headers' => ["X-Gismeteo-Token: {$this->tokens['gismeteo']}", "Accept-Encoding: deflate, gzip"],
			'params' => [
				'latitude' => $latitude,
				'longitude' => $longitude
			]
		]);
	}


} //* GisMeteo

new GisMeteo;