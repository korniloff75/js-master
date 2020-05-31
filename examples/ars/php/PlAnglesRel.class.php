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

	public
		$tss=[];


	/**
	 *
	 */
	public function __construct()
	{
		$this->hint = EntryPointGraph::HINT['rel'];

		parent::__construct()
			->FindNearests();
			// ->CollectTSS();

		// *Controls

		// echo "<hr><h3>".__METHOD__."</h3>nearests_rel<br>";
		// var_dump($this->nearests_rel);

		return $this;
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

		// * {array} $col - последовательность углов
		foreach($this->cols as $name=>$col)
		{
			if(
				is_numeric($name)
				|| $name === 'Moon'
			) continue;
			// echo $name;

			$nearests[$name]= [];

			// *{float} $f - Углы планеты $name
			foreach($col as $ind=>$f)
			{
				$ts_ind= $ind+1;

				$d_val= abs($f - $moon[$ind]);
				$sign= $f - $moon[$ind] > 0? 1: -1;

				$d_val_180 = $d_val;
				$d_val_0= $f - $moon[$ind] + 180;

				$d_val= $d_val>180
					? 360-$d_val
					: $d_val;

				// *Искомые углы
				foreach([ 0, 60, 90, 120, 180 ] as $a)
				{
					$nearests[$name][$a] = $nearests[$name][$a] ?? [];

					$cur= &$nearests[$name][$a];

					$diff_abs= abs($d_val - $a);

					$item= [
						'ts'=> $this->cols[0][$ts_ind],
						'ts_date'=> "control - " . date('d.m.Y - H:i:s', $this->cols[0][$ts_ind]),
						'm_val'=> $moon[$ind],
						'val'=> $f,
						'd_val'=> $d_val,
						// 'd_val'=> $d_val * $sign,
						'd_val_0'=> $d_val_0,
						'd_val_180'=> $d_val_180,
						'sign'=> $sign,
						'a' => $a,
						'name' => $name,
						'diff_abs'=> $diff_abs,
						'ind'=> $ind,
						'range'=> [],
					];

					if(!empty($cur[$ind-1]))
					{
						$item['direct'] = $d_val_180 - $cur[$ind-1]['d_val_180'];
					}

					foreach([-1,1] as $d_ind)
					{
						$_i= $ind+$d_ind;

						if(empty($col[$_i]))
							continue;

						$d_val_i = abs($col[$_i] - $moon[$_i]);
						$d_val_0= $col[$_i] - $moon[$_i] + 180;
						$d_val_180= $d_val_i;

						$d_val_i= $d_val_i>180
							? 360-$d_val_i
							: $d_val_i;

						$sign_i= $col[$_i] - $moon[$_i] > 0? 1: -1;

						$item['range'][]= [
							'ts'=> $this->cols[0][$_i+1],
							'ts_date'=> "control - " . date('d.m.Y - H:i:s', $this->cols[0][$_i+1]),
							'm_val'=> $moon[$_i],
							'val'=> $col[$_i],
							'd_val'=> $d_val_i,
							// 'd_val'=> $d_val_i * $sign_i,
							'd_val_0'=> $d_val_0,
							'd_val_180'=> $d_val_180,
							'sign'=> $sign_i,
							'diff_abs'=> abs($d_val_i - $a),
							'ind'=> $_i,
						];
					}


					usort($item['range'], function ($a, $b){
						return $a['diff_abs'] - $b['diff_abs'];
					});

					$cur[]= $item;

				} //* $a iter, $cur defined

			} //* $col iter

			// *Фильтруем и вычисляем
			$this->findExact($name, $nearests[$name]);

		} //* $names iter

		echo "<h3>".__METHOD__." - \$this->tss</h3>";
		var_dump($this->tss);
		echo "<h3>".__METHOD__." - \$this->nearests_rel</h3>";
		// var_dump($this->nearests_rel);

		return $this;
	}


	private function findExact(string $name, array &$angles)
	{
		foreach($angles as $a=>&$cur)
		{

			foreach($cur as $ind=>&$data)
			{
				$val_1= $data['val'];
				// *Разница с Луной
				$d_val_1= &$data['d_val'];

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];
				$d_val_2= &$data_2['d_val'];

				// *Приведённое значение угла
				$a_reduced = $a;

				//!
				if($a === 180)
				{
					$d_val_1= $data['d_val_180'];
					$d_val_2= $data_2['d_val_180'];
				}
				elseif($a === 0)
				{
					// $a_true = 0;
					$a_reduced = 180;

					$d_val_1 = $data['d_val_0'];

					$data_2= $data['range'][1] ?? $data['range'][0];
					$val_2= $data_2['val'];
					$d_val_2= $data_2['d_val_0'];

				}

				// todo find $_P
				// $_P = 'Uranus';
				$_P = 'Sun';
				if(
					$name === $_P && $data['a'] == 180
					&& (
						strpos($data['ts_date'], '03.06.2020 - 11:31:') !== false
						|| strpos($data['ts_date'], '03.06.2020 - 14:31:') !== false
					)
					// && $data['ts'] === 1591615911
				)
				{
					echo "<h4>$_P</h4>a={$data['a']}\n" . abs($d_val_1 - $d_val_2) . "\n";
					var_dump($data);
					var_dump(
						$a_reduced, $d_val_1, $d_val_2,
						$a_reduced > $d_val_1 && $a_reduced > $d_val_2,
						$a_reduced < $d_val_1 && $a_reduced < $d_val_2
					);
				}
				//? find $_P

				// *Выкидываем данные вне диапазонов
				/* if(
					$a * $data['sign'] > $d_val_1 && $a * $data_2['sign'] > $d_val_2
					|| $a * $data['sign'] < $d_val_1 && $a * $data_2['sign'] < $d_val_2
					|| $a !== 0 && (
						abs($d_val_1 - $d_val_2) > 30
					)
					|| $data['diff_abs'] > 30
					// || $data['diff_abs'] > 10
				) */
				if(
					$a_reduced > $d_val_1 && $a_reduced > $d_val_2
					|| $a_reduced < $d_val_1 && $a_reduced < $d_val_2
					|| $a_reduced !== 0 && (
						abs($d_val_1 - $d_val_2) > 30
					)
					|| $data['diff_abs'] > 10
				)
				{
					unset($cur[$ind]);
					continue;
				}

				//note Составляем пропорцию

				$data['exact']= round(($data['ts'] * ($a_reduced - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a_reduced)) / ($d_val_1 - $d_val_2));
				// $data['exact']= round(($data['ts'] * ($a * $data_2['sign'] - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2));
				// note $data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));

				$data['deg']= ($val_1 * ($a_reduced - $d_val_2) + $val_2 * ($d_val_1 - $a_reduced)) / ($d_val_1 - $d_val_2);
				// $data['deg']= ($val_1 * ($a * $data_2['sign'] - $d_val_2) + $val_2 * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2);

				// *Удаляем дубли

				/* if(
					!empty($cur[$ind-1])
					&& $data['exact'] === $cur[$ind-1]['exact']
				)
				{
					unset($cur[$ind-1]);
					continue;
				} */

				$this->CollectTSS($data);

				// *Сравниваем со шпаргалкой
				$data['exact_date']= "<b>$name exact relative - $a deg." . date('d.m.Y - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? json_encode($this->hint[$name][$a], JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES) : "А нэту пока...") . ' - relative with the Moon</b>';
			}

			if(empty($cur))
			{
				unset($angles[$a]);
			}
			else
			{
				// $cur = array_values($cur);
			}

		}

	}

	/* private function findExact__(string $name, array &$cur, $a)
	{
		foreach($cur as $ind=>&$data)
		{
			$val_1= $data['val'];
			// *Разница с Луной
			$d_val_1= $data['d_val'];

			$data_2= $data['range'][0];
			$val_2= $data_2['val'];
			$d_val_2= $data_2['d_val'];

			// todo find $_P
			$_P = 'Uranus';
			if($name === $_P && $data['a'] == 120)
			{
				echo "<h4>$_P</h4>a={$data['a']}\n" . abs($d_val_1 - $d_val_2) . "\n";
				var_dump($data);
			}
			// * /todo

			// *Выкидываем данные вне диапазонов

			if(
				$a > $d_val_1 && $a > $d_val_2
				|| $a < $d_val_1 && $a < $d_val_2
				|| $a !== 0 && (
					abs($d_val_1 - $d_val_2) > 30
				)
				|| $data['diff_abs'] > 30
				// || $data['diff_abs'] > 10
			)
			{
				unset($cur[$ind]);
				continue;
			}

			//note Составляем пропорцию

			$data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));
			// $data['exact']= round(($data['ts'] * ($a * $data_2['sign'] - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2));
			// note $data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));

			$data['deg']= ($val_1 * ($a - $d_val_2) + $val_2 * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2);
			// $data['deg']= ($val_1 * ($a * $data_2['sign'] - $d_val_2) + $val_2 * ($d_val_1 - $a * $data['sign'])) / ($d_val_1 - $d_val_2);

			// *Удаляем дубли

			if(
				!empty($cur[$ind-1])
				&& $data['exact'] === $cur[$ind-1]['exact']
			)
			{

				unset($cur[$ind-1]);
				continue;
			}

			$this->CollectTSS($data);

			// *Сравниваем со шпаргалкой
			$data['exact_date']= "<b>$name exact relative - $a deg." . date('d.m.Y - H:i:s', $data['exact']) . '</b>';
			$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? json_encode($this->hint[$name][$a], JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES) : "А нэту пока...") . ' - relative with the Moon</b>';
		}

	} */

	protected function CollectTSS(array &$data)
	{
		$this->tss[$data['exact']] = [
			'pl' => $data['name'],
			'a' => $data['a'],
			'date' => date('d.m.Y - H:i:s', $data['exact']),
			'deg' => EntryPointGraph::ConvertDeg($data['deg']) ?? "{$data['a']}°",
			'cat' => $data['name'] === 'Moon'? 'abs':'rel',
		];

		return $this->tss[$data['exact']];
	}

}
