<?php

$LOCAL = $_SERVER['HTTP_HOST'] === "js-master";

class Graph
{
	const
		SWETEST_PATH = __DIR__ . "/../swetest.exe",
		INTER_PARTS = 8,
		// *Путь к кэшу
		CACHE_PATH = __DIR__ . "/../cache_db.json",
		// *жизнь кэша, сек.
		CACHE_TIME = 3600 * 24;

	private $cols;

	private $json= [
		'columns'=> [
			['x']
		]
	];


	/**
	 * @param $deltaDate
	 * *Собираем данные для графика за период [-$deltaDate ... $deltaDate] в $this->json
	 */
	public function __construct(string $deltaDate= '10 day')
	{
		echo "<h3>{$GLOBALS['LOCAL']}</h3>";

		$start_time= microtime(true);

		// *set date range
		$rangeDate = [
			(new DateTime())->modify("-$deltaDate")->getTimestamp(),
			(new DateTime())->modify("+$deltaDate")->getTimestamp()
		];

		$this->cols= &$this->json['columns'];

		// *Получаем $this->json
		$this->cache($rangeDate);

		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";

		// *Controls
		var_dump(
			// $this->cols,
			self::SWETEST_PATH
			, file_exists(self::SWETEST_PATH)
			, $start_time
			, $delta_time
			, realpath('.')
			, $rangeDate
		);
	}


	protected function ExecSwetest($rangeDate)
	{

		for($ts=$rangeDate[0]; $ts<=$rangeDate[1]; $ts+=3600*48)
		{
			$date = date('d.m.Y', $ts);
			$time = date('H:i:s', $ts);

			$this->cols[0][]= $ts;

			exec(self::SWETEST_PATH . " -edir\"./sweph/\" -b$date -ut$time -p0123456789 -eswe -fPls -g, -head", $outExec, $status);

			// var_dump($ts);
		}

		$outExec= array_values($outExec);

		// var_dump($outExec);

		// *Парсим вывод из программы

		foreach ($outExec as $line) {

			$row = preg_split('/\s*,\s*/', $line);

			if(count($row)<3)
				continue;

			// *Абсолютные углы, град.
			$this->cols[$row[0]][]= (float) $row[1];

		};

		return $this;
	}

	/**
	 * *Интерполируем промежуточные значения $this->cols
	 */
	private function Interpolation()
	{
		$interCols= [];

		foreach($this->cols as $name=>&$v)
		{
			foreach($v as $n=>&$cur)
			{
				$interCols[$name][]= $cur;

				if(
					empty($next= @$v[$n+1])
					|| !is_numeric($cur)
				) continue;


				// *Фиксим переход 360->0 deg.
				if($cur > 300 && $next < 100)
				{
					$nextnext= @$v[$n+2];
					$prev= $v[$n-1];
					$delta= ($nextnext? $nextnext - $next : $cur - $prev) / self::INTER_PARTS;

					for ($i=1; $i < self::INTER_PARTS; $i++)
					{
						// if(!is_numeric($name) && abs($delta) < 180)
						$nextIter = round($cur + $delta * $i);
						$interCols[$name][]= $nextIter <= 360? $nextIter : $nextIter - 360;
						// $interCols[$name][]= $cur - 360 + $delta * $i;
					}
				}
				// *Обычный режим 0->360 deg.
				else
				{
					$delta= ($next - $cur) / self::INTER_PARTS;

					for ($i=1; $i < self::INTER_PARTS; $i++)
					{
						// if(!is_numeric($name) && abs($delta) < 180)
							$interCols[$name][]= $cur + $delta * $i;
					}
				}

				// var_dump($cur);

			}

		}

		$this->cols = $interCols;
	}


	/**
	 * Кеширование с интервалом self::CACHE_TIME
	 */
	private function cache($rangeDate)
	{
		if(
			file_exists(self::CACHE_PATH)
			&& filemtime(self::CACHE_PATH) + self::CACHE_TIME > time()
		)
		{
			var_dump(
				time(),
				filemtime(self::CACHE_PATH),
				file_exists(self::CACHE_PATH),
				(filemtime(self::CACHE_PATH) + self::CACHE_TIME),
				(filemtime(self::CACHE_PATH) + self::CACHE_TIME < time())
			);

			$this->json= json_decode(file_get_contents(self::CACHE_PATH), 1);
		}
		// *Cache rewrite
		else
		{
			// todo
			$this->ExecSwetest($rangeDate)
			// !
			// ;
				->Interpolation();
			file_put_contents(self::CACHE_PATH, json_encode($this->json));
		}
	}


	public function GetJSON()
	{
		return json_encode($this->json);
	}

}
