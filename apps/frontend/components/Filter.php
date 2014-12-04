<?php

namespace Frontend\Component;

use \Core\Utils as _U,
	\Phalcon\Mvc\User\Component,
	\Frontend\Models\Category,
	\Frontend\Models\Tag,
	\Frontend\Models\MemberFilter;

class Filter extends Component
{
	public $userFilters	= [];
	
	
	public function loadUserFilters()
	{
		$categories = Category::find() -> toArray();
		$tags = Tag::find() -> toArray();

		foreach ($categories as $obj) {
			$this -> userFilters[$obj['id']] = $obj;
			$this -> userFilters[$obj['id']]['tags'] = [];
		}

		foreach ($tags as $obj) {
			$this -> userFilters[$obj['category_id']]['tags'][] = $obj;
		}
		
		$this -> applyMemberPersonalization();

		$this -> view -> setVar('userFilters', $this -> userFilters);
	}
	
	
	public function applySessionFilters()
	{
		
	}
	
	
	public function applyMemberPersonalization()
	{
		if ($this -> session -> has('memberId')) {
			
		} 
		
		return;
	}
}