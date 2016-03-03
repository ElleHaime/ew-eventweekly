<?php

namespace Frontend\Models;

use Objects\Tag as TagObject,
	Frontend\Models\Category;

class Tag extends TagObject
{
	public function getTagsByName($tag)
	{
		$result = [];
		
		$tags = Tag::find(['name like "%' . $searchTitleSanitized . '%"']);
		if ($tags) {
			foreach ($tags as $searchTag) {
				$result[] = (int)$searchTag -> id;
			}
		}
		
		return $result;
	}
	
	
	public static function getFullTagsList()
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
	
	
	public static function getTagsByCategory($cId)
	{
		$result = false;
		
		if ($tags = Tag::find(['category_id = ' . (int)$cId])) {
			$result = $tags;
		}	
	
		return $result;
	}
} 