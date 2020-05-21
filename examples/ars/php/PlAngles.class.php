<?php
/**
 ** Обработка абсолютных углов планет относительно нулевого меридиана Зодиака.
 */
class PlAngles extends PlAnglesRel
{
	protected
		// *Шпаргалка
		$hint = [
			30=>'20.05.2020  2:10:22  30',
			60=>'22.05.2020 13:35:44  60',
			90=>'24.05.2020 23:08:46  90,',
			120=>'27.05.2020  6:33:00  120,',
			150=>'29.05.2020 11:40:05  150,',
			180=>'31.05.2020 14:37:41  180, ',
			210=>'2.06.2020 16:05:29  210,',
			240=>'4.06.2020 17:16:49  240,',
			270=>'6.06.2020 19:44:09  270,',
			300=>'9.06.2020  0:53:57  300,',
		];



	/**
	 *
	 */
	public function __construct()
	{
		$start_time= microtime(true);

		parent::__construct()
			->FindNearests()
			->FindExact();

		// *Controls
		echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->angles(),
		);

		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";
		echo "<h4>ОЗУ = " . memory_get_usage() / 1024 . " кБ</h4>";
		echo "<h4>ОЗУ (макс) = " . memory_get_peak_usage() / 1024 . " кБ</h4>";
	}


	/**
	 *
	 */
	private function FindNearests()
	{
		$nearests= &$this->nearests;
		// $graphData= [];

		$this->cols= &$this->json['columns'];

		foreach($this->cols as $name=>$col)
		{
			if(is_numeric($name)) continue;
			echo $name;

			$nearests[$name]= [];

			// *Углы планеты $name
			foreach($col as $ind=>$f)
			{
				$ts_ind= $ind+1;

				// *Искомые углы
				foreach($this->angles() as $a)
				{
					$nearests[$name][$a] = $nearests[$name][$a] ?? ['diff'=>1e5];

					if (abs($f - $a) < $nearests[$name][$a]['diff'])
					{

						// $this->FindExact($col, $ind);

						$cur= &$nearests[$name][$a];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'val'=> $f,
							'diff'=> abs($f - $a),
							'ind'=> $ind,
							'range'=> [],
						];

						foreach([-1,1] as $d_ind)
						{
							$_i= $ind+$d_ind;

							if(empty($col[$_i]))
								continue;

							$cur['range'][$_i]= [
								'ts'=> $this->cols[0][$_i+1],
								'val'=> $col[$_i],
								'diff'=> abs($col[$_i] - $a),
								'ind'=> $_i,
							];
						}
					};

				}

			}

		}

		return $this;
	}


	// *Находим точные значения для углов
	private function FindExact()
	{
		foreach($this->nearests as $name=>&$angles)
		{
			$col= $this->cols[$name];

			foreach($angles as $a=>&$data)
			{
				$val_1= $data['val'];

				usort($data['range'], function ($a, $b){
					return $a['diff'] > $b['diff'];
				});

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];

				// *Фильтруем значения вне диапазона

				if(
					$a > $val_1 && $a > $val_2
					|| $a < $val_1 && $a < $val_2
				)
				{
					unset($angles[$a]);
					continue;
				}

				//note Составляем пропорцию

				$data['exact']= round(($data['ts'] * ($a - $val_2) + $data_2['ts'] * ($val_1 - $a)) / ($val_1 - $val_2));

				// *Сравниваем со шпаргалкой
				$data['exact_date']= "<b>$name exact - $a deg." . date('Y/m/d - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint - $a deg. with the Moon - " . ($this->hint[$a] ?? "А нэту пока...") . '</b>';

				/* var_dump(
					$data['ts'], $data, $data_2
				); */
			}
		}

		var_dump(
			'$nearests= '
			// , $this->cols,
			// , $this->nearests
		);

	}

}
