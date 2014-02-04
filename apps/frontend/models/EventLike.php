<?php 

namespace Frontend\Models;

use Objects\EventLike as EventLikeObject;

class EventLike extends EventLikeObject
{
    public function getLikedEventsCount($uId)
    {
        if ($uId) {
            $event = new Event();
            $event->addCondition('Frontend\Models\EventLike.member_id = ' . $uId);
            $event->addCondition('Frontend\Models\EventLike.status = 1');
            $event->addCondition('Frontend\Models\Event.event_status = 1');
            $event->addCondition('Frontend\Models\Event.deleted = 0');

            return $event->fetchEvents()->count();
        } else {
            return 0;
        }
    }
} 