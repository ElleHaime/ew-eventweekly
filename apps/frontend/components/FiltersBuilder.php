<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Core\Utils\DateTime as _UDT,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\Location,
	\Frontend\Models\MemberFilter,
	\Frontend\Component\Filters\FilterSearch,
	\Frontend\Component\Filters\FilterForm;

class FiltersBuilder extends Component
{
	public $filters			 	= [];
	public $memberPreset		= false;
	public $filtersInSession	= false;
	public $gridsList			= ['event', 'venue'];
	public $activeGrid			= 'event';
	private $filterFormHeap 	= [];

	
	public function load()
	{
// $this -> session -> remove('filters');
// _U::dump($this -> session -> has('filters'));
  		if (!$this -> session -> has('filters')) {
  			$this -> filtersInSession = false;
			$this -> resetFilters();
 		} else {
 			$this -> filtersInSession = true;
 			$this -> setActiveGrid($this -> session -> get('searchGrid'));
 			
 			$this -> getSearchFilters();
 			foreach ($this -> gridsList as $index => $grid) {
 				$this -> getFormFilters($grid);
 			}
 		}

 		$this -> session -> set('filtersInSession', $this -> filtersInSession);
	}
	
	
	public function addFilter($filter, $value)
	{
		$this -> filters[] = $filter;
		$filterFormName = $this -> getFilterFormName($this -> activeGrid);
		
		switch ($filter) {
			case 'searchLocationFormattedAddress':
//TODO: add administrative area.
//TODO: problem here 12210 | Al Abageyah | Cairo Governorate | Egypt and in Japan
					$formattedAddress = get_object_vars(json_decode($value));
// 					$formattedAddress = $value;
					$value = (new Location()) -> createOnChange(['city' => $formattedAddress[\Core\Geo::GMAPS_CITY], 
																 'country' => $formattedAddress[\Core\Geo::GMAPS_COUNTRY],
																 'administrative_area_level_1' => $formattedAddress[\Core\Geo::GMAPS_STATE],
																 'place_id' => $formattedAddress[\Core\Geo::GMAPS_PLACE]]);
					$this -> session -> set('location', $value);
					$this -> filterSearch -> setLocation($value);
					$this -> $filterFormName -> setLocation($value);
				break;
				
			case 'searchLocation':
					$this -> session -> set('location', $value);
				
					$this -> filterSearch -> setLocation($value);
					foreach ($this -> gridsList as $index => $grid) {
						$filterFormName = $this -> getFilterFormName($grid);
						$this -> $filterFormName -> setLocation($value);
					}
				break;
			
			case 'searchTitle':
					$this -> filterSearch -> setTitle($value);
					foreach ($this -> gridsList as $index => $grid) {
						$filterFormName = $this -> getFilterFormName($grid);
						$this -> $filterFormName -> setTitle($value);
					}
				break;
				
			case 'searchStartDate':
					$this -> filterSearch -> setStartDate($value);
					$this -> $filterFormName -> setStartDate($value);
				break;
				
			case 'searchEndDate':
					$this -> filterSearch -> setEndDate($value);
					$this -> $filterFormName -> setEndDate($value);
				break;
	
			case 'searchTypeResult':
					$this -> $filterFormName -> setTypeResult($value);
				break;
	
			case 'searchCategories':
					$this -> filterSearch -> setCategories(array_keys($value));
					$this -> $filterFormName -> setCategories(array_keys($value));
				break;
	
			case 'searchTags':
					$this -> filterSearch -> setTags(array_keys($value));
					$this -> $filterFormName -> setTags(array_keys($value));
				break;
				
			case 'personalPresetActive':
					$value == 1 ? $this -> setMemberPreset() : $this -> unsetMemberPreset();
				break;
			
			case 'searchNotId':
					$this -> filterSearch -> setIds($value, false);
				break;
				
			case 'searchId':
					$this -> filterSearch -> setIds($value);
				break;
		}
		
		return $this;
	}
	
	
	public function removeFilter($filter)
	{
		$this -> filterSearch -> unsetFilterProperty($filter);

		$filterFormName = $this -> getFilterFormName($this -> activeGrid);
		$this -> $filterFormName -> unsetFilterProperty($filter);
	}
	
	
	public function applyFilters()
	{
		$this -> session -> set('filters', true);
		
		if (array_search('searchTitle', $this -> filters) === false) {
			$this -> removeFilter('searchTitle');
			$this -> removeFilter('compoundTitle');
		}

		if (array_search('personalPresetActive', $this -> filters) === false) {
			$this -> unsetMemberPreset();
		}

		if ($this -> getMemberPreset()) {
			// apply to tags
			$this -> filterSearch -> applyMemberPreset();
			
			foreach ($this -> gridsList as $index => $grid) {
				$filterFormName = $this -> getFilterFormName($grid);
				$this -> $filterFormName -> applyMemberPreset();
				$this -> session -> set($filterFormName, $this -> getTempFormFilters($grid));
			}
		} else {
			$this -> filterSearch -> applyGlobalPreset();
			
			$filterFormName = $this -> getFilterFormName($this -> activeGrid);
			$this -> $filterFormName -> applyGlobalPreset();
			$this -> session -> set($filterFormName, $this -> getTempFormFilters($this -> activeGrid));
				
			foreach ($this -> gridsList as $index => $grid) {
				if ($grid != $this -> activeGrid) {
					$filterFormName = $this -> getFilterFormName($grid);
					if (!$this -> $filterFormName -> getFromSession($grid)) {
						$this -> $filterFormName -> applyGlobalPreset();
						$this -> session -> set($filterFormName, $this -> $filterFormName -> getTempFormFilters($grid));
					}
				}
			}					
		}
		
		$this -> session -> set('filterSearch', $this -> getTempSearchFilters());

		return $this;
	}
	
	
	public function getSearchFilters()
	{
		return $this -> filterSearch -> getFromSession();
	}
	
	
	public function getFormFilters($grid = false)
	{
		if (!$grid) $grid = $this -> activeGrid; 
		$filterFormName = $this -> getFilterFormName($grid);

		return $this -> $filterFormName -> getFromSession($grid);
	}
	
	
	public function getTempSearchFilters()
	{
		return $this -> filterSearch -> getFilters();
	}
	
	
	public function getTempFormFilters($grid = false)
	{
		if (!$grid) $grid = $this -> activeGrid;
		$filterFormName = $this -> getFilterFormName($grid);
		
		return $this -> $filterFormName -> getFilters();
	}
	
	
	public function getFormFiltersInactive()
	{
		$result = [];
		
		foreach ($this -> gridsList as $index => $grid) {
			if ($grid != $this -> activeGrid) {
				$result[$grid] = $this -> getFormFilters($grid);
			}			
		}
		
		return $result;
	}
	

	public function resetFilters()
	{
		$this -> setActiveGrid();
		$this -> setMemberPreset();
		
		$this -> filterSearch -> reset();
		foreach ($this -> gridsList as $index => $grid) {
			$filterFormName = $this -> getFilterFormName($grid);
			$this -> $filterFormName -> reset();
		}
		
		$this -> applyFilters();
		
		return $this;
	}

	
	public function resetPreset()
	{
		$this -> addFilter('personalPresetActive', 1) -> setMemberPreset() -> applyFilters();
		
		return $this;
	}
	
	protected function setMemberPreset()
	{
		if ($this -> session -> has('memberId')) {
			$preset = (new MemberFilter()) -> getbyId($this -> session -> get('memberId'));
	
			if (!empty($preset)) {
				$this -> memberPreset = $preset;
			}
		}
		$this -> session -> set('memberPreset', $this -> memberPreset);
		
		return $this;
	}
	
	
	protected function unsetMemberPreset()
	{
		$this -> memberPreset = false;
		$this -> session -> set('memberPreset', $this -> memberPreset);
		
		return $this;
	}
	
	
	public function getMemberPreset()
	{
		return $this -> session -> get('memberPreset');
	}
	
	
	protected function getFilterProperties()
	{
		$properties = [];
		
		foreach(get_class_vars(get_called_class()) as $name => $value) {
			if(!in_array($name, array_keys(get_class_vars('\Frontend\Component\FiltersBuilder')))) {
				$properties[]= $name;
			}
		}
		
		return $properties;
	}
	
	
	public function unsetFilterProperty($propertyName)
	{
		if (property_exists($this, $propertyName) && !empty($this -> $propertyName)) 
		{
			if (is_array($this -> $propertyName)) {
				$this -> $propertyName = [];
			} else {
				unset($this -> $propertyName);
			}
		}
	
		return $this;
	}

	
	public function isFiltersInSession()
	{
		return $this -> session -> get('filtersInSession');
	} 
	
	
	public function getActiveGrid()
	{
		return $this -> activeGrid;
	}
	
	
	public function getActiveGridFromSession()
	{
		return $this -> session -> get('searchGrid');
	}
	
	
	public function getGridsList()
	{
		return $this -> gridsList;
	}
	
	
	public function setActiveGrid($grid = 'event')
	{
		$this -> activeGrid = $grid;
		
		$this -> filterSearch -> setFromSession();
		foreach ($this -> gridsList as $index => $grid) {
			$filterFormName = $this -> getFilterFormName($grid);
			$this -> $filterFormName -> setFromSession($grid);
		}
		
		$this -> session -> set('searchGrid', $this -> activeGrid);
	
		return $this;
	}
	
	
	private function getFilterFormName($grid)
	{
		return 'filterForm' . ucfirst($grid); 
	}
	
}