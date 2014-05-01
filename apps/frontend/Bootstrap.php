<?php

namespace Frontend;

use \Core\Utils as _U,
	\Frontend\Component\Counter;


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
		$this -> _initCounters($di);
	}

    public function _initCounters($di)
	{
		$di -> set('counters', function() use ($di) {
			return new Counter();
		});
	}

}