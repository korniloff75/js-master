<?php
class PlAngles extends Graph
{
	const
		ANGLES = [0,30,60,90,120,180,];

	private $angles=[];



	/**
	 * @param {string} $deltaDate
	 * $modify - https://www.php.net/manual/ru/datetime.modify.php
	 */
	public function __construct(string $deltaDate= '10 day')
	{
		$start_time= microtime(true);

		parent::__construct($deltaDate)
			->Find();

		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";

		// *Controls
		echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->angles(),

		);
	}


	// *Генерируем углы для поиска
	private function angles()
	{
		for ($a=0; $a <= 360; $a+=30) {
			yield $a;
		}
	}

	/**
	 *
	 */
	public function Find()
	{
		$diff= [];
		$nearests= [];

		$this->cols= &$this->json['columns'];

		// *Удаляем 'x'
		// unset($this->cols[0][0]);



		foreach($this->cols as $name=>$col)
		{
			if(is_numeric($name)) continue;
			echo $name;

			$nearests[$name]= [];

			// *Углы планеты $name
			foreach($col as $ind=>$f)
			{

				// *Искомые углы
				foreach($this->angles() as $a)
				{
					$nearests[$name][$a] = $nearests[$name][$a] ?? ['diff'=>1e5];

					if (abs($f - $a) < $nearests[$name][$a]['diff'])
					{

						$nearests[$name][$a]= [
							'ts'=> $this->cols[0][$ind+1],
							'val'=> $f,
							'diff'=> abs($f - $a),
						];
					};

				}

			}

		}


		var_dump(
			// $this->cols,
			$nearests
		);
	}

}
