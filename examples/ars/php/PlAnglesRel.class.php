<?php
/**
 ** Обработка относительных углов между Луной и др. планетами.
 */
class PlAnglesRel extends Graph
{
	private
		// *Шпаргалка
		$hint = [
			'Pluto'=>[
				120=>'22.05.2020  3:28:35 24°49\'47"Tau 120',
				180=>'26.05.2020 21:03:03 24°46\'07"Cnc 180',
				120=>'31.05.2020  5:46:07 24°42\'16"Vir 120',
			],
			'Jupiter'=>[
				120=>'22.05.2020  8:00:56 27°08\'43"Tau 120',
				180=>'27.05.2020  1:06:11 26°59\'44"Cnc 180',
				120=>'31.05.2020  9:16:34 26°47\'51"Vir 120',
			],
			'Saturn'=>[
				120=>'22.05.2020 17:12:00  1°50\'55"Gem 120',
				180=>'27.05.2020  9:42:07  1°44\'40"Leo 180',
				120=>'31.05.2020 17:19:36  1°37\'08"Lib 120',
				],
			'Sun'=>[
				0=>'22.05.2020 17:38:46  2°04\'39"Gem 0',
				60=>'27.05.2020 19:02:18  6°56\'15"Leo 60',
				90=>'30.05.2020  3:29:49  9°11\'40"Vir 90',
			],
			'Mars'=>[
				90=>'23.05.2020  2:43:18  6°45\'01"Gem 90',
				120=>'25.05.2020 14:57:30  8°26\'24"Cnc 120',
				180=>'30.05.2020  7:34:08 11°34\'42"Vir 180',
			],
			'Venus'=>[
				0=>'24.05.2020  3:14:31 19°30\'08"Gem 0',
				60=>'28.05.2020 13:30:10 17°19\'23"Leo 60',
				90=>'30.05.2020 15:18:22 16°07\'44"Vir 90',
			],
			'Neptune'=>[
				90=>'24.05.2020  5:33:37 20°43\'05"Gem 90',
				120=>'26.05.2020 13:42:52 20°45\'15"Cnc 120',
				180=>'30.05.2020 23:13:34 20°48\'51"Vir 180',
			],
			'Mercury'=>[
				0=>'24.05.2020 11:09:16 23°39\'33"Gem 0',
				60=>'29.05.2020 13:32:23  1°04\'52"Vir 60',
				90=>'31.05.2020 21:15:46  3°59\'05"Lib 90',
			],
			'Uranus'=>[
				60=>'25.05.2020 14:33:53  8°13\'44"Cnc 60',
				90=>'27.05.2020 21:33:52  8°20\'57"Leo 90',
				120=>'30.05.2020  2:14:40  8°27\'45"Vir 120',
			],

		];

	protected
		$nearests=[];

	protected
		$nearests_rel=[];


	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct()
			->FindNearests()
			->FindExact();

		// *Controls
		echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->acceptAngles(60,180),
		);

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
					$nearests[$name][$a] = $nearests[$name][$a] ?? ['diff'=>1e5];

					$diff= abs(abs($f - $moon[$ind]) - $a);

					if ($diff < $nearests[$name][$a]['diff'])
					{

						// $this->FindExact($col, $ind);

						$cur= &$nearests[$name][$a];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'm_val'=> $moon[$ind],
							'val'=> $f,
							'd_val'=> abs($f - $moon[$ind]),
							// 'rel_val'=> $f,
							'diff'=> $diff,
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
								'm_val'=> $moon[$_i],
								'val'=> $col[$_i],
								'd_val'=> abs($col[$_i] - $moon[$_i]),
								'diff'=> abs(abs($col[$_i] - $moon[$_i]) - $a),
								'ind'=> $_i,
							];
						}
					};

				}

			}

		}

		echo "<h3>".__METHOD__."</h3>";
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
				$d_val_1= abs($val_1 - $data['m_val']);

				usort($data['range'], function ($a, $b){
					return $a['diff'] > $b['diff'];
				});

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];
				$d_val_2= abs($val_2 - $data_2['m_val']);

				/* var_dump(
					$a, $d_val_1, $d_val_2,
					($a > $d_val_1 && $a > $d_val_2),
					($a < $d_val_1 && $a < $d_val_2),
					($a > $d_val_1 && $a > $d_val_2
					|| $a < $d_val_1 && $a < $d_val_2),
					'==='
				); */

				// *Фильтруем значения вне диапазона

				if(
					$a > $d_val_1 && $a > $d_val_2
					|| $a < $d_val_1 && $a < $d_val_2
				)
				{
					unset($angles[$a]);
					continue;
				}

				//note Составляем пропорцию

				$data['exact']= round(($data['ts'] * ($a - $d_val_2) + $data_2['ts'] * ($d_val_1 - $a)) / ($d_val_1 - $d_val_2));

				// *Сравниваем со шпаргалкой
				$data['exact_date']= "<b>$name exact relative - $a deg." . date('Y/m/d - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint - $a deg. " . (!empty($this->hint[$name])? $this->hint[$name][$a] ?? '' : "А нэту пока...") . ' - relative with the Moon</b>';

				/* var_dump(
					$data['ts'], $data, $data_2
				); */
			}
		}

		var_dump(
			"<h3>".__METHOD__."</h3>"
			// , $this->cols
			// , $this->nearests_rel
		);

	}


	public function CollectToJson(?array &$nearests=null)
	{
		$nearests = $nearests ?? $this->nearests;
		$o=[['x']];
		foreach($nearests as $name=>$angles)
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

		var_dump(
			__METHOD__
			// , $o
			// , $this->nearests
		);

		return $this->GetJSON($o);
	}

	protected function logDates($name, $a, &$data)
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
