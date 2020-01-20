<?php
require_once "UniConstruct.trait.php";


class Gismeteo extends UniKffBot {
	use UniConstruct;

	private
		$base = 'base_GM';


	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd= 'current')
	{
		$this->setConstruct($UKB, $cmd);

		$this->init();
	} //* __construct


	private function init()
	{
		//* Локация. Если нет в базе
		if(empty($this->getLocation()->location))
		{
			$this->log->add('$this->location is EMPTY!', E_USER_WARNING, [$this->location]);

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
				$this->requestGM('aggregate')->methodSwitcher();
				break;
			case 'by_day_part':
				$this->requestGM('by_day_part')->methodSwitcher();
				break;

			default:
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

				$this->apiRequest([
					'chat_id' => $this->id,
					'text' => "Показать прогноз?",
					'reply_markup' => [
						"inline_keyboard" => $forecastButs,
						"one_time_keyboard" => true,
						"resize_keyboard" => true,
						"selective" => true
					]
					// 'callback_data' =>
				]);
				break;
		}

	} //* init

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
			// todo удалить кнопку
			// editMessageReplyMarkup
		}
		else
		{
			$this->location = $this->locations[$this->id][0] ?? null;
		}
		return $this;
	}

	private function setLocation()
	{
		$this->locations[$this->id] = [
			"0" => $this->location
		];

		file_put_contents("{$this->base}/locations.json", json_encode($this->locations, JSON_UNESCAPED_UNICODE));
	}

	/**
	 *
	 */
	private function userPermission($text=null)
	{
		$text = $text ?? "Уточните свою геолокацию?\nБез данных о вашем местоположении бот не сможет отобразить для вас погоду.";

		$pf = [
			'chat_id' => $this->id,
			'text' => $text,
			'reply_markup' => [
				"keyboard" => [[[
					"text" => "Отправить локацию",
					"request_location" => true
				]]],
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
		//* Кеширование
		$limHours = 1;
		//* Имя файла с кешем
		$cacheFilename = "{$this->base}/{$this->id}.{$method}_" . implode('_', $this->cmd[1]) . ".json";
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
			&& (time() - filemtime($cacheFilename) < $limHours * 3600)
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

			if($respJSON = json_encode($this->responseData, JSON_UNESCAPED_UNICODE))
				file_put_contents($cacheFilename, $respJSON, LOCK_EX);
		}

		// $this->log->add(__METHOD__ . '$this->responseData = ', null, [$this->responseData]);

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

	private function collectWeather(&$data)
	{
		$picts = [
			'Ясно' => '☀',
			'Малооблачно' => '⛅',
			'Переменная облачность' => '⛅',
			'Пасмурно, ' => '☁',
			'Облачно' => '☁',
			'Переменная облачность, дождь' => '⛅&#127782;☔',
			'Переменная облачность, небольшой дождь' => '⛅&#127782;☔',
			'Дождь' => '☁☔',
			'Гроза' => '⛈☔',
			'Снег' => '☃❄',
		];
		$precipitation = [
			'intensity' => [
				'', 'слабый', '', 'сильный'
			],
			'type' => [
				'нет', 'дождь', 'снег', 'дождь со снегом или градом'
			]
		];

		$wind = ['штиль','Северный','Северо-восточный','Восточный','Юго-восточный','Южный',' 	Юго-западный','Западный','Северо-западный',];

		$gm = ['Нет','Небольшие','Слабая геомагнитная буря','Малая геомагнитная буря','Умеренная геомагнитная буря','Сильная геомагнитная буря','Пиздец какой шторм','Всё, полный пиздец, зашкалило!'];

		$w = $w= "<strong>Погода на {$data['date']['local']}</strong>
		{$data['description']['full']} {$picts[$data['description']['full']]}\n
		<b>Температура</b>";

		//* Отделяем посуточную погоду
		if(!empty($data['temperature']['air']['max']))
		{
			$w .= "
			<u>Воздух:</u>
			MAX {$data['temperature']['air']['max']['C']} ℃
			MIN {$data['temperature']['air']['min']['C']} ℃
			AVG {$data['temperature']['air']['avg']['C']} ℃
			<u>Воздух ощущается:</u>
			MAX {$data['temperature']['comfort']['max']['C']} ℃
			MIN {$data['temperature']['comfort']['min']['C']} ℃
			<u>Вода:</u>
			MAX {$data['temperature']['water']['max']['C']} ℃
			MIN {$data['temperature']['water']['min']['C']} ℃

			<b>Ветер</b>
			MAX {$wind[$data['wind']['direction']['max']['scale_8']]} - {$data['wind']['speed']['max']['m_s']} м/с
			MIN {$wind[$data['wind']['direction']['min']['scale_8']]} - {$data['wind']['speed']['min']['m_s']} м/с

			<b>Давление</b>
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

		return $w . "\n
		<b>Осадки</b> - {$precipitation['intensity'][$data['precipitation']['intensity']]} {$precipitation['type'][$data['precipitation']['type']]}
		({$data['precipitation']['type']} мм)

		<b>Геомагнитные возмущения</b> - {$gm[$data['gm']]}
		<a href='{$this->urlDIR}/gismeteo-newicons/{$data['icon']}.png' title='www.gismeteo.ru'>&#8205;</a>
		www.gismeteo.ru";
	}

	/**
	 ** Обработка погоды
	 */
	private function responseGMHandler(&$data= null)
	{
		//* Данные из methodSwitcher() или напрямую из requestGM()
		$data = $data ?? $this->responseData['response'];

		$this->log->add('$data[\'icon\'] = ' . $data['icon']);

		$this->apiRequest([
			'parse_mode' => 'html',
			'disable_web_page_preview' => false,
			'chat_id' => $this->id,
			'text' => $this->collectWeather($data)
		]);
	}

} //* Gismeteo
