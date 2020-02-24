<?php

class BDU extends Helper
{
	use UniConstruct;

	const
		FOLDER = __DIR__.'/../Game_2',
		BASE = self::FOLDER . '/base.json';

	protected
		$draws;

	private
		$drawsOwner,
		$addSelf,
		$toAllParticipants;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?array $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->init()->routerCmd()->saveCurData();

	} //* __construct


	private function init()
	{
		// $this->log->add('$this->BTNS=',null,[$this->BTNS]);

		$this->getCurData();

		$this->drawsOwner = isset($this->data['current draws']['owner'])
		&& $this->chat_id === $this->data['current draws']['owner']['id'];

		$this->data['change'] = 0;

		// $this->log->add(__METHOD__.' $this->data=',null,$this->data);

		return $this;
	} //* init


	protected function routerCmd($cmd=null)
	{
		$o = parent::routerCmd($cmd);

		$draws = &$this->data['current draws'];
		// $pumps = &$this->data['pumps'];

		if(!$o) switch ($cmd ?? $this->cmd[0])
		{
			//*
			case 'new draw':
				if(!empty($draws))
				{
					$o = $this->showMainMenu([
						'text' => 'Вы не можете создать розыгрыш, пока не разыгран предыдущий. Но вы можете участвовать в существующем!'
					]);
				break;
				}
		}
	}

} //* BDU
