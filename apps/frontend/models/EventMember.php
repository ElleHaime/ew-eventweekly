<?php

namespace Frontend\Models;

use Objects\EventMember as EventMemberObject;

class EventMember extends EventMemberObject
{
    public function getEventMemberEventsCount($uId)
    {
        if ($uId) {
            return self::find(array('member_id = ' . $uId . ' AND member_status = 1'))->count();
        } else {
            return 0;
        }
    }
} 