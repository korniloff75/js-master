<?php

/**
 *
 */
trait UniConstruct
{
	protected
		$cmd, $urlROOT, $urlDIR;

	private function import(UniKffBot &$UKB)
	{
		foreach (get_object_vars($UKB) as $key=> &$value)
		{
			$this->$key = $value;
		}
		// tolog(__METHOD__.'',null,[get_object_vars($UKB)]);
	}


	private function setConstruct(UniKffBot &$UKB, ?array $cmdArr=null)
	{
		$this->UKB = $UKB;
		$this->import($UKB);

		//* Define folders
		$this->urlROOT = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		$this->urlDIR = $this->urlROOT . '/' . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__);

		tolog(__METHOD__,null,['$cmdArr'=>$cmdArr]);

		$this->cmd = [array_shift($cmdArr), $cmdArr];

		$this->is_owner= $UKB->is_owner;

		tolog(__METHOD__,null,['is_owner'=>[$this->is_owner, $this->get('is_owner'), $this->user_id], '$this->cmd'=>$this->cmd]);

		//* Define tokens
		if(empty($this->tokens[$this->apiName ?? strtolower(__CLASS__)]))
		{
			tolog(__METHOD__ , E_USER_WARNING, ['$this->tokens'=>$this->tokens]);
		}
		return $this;
	}

} //* UniConstruct
