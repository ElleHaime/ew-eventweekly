<?php

namespace Frontend\Models;

use Objects\EventRating as EventRatingObject,
	Core\Utils as _U;

class EventRating extends EventRatingObject
{
	public function addEventRating($event)
	{
		$rating = self::findFirst(['event_id = "' . $event -> id . '"']);

		if ($rating) {
			$rating -> rank = (int)$rating -> rank + 1;
			$rating -> update();
		} else {
			$rating = new self();
			$rating -> assign(['event_id' => $event -> id,
								'location_id' => $event -> location_id,
								'rank' => 1]);
			$rating -> save();
		}
		
		return;
	}
	
	
	public function getTrendingIds($locationId)
	{
		$trendingEvents = EventRating::find(['object_type="event" and priority = 0 and location_id=' . $locationId]);
		
		if ($trendingEvents -> count() != 0) {
			foreach ($trendingEvents as $event) {
				$searchEventsId[] = $event -> event_id;
			}
			return $searchEventsId;
		} else {
			return false;
		}
	}
}