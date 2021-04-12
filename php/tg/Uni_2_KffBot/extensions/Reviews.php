<?php

class Reviews extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../BDU',
		BASE = self::FOLDER . '/base.json';


	/**
	 * @param cmd - 'cmdName__opt1__opt2__...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)
			// https://js-master.ru/php/tg/Uni_2_KffBot/BDU/base.json
			->getCurData()
			->inputDataRouter()
			->routerCmd();

	} //* __construct


	private function init()
	{
		// $this->log->add(__METHOD__.' $this->data=',null,$this->data);

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		$o = parent::routerCmd($cmd) ?? [];

		if(!$cmd)
			$cmd = &$this->cmd[0];
		$opts = &$this->cmd[1];

		if(method_exists(__CLASS__, $cmd))
			$o = array_merge_recursive($o, $this->{$cmd}($opts));

		if(count($o))
		{
			$this->apiRequest($o);
		}
		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

		return $this;
	}


	protected function showReviews(string $person_id)
	{
		$users= array_filter($this->data, function($k){
			return is_numeric($k);
		}, ARRAY_FILTER_USE_KEY);

		foreach($this->data as $id=>&$user)
		{
			if(!is_numeric($id)) continue;


		}

		return $this;
	}


	protected function setReview(string $person_id)
	{
		$this->objData->set([

		]);

		return $this;
	}

} //* Reviews
