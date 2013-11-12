<?php

namespace Ew\Backend;

class Bootstrap extends \Ew\Core\Bootstrap
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