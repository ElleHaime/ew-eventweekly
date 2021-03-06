<?php

namespace Frontend;

use \Core\Utils as _U,
	\Frontend\Component\Counter,
	\Frontend\Component\FiltersBuilder,
	\Frontend\Component\Filters\FilterSearch,
	\Frontend\Component\Filters\FilterForm;


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

    public function _initCounters($di)
	{
		$di -> set('counters', function() use ($di) {
			return new Counter();
		});
	}

	public function _initFilters($di)
	{
		$searchGrids = ['event', 'venue'];
		
		$di -> set('filtersBuilder', function() use ($di) {
			return new FiltersBuilder();
		});
		
		$di -> set('filterSearch', function() use ($di) {
			return new FilterSearch();
		});

		foreach ($searchGrids as $index => $grid) {
			$di -> set('filterForm' . ucfirst($grid), new FilterForm($grid));
		}
	}
}