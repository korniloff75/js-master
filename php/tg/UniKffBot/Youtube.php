<?php
require_once "UniConstruct.trait.php";


class Youtube extends UniKffBot {
	use UniConstruct;

	private
		$base = 'base_YT';

	protected
		$apiName = 'google';


	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd);

		$this->init();
	} //* __construct


	private function init()
	{
		$search = "Крым новости"; //  Поисковый запрос
		$limit = 1; // Количество результатов
		$res = &$this->youtube_search( $search, $limit) ;

		$this->log->add(__METHOD__ . '$res= ', null, [$res]);
		$media = [];
		$content = [];

		foreach($res['items'] as $i)
		{
			/* $media[]= [
				'type' => 'video',
				'media' => "https://www.youtube.com/embed/{$i['id']['videoId']}/",
				'thumb' => "{$i['snippet']['thumbnails']['medium']['url']}",
				'caption' => "{$i['snippet']['title']}",
			]; */


			// $this->log->add(__METHOD__ . '$i= ', null, $i);
			$title= urldecode($i['snippet']['title']);

			$this->apiRequest([
				'chat_id' => $this->id,
				'parse_mode' => 'html',
				'text' => "<b>{$title}</b>\n
				https://www.youtube.com/embed/{$i['id']['videoId']}",
			]);
		}
		// $this->sendMediaGroup($media);
	}

	function youtube_search($search, $limit)
	{
		$url = "https://www.googleapis.com/youtube/v3/search";
		$opts = [
			'sendMethod' => 'get',
			'params' => [
				'part'=> 'snippet',
				'q'=> urlencode($search),
				'type'=> 'video',
				'maxResults'=> $limit,
				'regionCode'=> 'RU',
				'key'=> $this->tokens['google'],
			],
		];

		return $this->CurlRequest($url, $opts);
	}

} //* Youtube
