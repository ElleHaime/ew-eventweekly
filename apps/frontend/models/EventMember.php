<?php

namespace Frontend\Models;

use Objects\EventMember as EventMemberObject;
use Frontend\Models\Event;

class EventMember extends EventMemberObject
{
    public function getEventMemberEventsCount($uId)
    {
        if ($uId) {
            //return self::find(array('member_id = ' . $uId . ' AND member_status = 1'))->count();
            $event = new Event();
            $event->addCondition('Objects\EventMember.member_id = ' . $uId);
            $event->addCondition('Objects\EventMember.member_status = 1');
            $event->addCondition('Frontend\Models\Event.event_status = 1');

            return $event->fetchEvents()->count();
        } else {
            return 0;
        }
    }
} 