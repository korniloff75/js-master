<?php
class PlAngles extends Graph
{
	private
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
		],
		$hint_relat = [

		],
		$nearests=[];



	/**
	 *
	 */
	public function __construct()
	{
		$start_time= microtime(true);

		parent::__construct()
			->FindNearests()
			->FindExact();

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


	public function CollectToJson()
	{
		$o=[['x']];
		foreach($this->nearests as $name=>$angles)
		{
			$col= $this->cols[$name];

			$cur= [$name];
			// $o[]= &$cur;

			foreach($angles as $a=>&$data)
			{
				// $o[0][]= $data['exact'] * 1e3;
				/* $data= array_merge_recursive([
					'range'=>
				], $data); */

				$this->logDates($name, $a, $data);

				$cur[]= $a;
			}
		}

		/* var_dump(
			__METHOD__
			, $o
			, $this->nearests
		); */

		return $this->GetJSON($o);
	}

	function logDates($name, $a, &$data)
	{
		$o= [];
		// *Выводим даты
		$o[]= "<b>$name exact - $a deg." . date('Y/m/d - H:i:s', $data['exact']) . '</b>';
		$o[]= "$name val_1 - {$data['val']} deg." . date('Y/m/d - H:i:s', $data['ts']);
		$o[]= "$name val_2 - {$data['range'][0]['val']} deg." . date('Y/m/d - H:i:s', $data['range'][0]['ts']);

		if($name === 'Moon' && !empty($this->hint[$a]))
		{
			$o[]= "<h3>Realy value - {$this->hint[$a]}</h3>";
		}
		$o[]= $data;

		print_r($o);
	}

}
