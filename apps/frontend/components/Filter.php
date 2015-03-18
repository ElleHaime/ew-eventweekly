<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\MemberFilter;

class Filter extends Component
{
	public $userFilters		= [];
	public $presetUsed 		= false;
	
	
	public function loadUserFilters($applyPersonalization = true)
	{
		$categories = Category::find() -> toArray();
		$tags = Tag::find() -> toArray();

		foreach ($categories as $obj) {
			$this -> userFilters[$obj['id']] = $obj;
			$this -> userFilters[$obj['id']]['tags'] = [];
		}

		foreach ($tags as $obj) {
			$this -> userFilters[$obj['category_id']]['tags'][$obj['id']] = $obj;
		}

		if ($applyPersonalization) {
			if ($this -> session -> has('memberId')) {
				$memberPreset = (new MemberFilter()) -> getbyId($this -> session -> get('memberId'));
				if (!empty($memberPreset)) {
					$this -> applyMemberPersonalization($memberPreset);
					$this -> view -> setVar('personalPresetActive', 1);
				} else {
					$this -> applySessionFilters();
				}
			}
		} else {
			$this -> applySessionFilters();
			$this -> view -> setVar('personalPresetActive', 0);
		}
		
		$this -> view -> setVar('userFilters', $this -> userFilters);
	}
	
	
	public function applySessionFilters()
	{
		foreach($this -> userFilters as $index => $filter) {
			if ($this -> session -> has('userSearchFilters')) {
				$userSearchFilters = $this -> session -> get('userSearchFilters'); 
				$sessionFilters = array_keys($userSearchFilters['tag']);
				 
				foreach ($this -> userFilters as $index => $filter) {
					foreach ($filter['tags'] as $item => $tag) {
						if (in_array($item, $sessionFilters)) {
							$this -> userFilters[$index]['tags'][$item]['inPreset'] = 1;
						}
					}
					if (in_array($index, array_keys($userSearchFilters['category']))) {
						$this -> userFilters[$index]['fullCategorySelect'] = 1;
					}
				}
			} else {
				foreach ($this -> userFilters as $index => $filter) {
					foreach ($filter['tags'] as $item => $tag) {
						$this -> userFilters[$index]['tags'][$item]['inPreset'] = 1;
					}
					$this -> userFilters[$index]['fullCategorySelect'] = 1;
				}
			}
		}
		
		return;
	}
	
	
	public function applyMemberPersonalization($memberPreset)
	{
		if(isset($memberPreset['tag'])) {
			foreach ($this -> userFilters as $filter => $val) {
				if (isset($memberPreset['category'][$val['id']])) {
					$this -> userFilters[$filter]['inPreset'] = 1;
					$this -> userFilters[$filter]['fullCategorySelect'] = 1;
				}
				
				foreach ($val['tags'] as $index => $tag) {
					if (in_array($index, $memberPreset['tag']['value'])) {
						$this -> userFilters[$filter]['tags'][$index]['inPreset'] = 1;
					}
				}
			}
		} 
		
		return;
	}

	
	public function getActiveTags()
	{
		$result = [];
		
		foreach ($this -> userFilters as $index => $filter) {
			foreach ($filter['tags'] as $i => $v) {
				if (isset($v['inPreset'])) {
					$result[] = $i;
				} 				
			}
		}
		
		return $result;
	}
	
	
	public function getActiveCategories()
	{
		$result = [];
		
		foreach ($this -> userFilters as $index => $filter) {
			if (isset($filter['fullCategorySelect'])) {
				$result[] = $filter -> id;
			}
		}
		
		return $result;
	}
	
	
	public function getUserFilters()
	{
		return $this -> userFilters;
	}
}