<?php

/**
 *
 */
trait UniConstruct
{
	private
		$id,
		$UKB,
		$cmd;

	private function setConstruct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->UKB = $UKB;
		$this->log = $UKB->log;
		$this->api = $UKB->api;
		// $this->inputData = $UKB->inputData;
		$this->message = $UKB->message;

		$this->id = $this->message['chat']['id'];

		//* Define folders
		$this->urlROOT = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		$this->urlDIR = $this->urlROOT . '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);
		//* Define cmd
		$cmdArr = array_values(array_filter(explode('__', $cmd)));
		$this->cmd = [array_shift($cmdArr), $cmdArr];

		//* Define tokens
		$this->getTokens(__DIR__.'/token.json');
		if(empty($this->tokens[$this->apiName ?? strtolower(__CLASS__)]))
		{
			$this->log->add(__METHOD__ . '$this->tokens = ', E_USER_ERROR, ($this->tokens));
			die;
		}
	}
}
