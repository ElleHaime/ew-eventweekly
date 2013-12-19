<?php

namespace Backend;

class Bootstrap extends \Core\Bootstrap
{
	protected $_moduleName = 'backend';

	public function registerAutoloaders()
	{
		parent::registerAutoloaders();
	}

	public function registerServices($di)
	{
		parent::registerServices($di);
	}
}