<?php

namespace Frontend;

use \Core\Utils as _U,
	\Frontend\Component\Counter,
	\Frontend\Component\Filter;


class Bootstrap extends \Core\Bootstrap
{
	protected $_moduleName = 'frontend';
	
	public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = NULL)
	{
		parent::registerAutoloaders($dependencyInjector);
	}
	
	public function registerServices(\Phalcon\DiInterface $dependencyInjector)
	{
		parent::registerServices($dependencyInjector);
		
		$this -> _initCounters($dependencyInjector);
		$this -> _initFilters($dependencyInjector);
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