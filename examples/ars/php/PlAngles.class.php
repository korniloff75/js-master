<?php
class PlAngles
{
	const
		INTER_PARTS = 8;

	private $cols;



	/**
	 * @param $deltaDate
	 * *Собираем данные для графика за период -$deltaDate ... $deltaDate в $this->json
	 */
	public function __construct()
	{
		$start_time= microtime(true);



		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";

		// *Controls
		var_dump(
			// $this->cols,

		);
	}


	protected function ExecSwetest($rangeDate)
	{



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


				// *Фиксим переход 360->0
				if($cur > 350 && $next < 50)
				{
					$prev= $v[$n-1];
					$delta= ($cur - $prev) / self::INTER_PARTS;

					for ($i=1; $i < self::INTER_PARTS; $i++)
					{
						// if(!is_numeric($name) && abs($delta) < 180)
							$interCols[$name][]= $delta * $i;
					}
				}
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

	public function GetJSON()
	{

	}

}
