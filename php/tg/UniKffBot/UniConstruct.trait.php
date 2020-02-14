<?php

/**
 *
 */
trait UniConstruct
{
	private
		$cmd;

	protected
		$urlROOT, $urlDIR;

	private function import(UniKffBot &$UKB)
	{
		foreach (get_object_vars($UKB) as $key=> &$value)
		{
			$this->$key = $value;
		}
		$this->log->add(__METHOD__.'',null,[get_object_vars($UKB)]);
	}


	private function setConstruct(UniKffBot &$UKB, ?string $cmd=null)
	{
		// $this->UKB = $UKB;
		$this->import($UKB);
		/* $this->log = $UKB->log;
		$this->api = $UKB->api;
		$this->inputData = $UKB->inputData;
		$this->message = $UKB->message; */

		// $this->id = $this->message['chat']['id'];

		//* Define folders
		$this->urlROOT = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		$this->urlDIR = $this->urlROOT . '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
		//* Define cmd
		$cmdArr = array_values(array_filter(explode('__', $cmd)));
		$this->cmd = [array_shift($cmdArr), $cmdArr];

		$this->log->add(__METHOD__.' owner, $this->cmd=',null,[[$this->is_owner, $this->get('is_owner'), $this->cbn['from']['id']], $this->cmd]);

		//* Define tokens
		// $this->getTokens(__DIR__.'/token.json');
		if(empty($this->tokens[$this->apiName ?? strtolower(__CLASS__)]))
		{
			$this->log->add(__METHOD__ . '$this->tokens = ', E_USER_WARNING, [$this->tokens]);
			// die;
		}
		return $this;
	}
}
