<?php

// var_dump($_REQUEST);


require_once __DIR__ . '/EntryPointGraph.class.php';

class fetchHandler
{
	// const CACHE_PATH = __DIR__ . "/../cache.json";

	protected $nearests;

	public function __construct()
	{
		$this->inputData = json_decode(file_get_contents('php://input'), 1);

		echo "<hr>";

		echo json_encode([
			'_GET'=>$_GET,
			'_POST'=>$_POST,
			'_REQUEST'=>$_REQUEST,
			// 'inputData'=>$inputData,
		]);

		echo "<hr>";
		$this->json = json_decode(file_get_contents(EntryPointGraph::CACHE_PATH), 1);
		$this->cols= &$this->json['columns'];

		$this->handleInputData();

		$this->FindNearests();
	}


	protected function handleInputData()
	{
		$this->json = json_decode(file_get_contents(EntryPointGraph::CACHE_PATH), 1);
		$this->cols= &$this->json['columns'];

		/* foreach($this->inputData as $name=>&$angles)
		{
			foreach($angles as $ca=>&$a)
				echo "{$name}[$ca] = ".implode(' / ',$a)."\n";
		} */
	}


	private function FindNearests()
	{
		$nearests= &$this->nearests;

		foreach($this->inputData as $name=>&$c_angles)
		{
			foreach($c_angles as $ca=>&$a_arr)
			{
				foreach($a_arr as &$a_cur)
				{
					foreach($this->cols[$name] as $ind=>&$fa)
					{
						$diff= $nearests[$name][$ca]['diff_abs'] ?? 1e5;
						$new_diff= abs($fa - $ca);

						if($new_diff>=$diff) continue;

						// echo "**diff = $diff\nnew_diff = $new_diff\n";

						// $nearests[$name][$ca][] = [
						$nearests[$name][$ca] = [
							'val'=> $fa,
							'a_cur'=> $a_cur,
							'diff_abs'=> $new_diff,
							'ind'=> $ind,
						];
					}
					echo "*{$name}[$ca] = $a_cur\n";

				}

				echo "<hr>";
			}

		}

		var_dump($nearests);
	}


	private function FindNearests_()
	{
		$nearests= &$this->nearests;

		foreach($this->cols as $name=>$col)
		{
			if(is_numeric($name)) continue;

			$nearests[$name]= [];

			// *Углы планеты $name
			foreach($col as $ind=>&$fa)
			{
				$ts_ind= $ind+1;

				// *Искомые углы
				foreach($this->acceptAngles() as $ca)
				{
					// var_dump($ca);
					$nearests[$name][$ca] = $nearests[$name][$ca] ?? ['diff_abs'=>1e5];

					if (abs($fa - $ca) < $nearests[$name][$ca]['diff_abs'])
					{

						// $this->FindExact($col, $ind);

						$cur= &$nearests[$name][$ca];
						$cur= [
							'ts'=> $this->cols[0][$ts_ind],
							'val'=> $fa,
							'diff_abs'=> abs($fa - $ca),
							'ind'=> $ind,
							'name' => $name,
							'a' => $ca,
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
								'diff_abs'=> abs($col[$_i] - $ca),
								'ind'=> $_i,
							];
						}
					};

					// var_dump($cur);

				}

			} // *$col

			/* uasort($nearests[$name], function($ca,$b){
				return $ca['ts'] - $b['ts'];
			}); */

		}

		return $this;
	}
}

new fetchHandler();