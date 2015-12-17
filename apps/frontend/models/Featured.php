<?php

namespace Frontend\Models;

use Objects\Featured as FeaturedObject,
	Core\Utils as _U;

class Featured extends FeaturedObject
{
	const PRIORIY_HIGH = 0;
	const PRIORIY_LOW = 1;
	
	
	public function getFeatured($locationId, $priority = [self::PRIORIY_HIGH, self::PRIORIY_LOW])
	{
		$result = [];
		$featuredEvents = self::find(['object_type="event" and priority in (' . $priority . ') and location_id=' . $locationId]);
	
		if ($featuredEvents -> count() != 0) {
			foreach ($featuredEvents as $event) {
				$result[$event -> object_id] = $event -> priority;
			}
			return $result; 
		} else {
			return false;
		}
	}
} 