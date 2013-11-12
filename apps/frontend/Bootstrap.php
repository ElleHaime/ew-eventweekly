<?php

namespace Frontend;


class Bootstrap extends \Core\Bootstrap
{
	protected $_moduleName = 'frontend';

	public function registerAutoloaders()
	{
		parent::registerAutoloaders();
	}
	
	public function registerServices($di)
	{
		parent::registerServices($di);
	}
}