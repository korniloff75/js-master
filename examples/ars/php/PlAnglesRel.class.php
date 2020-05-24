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
			->FindNearests();

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
					$nearests[$name][$a] = $nearests[$name][$a] ?? [];
					// $nearests[$name][$a] = $nearests[$name][$a] ?? ['diff_abs'=>1e5];

					$d_val= abs($f - $moon[$ind]);
					$sign= $f - $moon[$ind] > 0? 1: -1;

					$d_val= $d_val>180 && $a !== 180
						? 360-$d_val
						: $d_val;

					$diff_abs= abs($d_val - $a);

					// if (abs($diff_abs) < abs($nearests[$name][$a]['diff_abs']))
					{
						$cur= &$nearests[$name][$a];
						$cur[$ind]= [
							'ts'=> $this->cols[0][$ts_ind],
							'ts_date'=> "control - " . date('d.m.Y - H:i:s', $this->cols[0][$ts_ind]),
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

							$cur[$ind]['range'][]= [
								'ts'=> $this->cols[0][$_i+1],
								'ts_date'=> "control - " . date('d.m.Y - H:i:s', $this->cols[0][$_i+1]),
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


					usort($cur[$ind]['range'], function ($a, $b){
						return $a['diff_abs'] > $b['diff_abs'];
					});

					$this->findExact($name, $cur, $a);

				} //* $a iter

			}

		} //* $names iter



		// echo "<h3>".__METHOD__."</h3>";
		// var_dump($this->nearests_rel);

		return $this;
	}

	// *Выкидываем данные вне диапазонов
	// note deprecated
	/* private function filterCur(&$cur, $a, $name)
	{
		$cur = array_filter($cur, function(&$data) use($a, $name){
			// *Разница с Луной
			$d_val_1= $data['d_val'];

			$data_2= $data['range'][0];
			$val_2= $data_2['val'];
			// $d_val_2= abs($val_2 - $data_2['m_val']);
			$d_val_2= $data_2['d_val'];

			if(
				$a * $data['sign'] > $d_val_1 && $a * $data_2['sign'] > $d_val_2
				|| $a * $data['sign'] < $d_val_1 && $a * $data_2['sign'] < $d_val_2
			)
			{
					// unset($angles[$a]);

				return false;
			}

			return true;
		});

		return $this;
	} */


	private function findExact(string $name, array &$cur, $a)
	{
		$last_ind = null;
		foreach($cur as $ind=>&$data)
		{
			$last_ind = $last_ind ?? $ind;
			// *Разница с Луной
			$d_val_1= $data['d_val'];

			$data_2= $data['range'][0];
			$val_2= $data_2['val'];
			$d_val_2= $data_2['d_val'];

			if(
				$a * $data['sign'] > $d_val_1 && $a * $data_2['sign'] > $d_val_2
				|| $a * $data['sign'] < $d_val_1 && $a * $data_2['sign'] < $d_val_2
				|| $a !== 0 && (
					abs($d_val_1 - $d_val_2) > 30
				)
				|| $data['diff_abs'] > 30
			)
			{
				unset($cur[$ind]);
				continue;
			}

			//note Составляем пропорцию

			$data['exact']= round(($data['ts'] * ($a * $data_2['sign'] - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2));
			// note $data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));

			// *Удаляем дубли
			/* if(
				!empty($cur[$last_ind])
				&& $data['exact'] === $cur[$last_ind]['exact']
			) */
			if(
				!empty($cur[$ind-1])
				&& $data['exact'] === $cur[$ind-1]['exact']
			)
			{
				/* var_dump(
					$ind,
					// $last_ind,
					$cur[$ind],
				); */
				unset($cur[$ind-1]);
				continue;
			}

			// *Сравниваем со шпаргалкой
			$data['exact_date']= "<b>$name exact relative - $a deg." . date('d.m.Y - H:i:s', $data['exact']) . '</b>';
			$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? json_encode($this->hint[$name][$a], JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES) : "А нэту пока...") . ' - relative with the Moon</b>';
		}

		return $data;
	}


	// *Находим точные значения для углов
	// note deprecated
	/* private function FindExact__()
	{
		foreach($this->nearests_rel as $name=>&$angles)
		{
			$col= $this->cols[$name];

			foreach($angles as $a=>&$data)
			{
				$val_1= $data['val'];
				// *Разница с Луной
				$d_val_1= $data['d_val'];

				usort($data['range'], function ($a, $b){
					return abs($a['diff_abs']) > abs($b['diff_abs']);
				});

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];
				// $d_val_2= abs($val_2 - $data_2['m_val']);
				$d_val_2= $data_2['d_val'];


				// *Фильтруем значения вне диапазона

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
				$data['exact_date']= "<b>$name exact relative - $a deg." . date('d.m.Y - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? $this->hint[$name][$a] ?? '' : "А нэту пока...") . ' - relative with the Moon</b>';

			}
		}
	} */

}
