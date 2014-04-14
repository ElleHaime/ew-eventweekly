<?php

namespace Frontend\Models;

use Objects\EventMember as EventMemberObject;
use Frontend\Models\Event;

class EventMember extends EventMemberObject
{
    public function getEventMemberEventsCount($uId)
    {
        if ($uId) {
        	$query = new \Phalcon\Mvc\Model\Query(
        			"SELECT Frontend\Models\Event.id, Frontend\Models\Event.fb_uid
        			FROM Frontend\Models\Event
	        			LEFT JOIN Frontend\Models\EventMember ON Frontend\Models\Event.id = Frontend\Models\EventMember.event_id
        			WHERE Frontend\Models\Event.deleted = 0
	        			AND Frontend\Models\Event.event_status = 1
	        			AND Frontend\Models\Event.start_date > '" . date('Y-m-d H:i:s', strtotime('today -1 minute')) . "'
        				AND Frontend\Models\EventMember.member_status = 1
	        			AND Frontend\Models\EventMember.member_id = " . $uId,
        			$this -> getDI());
        	$event = $query -> execute();
        		
        	return $event;
/*        	
        	
        	$event = new Event();
            $event->addCondition('Frontend\Models\EventMember.member_id = ' . $uId);
            $event->addCondition('Frontend\Models\EventMember.member_status = 1');
            $event->addCondition('Frontend\Models\Event.event_status = 1');
            $event->addCondition('Frontend\Models\Event.deleted = 0');
            $event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');

            return $event->fetchEvents(); */
        } else {
            return 0;
        }
    }
} 