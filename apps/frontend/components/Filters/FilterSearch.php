<?php

namespace Frontend\Component\Filters;

use \Core\Utils as _U,
	\Core\Utils\DateTime as _UDT,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\MemberFilter,
	\Frontend\Component\FiltersBuilder;

class FilterSearch extends FiltersBuilder
{
	/* @var int */
	protected $searchLocationField		= null;
	
	/* @var string */
	protected $searchStartDate			= null;
	
	/* @var string */
	protected $searchEndDate			= null;
	
	/* @var array */
	protected $compoundTag				= [];
	
	/* @var array */
	protected $compoundCategory		= [];
	
	/* @var array */
	protected $compoundTitle			= null;
	
	/* @var array */
	protected $searchIn				= [];
	
	/* @var array */
	protected $searchNotIn				= [];

	
	
	
	public function setLocation($location = null)
	{
		if (is_null($location)) $location = $this -> session -> get('location'); 
		$this -> searchLocationField = $location -> id;
		
		return $this;				
	}
	
	
	public function setStartDate($arg = false)
	{
		if (!$arg) $arg = _UDT::getDefaultStartDate();
		$this -> searchStartDate = _UDT::nice(_UDT::toUnix($arg), null, '%Y-%m-%d %H:%M:%S');
		
		return $this;
	}
	
	
	public function getStartDate()
	{
		return $this -> searchStartDate;
	}
	
	
	public function setEndDate($arg = false)
	{
		if (!$arg) $arg = _UDT::getDefaultEndDate();
		$this -> searchEndDate = _UDT::nice(_UDT::toUnixPlusDays($arg, 1), null, '%Y-%m-%d %H:%M:%S');
		
		return $this;
	}
	
	
	public function getEndDate()
	{
		return $this -> searchEndDate;
	}
	
	
	public function setTitle($arg)
	{
		$title = (new \Phalcon\Filter()) -> sanitize($arg, 'string');
		$this -> compoundTitle = preg_replace('/([\(\)\[\]\{\}\\:\!]+)/i', ' ', $title);
		
		return $this;
	}

	
	public function setTags($tags = [])
	{
		if (!empty($tags)) {
			$this -> compoundTag = $tags;
		} 
		
		return $this;
	}
	
	
	public function setCategories($categories = [])
	{
		if (!empty($categories)) {
			$this -> compoundCategory = $categories;
		}
		
		return $this;
	}
	
	
	public function setIds($value, $isInSearch = true)
	{
		$isInSearch ? $this -> searchIn = $value 
					: $this -> searchNotIn = $value;
		
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
	
	
	public function applyGlobalPreset()
	{
		if (!empty($this -> compoundTitle)) {
			if (!empty($this -> compoundTag) || !empty($this -> compoundCategory)) {
				$this -> compoundTag = [];
				$this -> compoundCategory = [];
			}			

			if ($tags = Tag::find(['name like "%' . $this -> compoundTitle . '%"'])) {
				foreach ($tags as $searchWord) {
					$this -> compoundTag[] = (int)$searchWord -> id;
				}
			}
	
			if ($categories = Category::find(['name like "%' . $this -> compoundTitle . '%"'])) {
				foreach ($categories as $searchWord) {
					$this -> compoundCategory[] = (int)$searchWord -> id;
				}
			}
		}
		
		return $this;
	}
	
	
	public function applyMemberPreset()
	{
		$memberPreset = $this -> getMemberPreset();

		if (isset($memberPreset['category']) && !empty($memberPreset['category']['value'])) {
			foreach ($memberPreset['category']['value'] as $category) {
				$this -> compoundCategory[] = $category;
			}
		}
		
		if (isset($memberPreset['tag']) && !empty($memberPreset['tag']['value'])) {
			foreach ($memberPreset['tag']['value'] as $tag) {
				$this -> compoundTag[] = $tag;
			}
		}	
		
		return $this;
	}
	
	
	public function getFromSession()
	{
		$filters = $this -> session -> get('filterSearch');
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
		$this -> setCategories();
		$this -> setTags();
		
		$this -> compoundTitle = null;
		$this -> searchIn = [];
		$this -> searchNotIn = [];
		$this -> searchGrid = 'event';
		
		return $this;
	}
}