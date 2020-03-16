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
			/* if(!$this->is_group && !empty($this->message['message_id']))
			{
				$o['message_id'] = $this->message['message_id'];
				$this->apiRequest($o, 'editMessageText');
			}
			else  */
			$this->apiRequest($o);
		}
		$this->log->add(__METHOD__.' $o...=',null,[$o,$cmd]);

		return $this;
	}

} //* Reviews
