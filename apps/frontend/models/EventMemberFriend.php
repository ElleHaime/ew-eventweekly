<?php

namespace Frontend\Models;

use Objects\EventMemberFriend as EventMemberFriendObject;
use Frontend\Models\Event;

class EventMemberFriend extends EventMemberFriendObject
{
    public function getEventMemberFriendEventsCount($uId)
    {
    	if ($uId) {
            $event = self::find(array('member_id = ' . $uId));
            return $event;
        } else {
            return 0;
        }
    }
} 