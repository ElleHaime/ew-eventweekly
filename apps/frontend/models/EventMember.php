<?php

namespace Frontend\Models;

use Objects\EventMember as EventMemberObject;
use Frontend\Models\Event;

class EventMember extends EventMemberObject
{
    public function getEventMemberEventsCount($uId)
    {
        if ($uId) {
            $event = new Event();
            $event->addCondition('Frontend\Models\EventMember.member_id = ' . $uId);
            $event->addCondition('Frontend\Models\EventMember.member_status = 1');
            $event->addCondition('Frontend\Models\Event.event_status = 1');
            $event->addCondition('Frontend\Models\Event.deleted = 0');
            $event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');

            return $event->fetchEvents();
        } else {
            return 0;
        }
    }
} 