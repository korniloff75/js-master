<?php
require_once __DIR__."/../UniConstruct.trait.php";
require_once __DIR__."/../Helper.class.php";

class PumpMarket extends Helper implements PumpInt
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../Game',
		BASE = self::FOLDER . '/base.json';


	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveData();


	} //* __construct


	private function init()
	{
		// $this->log->add('self::BTNS=',null,[self::BTNS]);

		$this->getCurData();

		//* Pumps
		$this->data['pumps'] = $this->data['pumps'] ?? [];
		$this->data['change'] = 0;

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		$o = parent::routerCmd($cmd);
		// $pumps = &$this->data['pumps'];

		if(!$o) switch ($cmd ?? $this->cmd[0]) {

			//*** Биржа насосов ***
			case 'pump/market':
				$o = [
					'text' => [
						self::INFO['pump/market'],
						$this->showPumps(),
					],
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => self::PUMP_BTNS['pump/sale blue']],
								['text' => self::PUMP_BTNS['pump/sale all']],
								['text' => self::PUMP_BTNS['pump/sale gold']],
							],
							[
								['text' => self::PUMP_BTNS['pump/market']],
								['text' => self::BTNS['general']],
							],
				],],];
				break;

			case 'sale blue':
			case 'sale gold':
			case 'sale all':
				$sale = explode(' ',$this->cmd[0],2);
				$this->log->add('$sale=',null,[$sale]);
				$o = [
					'text' => [
						self::INFO['sale'][$sale[1]],
					],
				];

				if($sale[1]!=='all') $o['text'][]= self::INFO['unsale'];
				break;

			case 'replacePumps':
			case 'parsePumps':
				$this->{$this->cmd[0]}($this->cmd[1]);
				break;

			case 'sale':
				$this->addPump($this->cmd[1]);
				break;

			case 'unsale':
				$this->removePump($this->cmd[1]);
				return $this->routerCmd('pump/market');
				break;

			default:
				$o = $this->showMainMenu();
				break;
		} //*switch


		//* Подготовка и отправка
		if($o)
		{
			//* add keyboard options
			if(!empty($o['reply_markup']['keyboard']))
			{
				$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
			}

			//* Склеиваем текст перед отправкой
			if(is_array($o['text']))
			{
				$o['text'] = implode("\n\n", $o['text']);
			}

			//* Send
			$this->apiRequest($o);
		}

		return $this;
	} //* routerCmd 💡


	/**
	 * Удаление насосов юзера
	 */
	private function removePumpsFromUser($user, $type, &$arr=null)
	{
		if(!$arr) $arr = &$this->data['pumps'][$type];

		foreach($arr as $key=>&$val)
		{
			if($key!==$user && is_array($val))
			{
				$this->removePumpsFromUser($user,$type,$val);
			}
			elseif($key===$user)
			{
				// $this->log->add(__METHOD__.'$arr',null,[$arr, $arr[$key]]);
				unset($arr[$key]);
				$this->data['change']++;
			}
		}
	}

	/**
	 * Пакетная замена насосов
	 */
	private function replacePumps($cmd)
	{
		$type= strpos($cmd[0],'нефтяные насосы') !== false ? 'blue' : 'gold';
		$user= "@{$this->cbn['from']['username']}";
		$this->removePumpsFromUser($user,$type);
		$this->parsePumps($cmd);
	}

	/**
	 * Пакетное добавление насосов
	 */
	private function parsePumps($cmd)
	{
		$type= strpos($cmd[0],'нефтяные насосы') !== false ? 'blue' : 'gold';

		preg_match_all("~Серийный номер .+: (\\d+)$|будет работать до ([\\d\\.]+)$~im", $cmd[0], $parse);

		list(,$numbers,$dates) = $parse;

		$numbers= array_values(array_filter($numbers));
		$dates= array_values(array_filter($dates));
		$dates= array_map(function(&$i){
			return DateTime::createFromFormat('j.m.Y', $i)->format('Y-m-j');
		}, $dates);

		// $pumps= array_combine(,array_filter($dates));
		$this->log->add(__METHOD__.'',null,[$numbers,$dates]);

		foreach($numbers as $k=>$n)
		{
			$this->addPump([$type,$dates[$k],$n]);
		}
	}

	/**
	 * Удаление по номерам
	 */
	private function removePump($cmd, &$arr=null)
	{
		if(!$arr) $arr = &$this->data['pumps'];

		foreach($arr as $key=>&$val)
		{
			if(is_array($val))
			{
				$this->removePump($cmd,$val);
			}
			elseif(in_array($val, $cmd))
			{
				$this->log->add(__METHOD__.'$arr',null,[$arr, $arr[$key]]);
				unset($arr[$key]);
				$this->data['change']++;
			}
		}
	}

	private function addPump($cmd)
	{
		// list($type,$date,$numbers)= $cmd;
		list($type,$date,$numbers)= [array_shift($cmd), array_shift($cmd), $cmd];

		if(empty($date) || empty($numbers))
		{
			$o = [
				'text' => self::INFO['sale']['fail']
			];
		}
		else
		{
			$this->data['pumps'] = array_merge_recursive($this->data['pumps'], [$type=> [
				$date=> ["@{$this->cbn['from']['username']}"=> $numbers]
				// $date=> "$numbers - @{$this->cbn['from']['username']}"
			]]);

			$uPumps = &$this->data['pumps'][$type][$date]["@{$this->cbn['from']['username']}"];
			$uPumps = array_unique($uPumps);

			$this->data['change']++;
			return $this->routerCmd('pump/market');
		}
	}

	private function showPumps()
	{
		$pList = '';

		foreach($this->data['pumps'] as $type=>&$p)
		{
			ksort($p, SORT_NATURAL);
			$pList.= "\n<b>". self::INFO['pumpName'][$type]."</b>\n";

			foreach($p as $date=>&$val)
			{
				if(!count($val))
				{
					unset($p[$date]);
					$this->data['change']++;
				}
				else $pList.= "{$date}\n";

				foreach($val as $name=>&$num)
				{
					if(!count($num))
					{
						unset($val[$name]);
						$this->data['change']++;
					}
					else $pList.= "{$name} - " . implode(', ',$num) . "\n";
				}
			}
		}
		if(strlen($pList))
			$pList = "<b><u>На продажу будут выставлены:</u></b>\n$pList";
		return $pList;
	}

} //* PumpMarket
