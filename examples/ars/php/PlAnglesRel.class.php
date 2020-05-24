<?php
/**
 ** Обработка относительных углов между Луной и др. планетами.
 */
class PlAnglesRel extends Graph
{
	private
		// *Шпаргалка
		$hint;

	protected
		$nearests=[];

	protected
		$nearests_rel=[];


	/**
	 *
	 */
	public function __construct()
	{
		$this->hint = EntryPointGraph::HINT['rel'];

		parent::__construct()
			->FindNearests()
			->FindExact();

		// *Controls
		/* echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->acceptAngles(60,180),
		); */

		echo "<hr><h3>".__METHOD__."</h3>nearests_rel<br>";
		// var_dump($this->nearests_rel);

		return $this;
	}


	// *Генерируем углы для поиска
	protected function acceptAngles($start=0, $end=360)
	{
		for ($a=$start; $a <= $end; $a+=30) {
			yield $a;
		}
	}

	/**
	 *
	 */
	private function FindNearests()
	{
		$nearests= &$this->nearests_rel;
		// $ranges= [];

		$this->cols= &$this->json['columns'];
		$moon= &$this->cols['Moon'];

		// print_r($moon);

		foreach($this->cols as $name=>$col)
		{
			if(
				is_numeric($name)
				|| $name === 'Moon'
			) continue;
			// echo $name;

			$nearests[$name]= [];

			// *Углы планеты $name
			foreach($col as $ind=>$f)
			{
				$ts_ind= $ind+1;

				// *Искомые углы
				foreach([ 0, 60, 90, 120, 180 ] as $a)
				{
					$nearests[$name][$a] = $nearests[$name][$a] ?? ['diff_abs'=>1e5];

					$d_val= abs($f - $moon[$ind]);
					$sign= $f - $moon[$ind] > 0? 1: -1;

					$d_val= $d_val>180 && $a !== 180
						? 360-$d_val
						: $d_val;

					$diff_abs= abs($d_val - $a);

					if (abs($diff_abs) < abs($nearests[$name][$a]['diff_abs']))
					{
						$cur= &$nearests[$name][$a];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'ts_date'=> "control - " . date('Y/m/d - H:i:s', $this->cols[0][$ts_ind]),
							'm_val'=> $moon[$ind],
							'val'=> $f,
							'd_val'=> $d_val * $sign,
							'sign'=> $sign,
							'diff_abs'=> $diff_abs,
							'ind'=> $ind,
							'range'=> [],
						];

						foreach([-1,1] as $d_ind)
						{
							$_i= $ind+$d_ind;

							if(empty($col[$_i]))
								continue;

							$d_val = abs($col[$_i] - $moon[$_i]);
							// $d_val_180= $d_val;
							$d_val= $d_val>180 && $a !== 180
								? 360-$d_val
								: $d_val;
							// $d_val= $d_val>180? 360-$d_val: $d_val;
							$sign= $col[$_i] - $moon[$_i] > 0? 1: -1;

							$cur['range'][]= [
								'ts'=> $this->cols[0][$_i+1],
								'ts_date'=> "control - " . date('Y/m/d - H:i:s', $this->cols[0][$_i+1]),
								'm_val'=> $moon[$_i],
								'val'=> $col[$_i],
								'd_val'=> $d_val * $sign,
								// 'd_val_180'=> $d_val_180 * $sign,
								'sign'=> $sign,
								'diff_abs'=> abs($d_val - $a),
								'ind'=> $_i,
							];
						}
					};

				}

			}

		}

		// echo "<h3>".__METHOD__."</h3>";
		// var_dump($this->nearests_rel);

		return $this;
	}


	// *Находим точные значения для углов
	private function FindExact()
	{
		foreach($this->nearests_rel as $name=>&$angles)
		{
			$col= $this->cols[$name];

			foreach($angles as $a=>&$data)
			{
				$val_1= $data['val'];
				// *Разница с Луной
				$d_val_1= $data['d_val'];

				//! *Фиксим проход через 0
				/* if(
					$a === 0
					&& count($data['range']) == 2
					&& abs($data['range'][0]['val'] - $data['range'][1]['val']) > 180
				)
				{
					foreach($data['range'] as &$n)
					{
						$n['val'] = $n['val']<180? $n['val']: $n['val']-360;
						$n['diff_abs'] = abs($n['val'] - $a);
					}
				} */

				usort($data['range'], function ($a, $b){
					return abs($a['diff_abs']) > abs($b['diff_abs']);
				});

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];
				// $d_val_2= abs($val_2 - $data_2['m_val']);
				$d_val_2= $data_2['d_val'];

				/* var_dump(
					$a, $d_val_1, $d_val_2,
					($a > $d_val_1 && $a > $d_val_2),
					($a < $d_val_1 && $a < $d_val_2),
					($a > $d_val_1 && $a > $d_val_2
					|| $a < $d_val_1 && $a < $d_val_2),
					'==='
				); */


				// *Фильтруем значения вне диапазона

				/* if(
					$a > abs($d_val_1) && $a > abs($d_val_2)
					|| $a < abs($d_val_1) && $a < abs($d_val_2)
					// && $a !== 0
				) */
				if(
					$a * $data['sign'] > $d_val_1 && $a * $data_2['sign'] > $d_val_2
					|| $a * $data['sign'] < $d_val_1 && $a * $data_2['sign'] < $d_val_2
				)
				{
					// todo test 0
					if($a !== 180)
						unset($angles[$a]);
					else
					{
						$data['d_val_1']= $d_val_1;
						$data['d_val_2']= $d_val_2;
					}

					continue;
				}

				//note Составляем пропорцию

				$data['exact']= round(($data['ts'] * ($a * $data_2['sign'] - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2));
				// note $data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));

				// *Сравниваем со шпаргалкой
				$data['exact_date']= "<b>$name exact relative - $a deg." . date('Y/m/d - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? $this->hint[$name][$a] ?? '' : "А нэту пока...") . ' - relative with the Moon</b>';

				/* var_dump(
					$data['ts'], $data, $data_2
				); */
			}
		}

		/* var_dump(
			"<h3>".__METHOD__."</h3>"
			// , $this->cols
			// , $this->nearests_rel
		); */

	}

}
