<?php

namespace Frontend\Models;

use Objects\EventMemberFriend as EventMemberFriendObject;

class EventMemberFriend extends EventMemberFriendObject
{
    public function getEventMemberFriendEventsCount($uId)
    {
        if ($uId) {
            return self::find(array('member_id = ' . $uId)) -> count();
        } else {
            return 0;
        }
    }
} 