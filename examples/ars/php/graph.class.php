<?php

class Graph
{
	const
		SWETEST_PATH = __DIR__ . "/../swetest.exe",
		// see $modify - https://www.php.net/manual/ru/datetime.modify.php
		DELTA_DATE = '10 day',
		// *Шаг запуска программы, сек.
		EXEC_STEP = 3600 * 8,
		INTER_PARTS = 2,
		// *Путь к кэшу
		CACHE_PATH = __DIR__ . "/../cache.json",
		// *жизнь кэша, сек.
		CACHE_TIME = 3600 * 24;

	protected $cols;

	public $json= [
		'columns'=> [
			['x']
		]
	];


	/**
	 * *Собираем данные для графика за период [now ... DELTA_DATE] в $this->json
	 */
	public function __construct()
	{
		echo "<h3>".LOCAL."</h3>";

		// *set date range
		$rangeDate = [
			// (new DateTime())->modify("-1 day")->getTimestamp(),
			(new DateTime())->modify("-".self::DELTA_DATE)->getTimestamp(),
			(new DateTime())->modify("+".self::DELTA_DATE)->getTimestamp()
		];

		$this->cols= &$this->json['columns'];

		// *Получаем $this->json
		$this->cache($rangeDate);

		// *Controls
		var_dump(
			// $this->json,
			// $this->cols,
			self::SWETEST_PATH
			, file_exists(self::SWETEST_PATH)
			, realpath('.')
			, $rangeDate
		);

		return $this;
	}


	protected function ExecSwetest($rangeDate)
	{

		for($ts=$rangeDate[0]; $ts<=$rangeDate[1]; $ts+=self::EXEC_STEP)
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
		// *Cache read
		if(
			file_exists(self::CACHE_PATH)
			&& filemtime(self::CACHE_PATH) + self::CACHE_TIME > time()
		)
		{
			/* var_dump(
				time(),
				filemtime(self::CACHE_PATH),
				file_exists(self::CACHE_PATH),
				(filemtime(self::CACHE_PATH) + self::CACHE_TIME),
				(filemtime(self::CACHE_PATH) + self::CACHE_TIME < time())
			); */

			$this->cacheData = json_decode(file_get_contents(self::CACHE_PATH), 1);
			$this->json= &$this->cacheData;
		}
		// *Cache rewrite
		else
		{
			// todo
			$this->ExecSwetest($rangeDate);
			// !
			$this->Interpolation();

			file_put_contents(self::CACHE_PATH, $this->GetJSON());
		}
	}


	public function GetJSON($arr=null)
	{
		return json_encode($arr ?? $this->json);
	}

}
