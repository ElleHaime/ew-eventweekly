<?php

namespace Frontend\Models;

use Objects\EventMemberFriend as EventMemberFriendObject;
use Frontend\Models\Event;

class EventMemberFriend extends EventMemberFriendObject
{
    public function getEventMemberFriendEventsCount($uId)
    {

		if ($uId) {
			$query = new \Phalcon\Mvc\Model\Query(
					"SELECT Frontend\Models\Event.id, Frontend\Models\Event.fb_uid 
						FROM Frontend\Models\Event
							LEFT JOIN Frontend\Models\EventMemberFriend ON Frontend\Models\Event.id = Frontend\Models\EventMemberFriend.event_id
						WHERE Frontend\Models\Event.deleted = 0 
							AND Frontend\Models\Event.event_status = 1
							AND Frontend\Models\Event.start_date > '" . date('Y-m-d H:i:s', strtotime('today -1 minute')) . "'
							AND Frontend\Models\EventMemberFriend.member_id = " . $uId, 
					$this -> getDI());
			$event = $query -> execute();
			
			return $event;
        } else {
            return 0;
        }
    }
} 