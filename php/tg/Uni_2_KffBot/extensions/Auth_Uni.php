<?php
require_once __DIR__."/../UniConstruct.trait.php";
require_once __DIR__."/../Helper.class.php";

class PumpMarket extends Helper implements PumpInt
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/..',
		BASE = self::FOLDER . '/license_auth.json';


	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveCurData();


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

} //* PumpMarket
