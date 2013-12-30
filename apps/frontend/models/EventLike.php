<?php 

namespace Frontend\Models;

use Objects\EventLike as EventLikeObject;

class EventLike extends EventLikeObject
{
    public function getLikedEventsCount($uId)
    {
        return self::find(array('member_id = ' . $uId . " AND status = 1"))->count();
    }
} 