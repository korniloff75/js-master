<?php
class EntryPointGraph
{
	const
		TEST = 1,
		CACHE_ANGLES_PATH = __DIR__ . '/../angles.json',
		// *Путь к кэшу
		CACHE_PATH = __DIR__ . "/../cache.json",
		// *жизнь кэша, сек.
		CACHE_TIME = 3600 * 24;

	private
		$json;


	public function __construct()
	{
		$start_time= microtime(true);

		$this->cache();

		// *Controls
		echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->angles['abs']
			, $this->angles['rel']
		);

		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";
		echo "<h4>ОЗУ = " . memory_get_usage() / 1024 . " кБ</h4>";
		echo "<h4>ОЗУ (макс) = " . memory_get_peak_usage() / 1024 . " кБ</h4>";
	}


	/**
	 * Кеширование вычисленных углов с интервалом self::CACHE_TIME
	 */
	private function cache()
	{
		// *Cache read
		if(
			!(LOCAL && self::TEST)
			&& file_exists(self::CACHE_ANGLES_PATH)
			&& filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME > time()
			&& count(
				($this->angles = json_decode(file_get_contents(self::CACHE_ANGLES_PATH), 1))
			)
		)
		{
			$this->json = json_decode(file_get_contents(self::CACHE_PATH), 1);

			/* var_dump(
				time(),
				filemtime(self::CACHE_ANGLES_PATH),
				file_exists(self::CACHE_ANGLES_PATH),
				(filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME),
				(filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME < time())
			); */

		}
		// *Cache rewrite
		else
		{
			require_once './php/Graph.class.php';
			require_once './php/PlAnglesRel.class.php';
			require_once './php/PlAngles.class.php';

			$Graph = new PlAngles();

			$this->json = &$Graph->json;
			$this->angles = &$Graph->angles;
			// $JSON = $Graph->GetJSON();

			file_put_contents(
				self::CACHE_ANGLES_PATH,
				json_encode(
					$Graph->angles,
					JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
				)
			);
		}
	}


	public function GetJSON($arr=null)
	{
		return json_encode($arr ?? $this->json);
	}
}