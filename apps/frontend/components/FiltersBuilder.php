<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Core\Utils\DateTime as _UDT,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\Location,
	\Frontend\Models\MemberFilter;

class FiltersBuilder extends Component
{
	private $filters			 	= [];
	private $memberPreset		= false;

	
	public function load()
	{
  		if (!$this -> session -> has('filters')) {
			$this -> resetFilters();
 		} else {
 			$this -> filterForm -> getFromSession();
 			$this -> filterSearch -> getFromSession();
 		} 
	}
	
	
	public function addFilter($filter, $value)
	{
		$this -> filters[] = $filter;
		
		switch ($filter) {
			case 'searchLocationFormattedAddress':
//TODO: add administrative area.
//TODO: problem here 12210 | Al Abageyah | Cairo Governorate | Egypt and in Japan
					$formattedAddress = get_object_vars(json_decode($value));
					$value = (new Location()) -> createOnChange(['city' => $formattedAddress['locality'], 'country' => $formattedAddress['country']]);
					$this -> session -> set('location', $value);
					
					$this -> filterSearch -> setLocation($value);
					$this -> filterForm -> setLocation($value);
				break;
				
			case 'searchLocation':
					$this -> session -> set('location', $value);
				
					$this -> filterSearch -> setLocation($value);
					$this -> filterForm -> setLocation($value);
				break;
			
			case 'searchTitle':
					$this -> filterSearch -> setTitle($value);
					$this -> filterForm -> setTitle($value);
				break;
			
			case 'searchStartDate':
					$this -> filterSearch -> setStartDate($value);
					$this -> filterForm -> setStartDate($value);
				break;
				
			case 'searchEndDate':
					$this -> filterSearch -> setEndDate($value);
					$this -> filterForm -> setEndDate($value);
				break;
	
			case 'searchTypeResult':
					$this -> filterForm -> setTypeResult($value);
				break;
	
			case 'searchCategories':
					$this -> filterSearch -> setCategories(array_keys($value));
					$this -> filterForm -> setCategories(array_keys($value));
				break;
	
			case 'searchTags':
					$this -> filterSearch -> setTags(array_keys($value));
					$this -> filterForm -> setTags(array_keys($value));
				break;
				
			case 'personalPresetActive':
					$this -> setMemberPreset();
				break;
			
			case 'searchNotId':
					$this -> filterSearch -> setIds($value, false);
				break;
				
			case 'searchId':
					$this -> filterSearch -> setIds($value);
				break;
				
						
			default: return false;
		}
		
		return $this;
	}
	
	
	public function removeFilter($filter)
	{
		$this -> filterSearch -> unsetFilterProperty($filter);
		$this -> filterForm -> unsetFilterProperty($filter);
	}
	
	
	public function applyFilters()
	{
		if (!array_search('searchTitle', $this -> filters)) {
			$this -> removeFilter('compoundTitle');
		}
		if (!array_search('personalPresetActive', $this -> filters)) {
			$this -> unsetMemberPreset();
		}
		
		if ($this -> getMemberPreset()) {
			// apply to tags
			$this -> filterSearch -> applyMemberPreset();
			$this -> filterForm -> applyMemberPreset();
		} else {
			$this -> filterSearch -> applyGlobalPreset();
			$this -> filterForm -> applyGlobalPreset();
		}
		
		$this -> session -> set('filterForm', $this -> getFormFilters());
		$this -> session -> set('filterSearch', $this -> getSearchFilters());
		
		return $this;
	}
	
	
	public function getSearchFilters()
	{
		return $this -> filterSearch -> getFilters();
	}
	
	
	public function getFormFilters()
	{
		return $this -> filterForm -> getFilters();
	}
	

	public function resetFilters()
	{
		$this -> setMemberPreset();
		
		$this -> filterSearch -> reset();
		$this -> filterForm -> reset();
		
		$this -> session -> set('filters', true);
		$this -> applyFilters();
		
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
		
		return $this;
	}
	
	
	protected function unsetMemberPreset()
	{
		$this -> memberPreset = false;
	}
	
	
	public function getMemberPreset()
	{
		return $this -> memberPreset;
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
		if (property_exists($this, $propertyName)) {
			if (is_array($this -> $propertyName)) {
				$this -> $propertyName = [];
			} else {
				unset($this -> $propertyName);
			}
		}
	
		return $this;
	}
	
}