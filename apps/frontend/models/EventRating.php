<?php

namespace Frontend\Models;

use Objects\EventRating as EventRatingObject,
	Core\Utils as _U;

class EventRating extends EventRatingObject
{
	public function addEventRating($event)
	{
		$rating = EventRatingObject::findFirst(['event_id = "' . $event -> id . '"']);

		if ($rating) {
			$rating -> rank = (int)$rating -> rank + 1;
			$rating -> update();
		} else {
			$rating = new EventRatingObject();
			$metaData = $rating->getModelsMetaData();

			$rating -> assign(['event_id' => $event -> id,
								'location_id' => $event -> location_id,
								'rank' => 1]);
			$rating -> save();
		}
		
		return;
	}
	
	
	public function getTrendingIds($locationId)
	{
		$result = [];
		$trendingEvents = EventRating::find(['location_id=' . $locationId, 'order' => 'rank DESC']);
		
		if ($trendingEvents -> count() != 0) {
			foreach ($trendingEvents as $event) {
				$result[] = $event -> event_id;
			}
			return $result;
		} else {
			return false;
		}
	}
}