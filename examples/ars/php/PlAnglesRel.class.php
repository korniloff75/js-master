<?php
/**
 ** Обработка относительных углов между Луной и др. планетами.
 */
class PlAnglesRel extends Graph
{
	private
		// *Шпаргалка
		$hint = [
			'Sun'=>[
		0=>'22.05.2020 17:38:46  2°04\'39"Gem 0',
		60=>'27.05.2020 19:02:18  6°56\'15"Leo 60',
		90=>'30.05.2020  3:29:49  9°11\'40"Vir 90',
		120=>'1.06.2020  9:27:41 11°20\'58"Lib 120',
		180=>'5.06.2020 19:12:17 15°34\'02"Sgr 180',
		120=>'10.06.2020 14:34:50 20°09\'51"Aqr 120',
		90=>'13.06.2020  6:23:35 22°42\'17"Psc 90',
		60=>'16.06.2020  0:10:43 25°19\'23"Ari 60',
		],
			'Mercury'=>[
		0=>'24.05.2020 11:09:16 23°39\'33"Gem 0',
		60=>'29.05.2020 13:32:23  1°04\'52"Vir 60',
		90=>'31.05.2020 21:15:46  3°59\'05"Lib 90',
		120=>'3.06.2020  2:40:54  6°28\'32"Sco 120',
		180=>'7.06.2020 14:05:41 10°34\'06"Cap 180',
		120=>'12.06.2020 12:06:30 13°32\'03"Psc 120',
		90=>'15.06.2020  2:06:54 14°23\'42"Ari 90',
		60=>'17.06.2020 15:02:58 14°45\'05"Tau 60',
		],
			'Venus'=>[
		0=>'24.05.2020  3:14:31 19°30\'08"Gem 0',
		60=>'28.05.2020 13:30:10 17°19\'23"Leo 60',
		90=>'30.05.2020 15:18:22 16°07\'44"Vir 90',
		120=>'1.06.2020 15:20:01 14°54\'36"Lib 120',
		180=>'5.06.2020 13:57:21 12°26\'18"Sgr 180',
		120=>'9.06.2020 19:14:54  9°55\'35"Aqr 120',
		90=>'12.06.2020  2:34:26  8°42\'42"Psc 90',
		60=>'14.06.2020 12:24:08  7°36\'37"Ari 60',
		],
			'Mars'=>[
		90=>'23.05.2020  2:43:18  6°45\'01"Gem 90',
		120=>'25.05.2020 14:57:30  8°26\'24"Cnc 120',
		180=>'30.05.2020  7:34:08 11°34\'42"Vir 180',
		120=>'3.06.2020 15:44:10 14°27\'20"Sco 120',
		90=>'5.06.2020 19:44:02 15°52\'56"Sgr 90',
		60=>'8.06.2020  2:06:30 17°21\'58"Cap 60',
		0=>'13.06.2020  2:12:25 20°36\'47"Psc 0',
		],
			'Jupiter'=>[
		120=>'22.05.2020  8:00:56 27°08\'43"Tau 120',
		180=>'27.05.2020  1:06:11 26°59\'44"Cnc 180',
		120=>'31.05.2020  9:16:34 26°47\'51"Vir 120',
		90=>'2.06.2020 10:39:48 26°41\'01"Lib 90',
		60=>'4.06.2020 11:36:18 26°33\'34"Sco 60',
		0=>'8.06.2020 18:05:25 26°15\'41"Cap 0',
		60=>'13.06.2020 12:44:35 25°52\'14"Psc 60',
		90=>'16.06.2020  0:49:24 25°38\'35"Ari 90',
		],
			'Saturn'=>[
		120=>'22.05.2020 17:12:00  1°50\'55"Gem 120',
		180=>'27.05.2020  9:42:07  1°44\'40"Leo 180',
		120=>'31.05.2020 17:19:36  1°37\'08"Lib 120',
		90=>'2.06.2020 18:37:36  1°32\'59"Sco 90',
		60=>'4.06.2020 19:43:05  1°28\'29"Sgr 60',
		0=>'9.06.2020  3:16:35  1°17\'53"Aqr 0',
		60=>'13.06.2020 23:12:22  1°04\'16"Ari 60',
		90=>'16.06.2020 11:28:56  0°56\'30"Tau 90',
		],
			'Uranus'=>[
		60=>'25.05.2020 14:33:53  8°13\'44"Cnc 60',
		90=>'27.05.2020 21:33:52  8°20\'57"Leo 90',
		120=>'30.05.2020  2:14:40  8°27\'45"Vir 120',
		180=>'3.06.2020  6:16:31  8°40\'23"Sco 180',
		120=>'7.06.2020 11:08:02  8°52\'42"Cap 120',
		90=>'9.06.2020 17:29:35  8°59\'10"Aqr 90',
		60=>'12.06.2020  3:20:05  9°05\'51"Psc 60',
		0=>'17.06.2020  4:15:12  9°19\'17"Tau 0',
		],
			'Neptune'=>[
		90=>'24.05.2020  5:33:37 20°43\'05"Gem 90',
		120=>'26.05.2020 13:42:52 20°45\'15"Cnc 120',
		180=>'30.05.2020 23:13:34 20°48\'51"Vir 180',
		120=>'4.06.2020  2:14:07 20°51\'40"Sco 120',
		90=>'6.06.2020  4:10:20 20°52\'53"Sgr 90',
		60=>'8.06.2020  8:25:18 20°54\'01"Cap 60',
		0=>'13.06.2020  2:50:46 20°55\'58"Psc 0',
		],
			'Pluto'=>[
		120=>'22.05.2020  3:28:35 24°49\'47"Tau 120',
		180=>'26.05.2020 21:03:03 24°46\'07"Cnc 180',
		120=>'31.05.2020  5:46:07 24°42\'16"Vir 120',
		90=>'2.06.2020  7:22:02 24°40\'18"Lib 90',
		60=>'4.06.2020  8:26:29 24°38\'16"Sco 60',
		0=>'8.06.2020 15:00:54 24°33\'46"Cap 0',
		60=>'13.06.2020  9:56:09 24°28\'19"Psc 60',
		90=>'15.06.2020 22:21:42 24°25\'18"Ari 90',
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

					$d_val= abs($f - $moon[$ind]);
					$sign= $f - $moon[$ind] > 0? 1: -1;
					$d_val= $d_val>180? 360-$d_val: $d_val;
					$diff= abs($d_val - $a);

					/* $real_d_val= $f - $moon[$ind];
					$real_d_val= abs($real_d_val)>180? 360-abs($real_d_val): $real_d_val;
					$real_diff= $real_d_val - $a; */

					if (abs($diff) < abs($nearests[$name][$a]['diff']))
					{
						$cur= &$nearests[$name][$a];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'm_val'=> $moon[$ind],
							'val'=> $f,
							'd_val'=> $d_val * $sign,
							'sign'=> $sign,
							'diff'=> $diff,
							// 'real_diff'=> $real_diff,
							'ind'=> $ind,
							'range'=> [],
						];

						foreach([-1,1] as $d_ind)
						{
							$_i= $ind+$d_ind;

							if(empty($col[$_i]))
								continue;

							$d_val = abs($col[$_i] - $moon[$_i]);
							$d_val= $d_val>180? 360-$d_val: $d_val;
							$sign= $col[$_i] - $moon[$_i] > 0? 1: -1;

							$cur['range'][]= [
								'ts'=> $this->cols[0][$_i+1],
								'm_val'=> $moon[$_i],
								'val'=> $col[$_i],
								'd_val'=> $d_val * $sign,
								'sign'=> $sign,
								'diff'=> abs($d_val - $a),
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
						$n['diff'] = abs($n['val'] - $a);
					}
				} */

				usort($data['range'], function ($a, $b){
					return abs($a['diff']) > abs($b['diff']);
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
