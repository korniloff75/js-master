<?php

class OnlyDirFilter extends DirFilter
{
	public function accept()
	{
		/* var_dump(
			$this->getPathname(),
			$this->current()->getPathname()
		); */
		return $this->current()->isDir() && !$this->iterator->isDot()
		&& preg_match($this->regEx, Path::fixSlashes($this->getPathname()));
		//  && parent::accept();
	}

} // OnlyDirFilter