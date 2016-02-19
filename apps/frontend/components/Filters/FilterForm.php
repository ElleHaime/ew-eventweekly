<?php

namespace Frontend\Component\Filters;

use \Core\Utils as _U,
	\Core\Utils\DateTime as _UDT,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\MemberFilter,
	\Frontend\Component\FiltersBuilder;

class FilterForm extends FiltersBuilder
{
	/* @var string */
	protected $searchLocationCity		= false;
	
	/* @var string */
	protected $searchLocationCountry	= false;
	
	/* @var string */
	protected $searchLocationState		= false;
	
	/* @var string */
	protected $searchStartDate			= false;
	
	/* @var string */
	protected $searchEndDate			= false;
	
	/* @var string */
	protected $searchTitle				= false;
	
	/* @var string */
	protected $searchTypeResult		= 'List';
	
	/* @var array */
	protected $userFilters				= [];
	
	
	
	public function setLocation($location = null)
	{
		if (empty($location)) $location = $this -> session -> get('location');

		$this -> searchLocationCity = $location -> city;
		$this -> searchLocationCountry = $location -> country;
		if (!empty($location -> state)) 
			$this -> searchLocationState = $location -> state;
		
		return $this;
	}
	
	
	public function setStartDate($arg = false)
	{
		if (!$arg) $arg = _UDT::getDefaultStartDate(true); 
		$this -> searchStartDate = _UDT::nice(_UDT::toUnix($arg), null, '%Y-%m-%d');
		
		return $this;
	}
	
	
	public function setEndDate($arg = false)
	{
		if (!$arg) $arg = _UDT::getDefaultEndDate(true);
		$this -> searchEndDate = _UDT::nice(_UDT::toUnix($arg), null, '%Y-%m-%d');
		
		return $this;
	}
	
	
	public function setTitle($arg)
	{
		$this -> searchTitle = $arg;
		
		return $this;
	}
	
	
	public function setTypeResult($arg)
	{
		$this -> searchTypeResult = $arg;
	
		return $this;
	}
	
	
	public function setTags($tags = [])
	{
		$tagsList = Tag::getFullTagsList();
		if (empty($tags)) {
			$tags = $tagsList;
		}
		
		foreach ($tagsList as $index => $filter) {
			foreach ($filter['tags'] as $item => $tag) {
				$this -> userFilters[$index]['tags'][$item]['inPreset'] = 1;
			}
			$this -> userFilters[$index]['fullCategorySelect'] = 1;
		}
		
		return $this;
	}
	

	public function getFilters()
	{
		$filters = [];
		
		$props = $this -> getFilterProperties();
		foreach ($props as $property) {
			if (!empty($this -> $property)) {
				$filters[$property] = $this -> $property;
			} 
		}
		
		return $filters;
	}
	
	
	public function applyMemberPreset()
	{
		$memberPreset = $this -> getMemberPreset();
		
		if (isset($memberPreset['category']) && !empty($memberPreset['category']['value'])) {
			$memberCategories = $memberPreset['category']['value'];
		} else {
			$memberCategories = [];
		}

		foreach ($this -> userFilters as $filter => $val) {
			if (in_array($val['id'], $memberCategories)) {
				$this -> userFilters[$filter]['inPreset'] = 1;
				$this -> userFilters[$filter]['fullCategorySelect'] = 1;
			} else {
				unset($this -> userFilters[$filter]['inPreset']);
				unset($this -> userFilters[$filter]['fullCategorySelect']);
			}

			foreach ($val['tags'] as $index => $tag) {
				if (in_array($index, $memberPreset['tag']['value'])) {
					$this -> userFilters[$filter]['tags'][$index]['inPreset'] = 1;
				} else {
					unset($this -> userFilters[$filter]['tags'][$index]['inPreset']);
				}
			}
		}
		
		return $this;
	}
	
	
	public function setFromSession()
	{
		$filters = $this -> session -> get('filterForm');
		$props = $this -> getFilterProperties();
		
		foreach ($props as $property) {
			if (isset($filters[$property]) && !empty($filters[$property])) {
				$this -> $property = $filters[$property];
			}
		}
		
		return $this;
	}
		
	
	public function reset()
	{
		$this -> setLocation();
		$this -> setStartDate();
		$this -> setEndDate();
		$this -> setTags();
		
		return $this;
	}
}