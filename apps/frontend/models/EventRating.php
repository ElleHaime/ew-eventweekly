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
}