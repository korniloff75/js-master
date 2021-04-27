<?php

if(version_compare(PHP_VERSION, '7.0') < 0)
{
	die("<h2>Для работы скрипта " . basename(__FILE__) . " требуется версия РНР выше 7.0 !!!</h2>");
}

define('LOCAL', ($_SERVER['HTTP_HOST'] === "js-master"));


class EntryPointGraph
{
	const
		TEST = 1,
		CACHE_ANGLES_PATH = __DIR__ . '/../angles.json',
		CACHE_TSS_PATH = __DIR__ . '/../tss.json',
		// *Путь к кэшу
		CACHE_PATH = __DIR__ . "/../cache.json",
		// *жизнь кэша, сек.
		CACHE_TIME = 3600 * 24,

		// *Шпаргалка
		HINT = [
			'abs'=> [
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
			'rel'=> [
				'Sun'=>[
			0=>'22.05.2020 17:38:46  2°04\'39"Gem 0',
			180=>'5.06.2020 19:12:17 15°34\'02"Sgr 180',
			120=>['1.06.2020  9:27:41 11°20\'58"Lib 120', '10.06.2020 14:34:50 20°09\'51"Aqr 120'],
			90=>['30.05.2020  3:29:49  9°11\'40"Vir 90', '13.06.2020  6:23:35 22°42\'17"Psc 90'],
			60=>['27.05.2020 19:02:18  6°56\'15"Leo 60', '16.06.2020  0:10:43 25°19\'23"Ari 60'],
			],
				'Mercury'=>[
			0=>'24.05.2020 11:09:16 23°39\'33"Gem 0',
			180=>'7.06.2020 14:05:41 10°34\'06"Cap 180',
			120=>['3.06.2020  2:40:54  6°28\'32"Sco 120', '12.06.2020 12:06:30 13°32\'03"Psc 120'],
			90=>['31.05.2020 21:15:46  3°59\'05"Lib 90', '15.06.2020  2:06:54 14°23\'42"Ari 90'],
			60=>['29.05.2020 13:32:23  1°04\'52"Vir 60', '17.06.2020 15:02:58 14°45\'05"Tau 60'],
			],
				'Venus'=>[
			180=>'5.06.2020 13:57:21 12°26\'18"Sgr 180',
			120=>['1.06.2020 15:20:01 14°54\'36"Lib 120', '9.06.2020 19:14:54  9°55\'35"Aqr 120'],
			90=>['30.05.2020 15:18:22 16°07\'44"Vir 90', '12.06.2020  2:34:26  8°42\'42"Psc 90'],
			60=>['28.05.2020 13:30:10 17°19\'23"Leo 60', '14.06.2020 12:24:08  7°36\'37"Ari 60'],
			0=>['24.05.2020  3:14:31 19°30\'08"Gem 0', '19.06.2020  8:39:35  6°01\'30"Gem 0'],
			],
				'Mars'=>[
			180=>'30.05.2020  7:34:08 11°34\'42"Vir 180',
			120=>['25.05.2020 14:57:30  8°26\'24"Cnc 120', '3.06.2020 15:44:10 14°27\'20"Sco 120'],
			0=>'13.06.2020  2:12:25 20°36\'47"Psc 0',
			60=>['8.06.2020  2:06:30 17°21\'58"Cap 60', '18.06.2020  9:16:21 23°59\'38"Tau 60'],
			90=>['23.05.2020  2:43:18  6°45\'01"Gem 90', '5.06.2020 19:44:02 15°52\'56"Sgr 90', '20.06.2020 21:47:30 25°34\'53"Gem 90'],
			],
				'Jupiter'=>[
			180=>'27.05.2020  1:06:11 26°59\'44"Cnc 180',
			0=>'8.06.2020 18:05:25 26°15\'41"Cap 0',
			60=>['4.06.2020 11:36:18 26°33\'34"Sco 60', '13.06.2020 12:44:35 25°52\'14"Psc 60'],
			90=>['2.06.2020 10:39:48 26°41\'01"Lib 90', '16.06.2020  0:49:24 25°38\'35"Ari 90'],
			120=>['22.05.2020  8:00:56 27°08\'43"Tau 120', '31.05.2020  9:16:34 26°47\'51"Vir 120', '18.06.2020 12:02:06 25°24\'16"Tau 120'],
			],
				'Saturn'=>[
			180=>'27.05.2020  9:42:07  1°44\'40"Leo 180',
			0=>'9.06.2020  3:16:35  1°17\'53"Aqr 0',
			60=>['4.06.2020 19:43:05  1°28\'29"Sgr 60', '13.06.2020 23:12:22  1°04\'16"Ari 60'],
			90=>['2.06.2020 18:37:36  1°32\'59"Sco 90', '16.06.2020 11:28:56  0°56\'30"Tau 90'],
			120=>['22.05.2020 17:12:00  1°50\'55"Gem 120','31.05.2020 17:19:36  1°37\'08"Lib 120', '18.06.2020 22:34:03  0°48\'29"Gem 120'],
			],
				'Uranus'=>[
			180=>'3.06.2020  6:16:31  8°40\'23"Sco 180',
			120=>['30.05.2020  2:14:40  8°27\'45"Vir 120', '7.06.2020 11:08:02  8°52\'42"Cap 120'],
			90=>['27.05.2020 21:33:52  8°20\'57"Leo 90', '9.06.2020 17:29:35  8°59\'10"Aqr 90'],
			60=>['25.05.2020 14:33:53  8°13\'44"Cnc 60', '12.06.2020  3:20:05  9°05\'51"Psc 60'],
			0=>'17.06.2020  4:15:12  9°19\'17"Tau 0',
			],
				'Neptune'=>[
			180=>'30.05.2020 23:13:34 20°48\'51"Vir 180',
			120=>['26.05.2020 13:42:52 20°45\'15"Cnc 120', '4.06.2020  2:14:07 20°51\'40"Sco 120'],
			0=>'13.06.2020  2:50:46 20°55\'58"Psc 0',
			60=>['8.06.2020  8:25:18 20°54\'01"Cap 60','18.06.2020  3:18:01 20°57\'11"Tau 60'],
			90=>['24.05.2020  5:33:37 20°43\'05"Gem 90', '6.06.2020  4:10:20 20°52\'53"Sgr 90', '20.06.2020 13:06:51 20°57\'29"Gem 90'],
			],
				'Pluto'=>[
			180=>'26.05.2020 21:03:03 24°46\'07"Cnc 180',
			0=>'8.06.2020 15:00:54 24°33\'46"Cap 0',
			60=>['4.06.2020  8:26:29 24°38\'16"Sco 60', '13.06.2020  9:56:09 24°28\'19"Psc 60'],
			90=>['2.06.2020  7:22:02 24°40\'18"Lib 90', '15.06.2020 22:21:42 24°25\'18"Ari 90'],
			120=>['22.05.2020  3:28:35 24°49\'47"Tau 120', '31.05.2020  5:46:07 24°42\'16"Vir 120', '18.06.2020 10:00:39 24°22\'14"Tau 120'],
			],
			]
		];

	private
		$json;


	public function __construct()
	{
		$start_time= microtime(true);

		$this->cache();

		// *Controls
		echo "<h2>" . __CLASS__ . "</h2>";
		/* var_dump(
			$this->angles['abs']
			, $this->angles['rel']
		); */

		// *Timing
		$delta_time= (microtime(true) - $start_time) * 1000;
		echo "<h4>TimeExec = $delta_time ms</h4>";
		echo "<h4>ОЗУ = " . memory_get_usage() / 1024 . " кБ</h4>";
		echo "<h4>ОЗУ (макс) = " . memory_get_peak_usage() / 1024 . " кБ</h4>";
		echo "<hr>";
	}


	/**
	 * Кеширование вычисленных углов с интервалом self::CACHE_TIME
	 */
	private function cache()
	{
		// *Cache read
		if(
			!(LOCAL && self::TEST)
			&& file_exists(self::CACHE_ANGLES_PATH)
			&& filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME > time()
			&& count(
				($this->angles = json_decode(file_get_contents(self::CACHE_ANGLES_PATH), 1))
			)
			&& count(
				($this->tss = json_decode(file_get_contents(self::CACHE_TSS_PATH), 1))
			)
		)
		{
			ksort($this->tss);
			$this->json = json_decode(file_get_contents(self::CACHE_PATH), 1);

			/* var_dump(
				time(),
				filemtime(self::CACHE_ANGLES_PATH),
				file_exists(self::CACHE_ANGLES_PATH),
				(filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME),
				(filemtime(self::CACHE_ANGLES_PATH) + self::CACHE_TIME < time())
			); */

		}
		// *Cache rewrite
		else
		{
			require_once __DIR__.'/Graph.class.php';
			require_once __DIR__.'/PlAnglesRel.class.php';
			require_once __DIR__.'/PlAngles.class.php';

			$Graph = new PlAngles();

			$this->json = &$Graph->json;
			$this->angles = &$Graph->angles;
			ksort($this->tss = &$Graph->tss);
			/* usort($this->angles['abs']['Moon'], function($a,$b){
				return $a['ts'] - $b['ts'];
			}); */

			// $this->PlAnglesObj = &$Graph;
			// $JSON = $Graph->GetJSON();

			file_put_contents(
				self::CACHE_ANGLES_PATH,
				$this->GetJSON($Graph->angles)
			);
			file_put_contents(
				self::CACHE_TSS_PATH,
				$this->GetJSON($Graph->tss)
			);
		}
	}


	public function GetJSON(&$arr=null)
	{
		return json_encode($arr ?? $this->json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}


	public static function ConvertDeg($dec)
	{
		if(is_null($dec)) return null;

		$deg= floor($dec);
		$min= ($dec - $deg) * 60;
		$mm= floor($min);
		$ss= round(($min - $mm) * 60);

		// *convert to Zodiac
		$deg = $deg - floor($deg/30)*30;

		return "{$deg}° $mm' $ss\"";
	}



	public function CollectToJson(?array &$nearests=null, ?string $category= null)
	{
		$nearests ?? ($nearests = &$this->angles);

		$this->cols = &$this->json['columns'];

		if(
			empty($category)
		)
		{
			foreach($nearests as $cat=>&$val)
			{
				/* if($cat === 'abs')
				{
					$moon = &$val['Moon'];
					// $moon
					$val= $moon;
				}
				else  */
				$this->CollectToJson($val, $cat);
			}
			return;
		}

		foreach($nearests as $name=>&$angles)
		{
			if(
				!count($angles)
			)
				unset($nearests[$name]);

			foreach($angles as $a=>&$data)
			{
				/* $data= array_merge_recursive([
					'range'=>
				], $data); */

				if($category === 'abs')
				// if(!empty($data['exact']))
				{
					$data= [$data];
				}

				foreach($data as &$d)
				{
					// $this->logDates($category, $name, $a, $d);
					unset($d['range']);
				}

			}
		}

		var_dump(
			__METHOD__
			// , $this->nearests
		);

		// return $this->GetJSON($nearests);
	}



	private function logDates($category, $name, $a, &$data)
	{
		$hint = self::HINT[$category];
		$rel = $category === 'rel';

		$o= [];

		if($rel)
		{
			$prefix = "relative";
			$val = 'd_val';
			$o[]= "diff_abs = {$data['diff_abs']}";
			$o[]= "<b>{$data['deg']} deg. " . self::ConvertDeg($data['deg']) ."</b>";
		}
		else
		{
			$prefix = "abs";
			$val = 'val';
		}

		// *Выводим даты
		$o[]= "<b>$name $prefix exact - $a deg. " . date('d.m.Y - H:i:s', $data['exact']) . "</b> - {$data['exact']}";
		$o[]= "$name val_1 - {$data[$val]} deg. ({$data['val']}) " . date('d.m.Y - H:i:s', $data['ts']);
		$o[]= "$name val_2 - {$data['range'][0][$val]} deg. ({$data['range'][0]['val']}) " . date('d.m.Y - H:i:s', $data['range'][0]['ts']);

		// $real=
		if($name === 'Moon' && !$rel && !empty($hint[$a]))
		{
			$o[]= "<h3>Realy value - {$hint[$a]}</h3>";
		}
		elseif($rel && !empty($hint[$name][$a]))
		{
			$o[]= "<h3>Realy value</h3>";
			$o[]= $hint[$name][$a];
		}

		// $o[]= $data;

		print_r($o);
	}
}