<?php

class Gismeteo extends CommonBot implements Game
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../base_GM',
		LIM_HOURS = 1;

	private
		$base = __DIR__.'/../base_GM';


	/**
	 * @param cmd - 'cmdName__opt1__opt2__...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->_init();

	} //* __construct


	private function _init()
	{
		//* Локация. Если нет в базе
		if(empty($this->getLocation()->location))
		{
			tolog('$this->location is EMPTY!', E_USER_WARNING, ['$this->location'=>$this->location]);

			//* Запрашиваем
			$this->userPermission();
			die;
		}

		//* Обработка команд
		switch ($this->cmd[0]) {
			case 'changeLocation':
				$this->userPermission("Поменяйте вашу геолокацию, нажав на кнопку внизу экрана.\n\nТекущие данные о погоде будут отображены сразу, а новые данные по прогнозу будут доступны в течение часа после вашего последнего запроса прогноза по прежней геолокации.");
				break;
			case 'forecast_aggregate':
				$this->requestGM('aggregate')
					->methodSwitcher();
				break;
			case 'by_day_part':
				$this->requestGM('by_day_part')->methodSwitcher();
				break;

			case 'Gismeteo':
			case 'gismeteo':
				$this->requestGM('current')->responseGMHandler();
				$forecastButs = [[]];
				for ($i=3; $i <= 5; $i++) {
					$forecastButs[0][]= [
						"text" => "$i " . ($i===5 ? 'дней' : 'дня'),
						"callback_data" => "/gismeteo/forecast_aggregate__$i"
					];
				}

				$forecastButs []= [[
					"text" => "Сменить локацию",
					"callback_data" => "/gismeteo/changeLocation"
				]];

				//* Выводим текущее положение
				$this->apiRequest(array_merge([
					'chat_id' => $this->user_id,
					'title' => "Текущие координаты",
					'reply_markup' => [
						"inline_keyboard" => $forecastButs,
						"one_time_keyboard" => true,
						"resize_keyboard" => true,
						"selective" => true
					]
				], $this->location), 'sendVenue');
				break;
		}

	} //* _init

	private function getLocation()
	:object
	{
		//* read base
		$this->locations = json_decode(
			@file_get_contents("{$this->base}/locations.json"), true
		) ?? [];

		//* Пришла новая локация
		if(!empty($this->location = @$this->message['location']))
		{
			$this->setLocation();
		}
		else
		{
			$this->location = $this->locations[$this->user_id][0] ?? null;
		}
		return $this;
	}

	private function setLocation()
	{
		$this->locations[$this->user_id] = [
			"0" => $this->location
		];

		file_put_contents("{$this->base}/locations.json", json_encode($this->locations, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES));
	}

	/**
	 *
	 */
	private function userPermission($text=null)
	{
		$text = $text ?? "Уточните свою геолокацию?\nБез данных о вашем местоположении бот не сможет отобразить для вас погоду.";

		$pf = [
			'chat_id' => $this->user_id,
			'text' => $text,
			'reply_markup' => [
				"keyboard" => [
					[[
						"text" => "Отправить локацию",
						"request_location" => true
					]],
					[['text' => self::BTNS['general']],]
				],
				"one_time_keyboard" => true,
				"resize_keyboard" => true,
				"selective" => true
			]
		];

		$this->apiResponseJSON($pf);
	}


	private function requestGM($method = 'current')
	:object
	{
		//* Имя файла с кешем
		$cacheFilename = "{$this->base}/{$this->user_id}.{$method}_" . implode('_', $this->cmd[1]) . ".json";
		if(!file_exists($this->base))
			mkdir($this->base, 0664);

		$params = $this->location;
		switch ($method) {
			//* Прогноз с шагом 6 часов
			case 'by_day_part':
			//* Прогноз с шагом 24 часа
			case 'aggregate':
				$params['days'] = $this->cmd[1][0];
				$urlAPI = "https://api.gismeteo.net/v2/weather/forecast/{$method}/";
				break;

			default:
				//* current
				$urlAPI = "https://api.gismeteo.net/v2/weather/{$method}/";
				break;
		}

		if(
			file_exists($cacheFilename)
			&& (time() - filemtime($cacheFilename) < self::LIM_HOURS * 3600)
			&& $this->cmd[0] !== 'setLocation'
		)
		{
			$this->responseData = json_decode(
				file_get_contents($cacheFilename), 1
			);
		}
		//* If old or not exist
		else
		{
			$this->responseData = $this->CurlRequest($urlAPI, [
				'sendMethod' => 'get',
				'headers' => ["X-Gismeteo-Token: {$this->tokens['gismeteo']}", "Accept-Encoding: deflate, gzip"],
				'params' => $params
			]);

			if($respJSON = json_encode($this->responseData, JSON_UNESCAPED_UNICODE|JSON_NUMERIC_CHECK|JSON_UNESCAPED_SLASHES))
				file_put_contents($cacheFilename, $respJSON, LOCK_EX);
		}

		// tolog(__METHOD__ . '$this->responseData = ', null, [$this->responseData]);

		return $this;
	} //* requestGM


	private function methodSwitcher()
	{
		$data = &$this->responseData['response'];
		if(!empty($data[0]))
		{
			foreach($data as $tod)
			{
				$this->responseGMHandler($tod);
			}
		}
		else
			$this->responseGMHandler($data);
	}

	//* Собираем иконки
	private function collectIcons($icon)
	{
		$picts = [
			'day' => [
				'c0' => '☀',
				'c1' => '⛅',
				'c2' => '⛅',
				'c3' => '☁',
				'r1' => '&#127782;',
				'r2' => '&#127783;☔',
				'r3' => '☔&#127783;☔',
				'st' => '⛈☔',
				's1' => '&#127784;',
				's2' => '&#127784;❄',
				's3' => '❄&#127784;❄',

			],
			'night' => [
				'c0' => '🌙',
				'c1' => '☁',
				'c2' => '☁',
				'r1' => '&#127783;',
			],
		];

		$icon = preg_replace('/(.*)rs(\d)(.*)/', '$1r$2_s$2$3', $icon);
		$arr = explode('_',$icon);
		$day = $arr[0] !== 'n';
		$out = '';

		if(!preg_match('/_?c\d/', $icon)) $arr[]= 'c0';

		foreach($arr as &$i)
		{
			if(preg_match('/^\w+\d/', $i, $r))
			{
				if($day) $out.= $picts[$day?'day':'night'][$i] ?? $picts['day'][$i] ?? '';
			}
		}

		return $out;
	} //* collectIcons($data['icon'])


	private function collectWeather(&$data)
	{
		$wind = ['штиль','Северный','Северо-восточный','Восточный','Юго-восточный','Южный',' 	Юго-западный','Западный','Северо-западный',];

		$precipitation = [
			'intensity' => ['нет','слабый','умеренный','сильный'],
			'type' => ['','дождь','снег','снег с дождём'],
		];

		$gm = ['Нет','Небольшие','Слабая геомагнитная буря','Малая геомагнитная буря','Умеренная геомагнитная буря','Сильная геомагнитная буря','Пиздец какой шторм','Всё, полный пиздец, зашкалило!'];

		$date = (new DateTime($data['date']['local']))->format('l - d M');

		// $this->numDecor($data);

		$w= "<strong>Погода на {$date} ({$data['date']['local']})</strong>
		{$data['description']['full']} " . $this->collectIcons($data['icon']) . "\n
		<b>Температура</b>";

		//* Отделяем посуточную погоду
		if(!empty($data['temperature']['air']['max']))
		{
			$w .= "
			<b><u>Воздух:</u></b>
			MAX {$data['temperature']['air']['max']['C']} ℃
			MIN {$data['temperature']['air']['min']['C']} ℃
			AVG {$data['temperature']['air']['avg']['C']} ℃
			<u>Воздух ощущается:</u>
			MAX {$data['temperature']['comfort']['max']['C']} ℃
			MIN {$data['temperature']['comfort']['min']['C']} ℃\n
			<u>Вода:</u>
			MAX {$data['temperature']['water']['max']['C']} ℃
			MIN {$data['temperature']['water']['min']['C']} ℃

			<u>Ветер</u>
			MAX {$wind[$data['wind']['direction']['max']['scale_8']]} - {$data['wind']['speed']['max']['m_s']} м/с
			MIN {$wind[$data['wind']['direction']['min']['scale_8']]} - {$data['wind']['speed']['min']['m_s']} м/с

			<u>Давление</u>
			MAX {$data['pressure']['mm_hg_atm']['max']} мм рт.ст.
			MIN {$data['pressure']['mm_hg_atm']['min']} мм рт.ст.";
		}
		//* от почасовой
		else
		{
			$w .= "
			Воздух: {$data['temperature']['air']['C']} ℃
			Воздух ощущается: {$data['temperature']['comfort']['C']} ℃

			Вода: {$data['temperature']['water']['C']} ℃

			<b>Ветер</b> - {$wind[$data['wind']['direction']['scale_8']]} - {$data['wind']['speed']['m_s']} м/с

			<b>Давление</b> - {$data['pressure']['mm_hg_atm']} мм рт.ст.";

		}

		$w .= "\n
		<b>Осадки</b> - {$precipitation['intensity'][$data['precipitation']['intensity']]} {$precipitation['type'][$data['precipitation']['type']]}
		({$data['precipitation']['type']} мм)

		<b>Геомагнитные возмущения</b> - {$gm[$data['gm']]}
		<a href='{$this->urlDIR}/gismeteo-newicons/{$data['icon']}.png' title='www.gismeteo.ru'>&#8205;</a>
		www.gismeteo.ru";

		return $w;
		// return $this->numDecor($w);
	}

	/**
	 ** Обработка погоды
	 */
	private function responseGMHandler(&$data= null)
	{
		//* Данные из methodSwitcher() или напрямую из requestGM()
		$data = $data ?? $this->responseData['response'];

		tolog('$data[\'icon\'] = ' . $data['icon']);

		$this->apiRequest([
			'parse_mode' => 'html',
			'disable_web_page_preview' => false,
			'chat_id' => $this->user_id,
			'text' => $this->collectWeather($data)
		]);
	}

	private function numDecor(&$str)
	{
		/* $num = ['0','1','2','3','4','5','6','7','8','9'];
		$dec = ['𝟎','𝟏','𝟐','𝟑','𝟒','𝟓','𝟔','𝟕','𝟖','𝟗'];

		array_walk_recursive($arr, function(&$i) use($num,$dec) {
			$i = str_replace($num, $dec, '<b>'. $i .'</b>');
		}); */

		tolog(__METHOD__.' ',null, [$arr]);

		return preg_replace("~(?<=[^&#])(\d+?)(?! [;])~", "<b>$1</b>", $str);
	}

} //* Gismeteo