<?php

use Phalcon\Mvc\Micro;

class Bootstrap extends Micro
{
	public function __construct($di)
	{
		$this->setDI($di);
	}
}