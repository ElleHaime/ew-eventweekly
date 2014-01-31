<?php 

namespace Frontend\Models;

use Objects\EventLike as EventLikeObject;

class EventLike extends EventLikeObject
{
    public function getLikedEventsCount($uId)
    {
        if ($uId) {
            return self::find(array('member_id = ' . $uId . " AND status = 1"))->count();

            /*$event = new Event();
            $event->addCondition('Objects\EventLike.member_id = ' . $uId);
            $event->addCondition('Objects\EventLike.status = 1');
            $event->addCondition('Frontend\Models\Event.event_status = 1');*/

            return $event->fetchEvents()->count();
        } else {
            return 0;
        }
    }
} 