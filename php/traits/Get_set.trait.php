<?php

/**
 *
 */
trait Get_set
{
	private $data = [];

	public function get($pname)
	{
		return $this->data[$pname];
	}

	public function __get($pname)
	{
		return $this->data[$pname];
	}

	protected function set($pname, $pval)
	{
		return $this->data[$pname] = $pval;
	}

	/* protected function __set($pname, $pval)
	{
		return $this->data[$pname] = $pval;
	} */
}
