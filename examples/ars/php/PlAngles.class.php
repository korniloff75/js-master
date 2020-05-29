<?php
/**
 ** Обработка абсолютных углов планет относительно нулевого меридиана Зодиака.
 */
class PlAngles extends PlAnglesRel
{
	const
		CACHE_ANGLES_PATH = __DIR__ . '/../angles.json';

	public $angles;

	private
		// *Шпаргалка
		$hint;


	/**
	 *
	 */
	public function __construct()
	{
		$this->hint = EntryPointGraph::HINT['abs'];

		parent::__construct()
			->FindNearests()
			->FindExact();

		$this->angles = [
			'abs'=> $this->nearests,
			'rel'=> $this->nearests_rel,
		];

		// *Controls
		/* echo "<h3>" . __CLASS__ . "</h3>";
		var_dump(
			$this->acceptAngles()
			// , $this->angles
		); */
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
		$nearests= &$this->nearests;
		// $graphData= [];

		$this->cols= &$this->json['columns'];

		foreach($this->cols as $name=>$col)
		{
			if(is_numeric($name)) continue;

			$nearests[$name]= [];

			// *Углы планеты $name
			foreach($col as $ind=>&$f)
			{
				$ts_ind= $ind+1;

				// *Искомые углы
				foreach($this->acceptAngles() as $a)
				{
					// var_dump($a);
					$nearests[$name][$a] = $nearests[$name][$a] ?? ['diff_abs'=>1e5];

					if (abs($f - $a) < $nearests[$name][$a]['diff_abs'])
					{

						// $this->FindExact($col, $ind);

						$cur= &$nearests[$name][$a];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'val'=> $f,
							'diff_abs'=> abs($f - $a),
							'ind'=> $ind,
							'name' => $name,
							'a' => $a,
							'range'=> [],
						];

						foreach([-1,1] as $d_ind)
						{
							$_i= $ind+$d_ind;

							if(empty($col[$_i]))
								continue;

							$cur['range'][]= [
								'ts'=> $this->cols[0][$_i+1],
								'val'=> $col[$_i],
								'diff_abs'=> abs($col[$_i] - $a),
								'ind'=> $_i,
							];
						}
					};

					// var_dump($cur);

				}

			} // *$col

			/* uasort($nearests[$name], function($a,$b){
				return $a['ts'] - $b['ts'];
			}); */

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

				// var_dump($data['range']);

				// *Фиксим проход через 0
				if(
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
				}

				usort($data['range'], function (&$n, &$p){
					return $n['diff_abs'] > $p['diff_abs'];
				});

				$data_2= $data['range'][0];
				$val_2= $data_2['val'];

				// *Фильтруем значения вне диапазона

				/* if($a == 0)
				{
					// $data['ttt'] = $data['range'];
					continue;
				} */

				if(
					$a > $val_1 && $a > $val_2
					|| $a < $val_1 && $a < $val_2
				)
				{
					// if($a == 0) continue;
					unset($angles[$a]);
					continue;
				}

				//note Составляем пропорцию

				$data['exact']= $data['exact'] ?? round(($data['ts'] * ($a - $val_2) + $data_2['ts'] * ($val_1 - $a)) / ($val_1 - $val_2));

				// *Сравниваем со шпаргалкой
				/* $data['exact_date']= "<b>$name exact abs - $a deg. " . date('Y/m/d - H:i:s', $data['exact']) . '</b>';
				$data['hint']= "<b>$name hint abs - $a deg. " . ($this->hint[$a] ?? "А нэту пока...") . '</b>'; */

				/* var_dump(
					$data['ts'], $data, $data_2
				); */

				if($name !== 'Moon') continue;

				// *Добавляем Moon abs -> tss
				$this->CollectTSS($data);
			}
		}

		var_dump(
			'$nearests= '
			// , $this->cols,
			// , $this->nearests
		);

	}

}
