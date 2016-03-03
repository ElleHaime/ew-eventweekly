<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Core\Utils\DateTime as _UDT,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\MemberFilter;

class Filter extends Component
{
	/*
	 * @var int
	 * @default session location
	 */
	private $searchLocationField	= false;
	
	/*
	 * @var string
	 * @default today
	 */
	private $searchStartDate		= false;
	
	/*
	 * @var string
	 * @default +week
	 */
	private $searchEndDate			= false;
	
	/*
	 * @var string
	 * @default null
	 */
	private $searchTitle			= false;
	
	/*
	 * @var string
	 * @default list
	 */
	private $searchTypeResult		= 'list';
	
	/*
	 * @var array
	 * @default all tags
	 */
	private $searchTags			= [];
	
	/*
	 * @var bool
	 * @defaultNonLogged 0
	 * @defaultLogged 1  
	 */
	private $personalPresetActive	= 0;
	
	
	/*
	 * @var array
	 */
	protected $filters				= [];

	/*
	 * @var array
	 */
	protected $postSearchVariables = ['searchLocationField',
										'searchLocationLatMin',
										'searchLocationLatMax',
										'searchLocationLngMin',
										'searchLocationLngMax',
										'searchLocationFormattedAddress',
										'personalPresetActive',
										'searchStartDate',
										'searchEndDate',
										'searchTitle',
										'searchTypeResult', 	// map, list
										'searchTags',
										'searchCategories'];
	
	
	
	public function __construct()
	{
		if (!$this -> session -> has('userSearch')) {
			$this -> reset();
			$this -> session -> set('userSearch', $this -> filters);
		}
	}
	
	
	/*
	 * Load current filters from session or compose default, if session wasn't set 
	 */
	public function load()
	{
		return $this -> session -> get('userSearch');
	} 
	
	
	/*
	 * Set or update filter
	 */  
	public function __set($name, $value)
	{
		if (property_exists($this, $name)) {
			switch ($name) {
				case 'searchStartDate':
						$value = date('Y-m-d H:i:s', strtotime($value));
					break;
				case 'searchEndDate':
						$value = date('Y-m-d H:i:s', strtotime($value)  . '+ 1 day');
					break;
			}
			$this -> $name = $value;
			
			$filters = $this -> session -> get('userSearch');
			$filters[$name] = $this -> $name;
			$this -> session -> set('userSearch', $filters);
		}
	}
	
	
	/*
	 * Load default filters
	 */
	public function reset()
	{
		$this -> filters = [];
		
		// set default location from user session
		$this -> filters['searchLocationField'] = $this -> session -> get('location') -> id;
		
		// set default startDate
		$this -> filters['searchStartDate'] = _UDT::getDefaultStartDate();
		
		// set default endDate
		$this -> filters['searchEndDate'] = _UDT::getDefaultStartDate();
		
		// set default tags
		if ($this -> session -> has('memberId')) {
			$this -> filters['userFilters'] = $this -> applyMemberPreset();
		} else {
			$this -> filters['userFilters'] = $this -> applyGlobalPreset();
		}
	}
	
	
	/*
	 * Apply member preset to all filters, mark tags and categories as selected
	 */
	private function applyMemberPreset()
	{
		$tagsList = $this -> getTagsList();
		
		$memberPreset = (new MemberFilter()) -> getbyId($this -> session -> get('memberId'));
		if (!empty($memberPreset)) {
			if (isset($memberPreset['category']) && !empty($memberPreset['category']['value'])) {
				$memberCategories = $memberPreset['category']['value'];
			} else {
				$memberCategories = [];
			}
	
			foreach ($tagsList as $filter => $val) {
				if (in_array($val['id'], $memberCategories)) {
					$tagsList[$filter]['inPreset'] = 1;
					$tagsList[$filter]['fullCategorySelect'] = 1;
				}

				foreach ($val['tags'] as $index => $tag) {
					if (in_array($index, $memberPreset['tag']['value'])) {
						$tagsList[$filter]['tags'][$index]['inPreset'] = 1;
					}
				}
			}
		} 
		
		return $tagsList;
	}
	
	/*
	 * Apply global preset to all filters, mark tags and categories as selected
	 */
	private function applyGlobalPreset()
	{
		$tagsList = $this -> getTagsList();
		
		foreach ($tagsList as $filter => $val) {
			$tagsList[$filter]['inPreset'] = 1;
			$tagsList[$filter]['fullCategorySelect'] = 1;
		
			foreach ($val['tags'] as $index => $tag) {
				$tagsList[$filter]['tags'][$index]['inPreset'] = 1;
			}
		}
		
		return $tagsList;		
	}
	

	/*
	 * Load all tags in filters
	 */
	private function getTagsList()
	{
		$tagsFilters = [];
	
		$categories = Category::find() -> toArray();
		$tags = Tag::find() -> toArray();
	
		foreach ($categories as $obj) {
			$tagsFilters[$obj['id']] = $obj;
			$tagsFilters[$obj['id']]['tags'] = [];
		}
	
		foreach ($tags as $obj) {
			$tagsFilters[$obj['category_id']]['tags'][$obj['id']] = $obj;
		}
	
		return $tagsFilters;
	}
	
	
	
	
	
	
	
	
	
// 	public function loadUserFilters($applyPersonalization = true)
// 	{
// 		$categories = Category::find() -> toArray();
// 		$tags = Tag::find() -> toArray();

// 		foreach ($categories as $obj) {
// 			$this -> userFilters[$obj['id']] = $obj;
// 			$this -> userFilters[$obj['id']]['tags'] = [];
// 		}

// 		foreach ($tags as $obj) {
// 			$this -> userFilters[$obj['category_id']]['tags'][$obj['id']] = $obj;
// 		}

// 		if ($applyPersonalization && $this -> session -> has('memberId')) {
// 			$memberPreset = (new MemberFilter()) -> getbyId($this -> session -> get('memberId'));
			
// 			if (!empty($memberPreset)) {
// 				$this -> applyMemberPersonalization($memberPreset);
// 				$this -> view -> setVar('personalPresetActive', 1);
// 			} else {
// 				$this -> applySessionFilters();
// 			}
// 		} else {
// 			$this -> applySessionFilters();
// 			$this -> view -> setVar('personalPresetActive', 0);
// 		}
		
// 		$this -> view -> setVar('userFilters', $this -> userFilters);
// 	}
	
	
// 	public function applySessionFilters()
// 	{
// 		foreach($this -> userFilters as $index => $filter) {
// 			if ($this -> session -> has('userSearchFilters')) {
// 				$userSearchFilters = $this -> session -> get('userSearchFilters'); 
// 				$sessionFilters = array_keys($userSearchFilters['tag']);
				 
// 				foreach ($this -> userFilters as $index => $filter) {
// 					foreach ($filter['tags'] as $item => $tag) {
// 						if (in_array($item, $sessionFilters)) {
// 							$this -> userFilters[$index]['tags'][$item]['inPreset'] = 1;
// 						}
// 					}
// 					if (in_array($index, array_keys($userSearchFilters['category']))) {
// 						$this -> userFilters[$index]['fullCategorySelect'] = 1;
// 					}
// 				}
// 			} else {
// 				foreach ($this -> userFilters as $index => $filter) {
// 					foreach ($filter['tags'] as $item => $tag) {
// 						$this -> userFilters[$index]['tags'][$item]['inPreset'] = 1;
// 					}
// 					$this -> userFilters[$index]['fullCategorySelect'] = 1;
// 				}
// 			}
// 		}
		
// 		return;
// 	}
	
	
// 	public function applyMemberPersonalization($memberPreset)
// 	{
// 		if(isset($memberPreset['tag'])) {
// 			if (isset($memberPreset['category']) && !empty($memberPreset['category']['value'])) {
// 				$memberCategories = $memberPreset['category']['value'];
// 			} else {
// 				$memberCategories = [];
// 			}
			
// 			foreach ($this -> userFilters as $filter => $val) {
// 				if (in_array($val['id'], $memberCategories)) {
// 					$this -> userFilters[$filter]['inPreset'] = 1;
// 					$this -> userFilters[$filter]['fullCategorySelect'] = 1;
// 				}
				
// 				foreach ($val['tags'] as $index => $tag) {
// 					if (in_array($index, $memberPreset['tag']['value'])) {
// 						$this -> userFilters[$filter]['tags'][$index]['inPreset'] = 1;
// 					}
// 				}
// 			}
// 		} 
		
// 		return;
// 	}
	
	
// 	public function setSessionFilters(array $filters)
// 	{
// 		$this -> session -> set('userSearchFilters', $filters);
// 		return $this;
// 	}
	
	
// 	public function getActiveTags()
// 	{
// 		$result = [];
		
// 		foreach ($this -> userFilters as $index => $filter) {
// 			foreach ($filter['tags'] as $i => $v) {
// 				if (isset($v['inPreset'])) {
// 					$result[] = $i;
// 				} 				
// 			}
// 		}
		
// 		return $result;
// 	}
	
	
// 	public function getActiveCategories()
// 	{
// 		$result = [];
		
// 		foreach ($this -> userFilters as $index => $filter) {
// 			if (isset($filter['fullCategorySelect'])) {
// 				$result[] = $filter['id'];
// 			}
// 		}
		
// 		return $result;
// 	}
	
	
// 	public function getUserFilters()
// 	{
// 		return $this -> userFilters;
// 	}
}
