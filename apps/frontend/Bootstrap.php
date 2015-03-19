<?php

namespace Frontend;

use \Core\Utils as _U,
	\Frontend\Component\Counter,
	\Frontend\Component\Filter;


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
		$this -> _initFilters($di);
	}

    public function _initFilters($di)
	{
		$di -> set('counters', function() use ($di) {
			return new Counter();
		});
	}

	public function _initCounters($di)
	{
		$di -> set('filters', function() use ($di) {
			return new Filter();
		});
	}
	
}