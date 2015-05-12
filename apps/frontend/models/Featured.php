<?php

namespace Frontend\Models;

use Objects\Featured as FeaturedObject;

class Featured extends FeaturedObject
{
	public function getFeaturedIds($locationId)
	{
		$featuredEvents = Featured::find(['object_type="event" and priority in (0,1) and location_id=' . $locationId]);
		
		if ($featuredEvents -> count() != 0) {
			foreach ($featuredEvents as $event) {
				$searchEventsId[] = $event -> object_id;
			}
			return $searchEventsId; 
		} else {
			return false;
		}
	}
} 