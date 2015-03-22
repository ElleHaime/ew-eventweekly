<?php
/**
 * Class TMember
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core\Traits;

use Core\Utils as _U;


trait TCMember {

    public function fetchMemberLikes()
    {
        $query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.event_id,
        											  Frontend\Models\EventLike.status
                                                FROM Frontend\Models\EventLike
                                                WHERE Frontend\Models\EventLike.status in(0,1)
                                                    AND Frontend\Models\EventLike.member_id = " . $this -> session -> get('memberId'), 
                                            $this -> getDI());
        $event = $query -> execute();
        $likedEventsIds = [];
        $unlikedEventsIds = [];

        if($event) {
            foreach ($event as $key) {
            	if ($key -> status == 1) {
                	$likedEventsIds[] = $key -> event_id;
            	} else {
            		$unlikedEventsIds[] = $key -> event_id;
            	}
            }
        }

        $this -> view -> setVar('likedEventsIds', $likedEventsIds);
        $this -> view -> setVar('unlikedEventsIds', $unlikedEventsIds);
    }


    public function fetchMemberLikeForEvent($eventId)
    {
    	if ($this -> session -> has('memberId')) {
	        $query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.status
	                                                FROM Frontend\Models\EventLike
	                                                WHERE Frontend\Models\EventLike.event_id = '" . $eventId . "'
	                                                    AND Frontend\Models\EventLike.member_id = " . $this -> session -> get('memberId'), 
	                                            $this -> getDI());
	        $event = $query -> execute() -> toArray();
	        $likedEventStatus = 0;
	
	        if(!empty($event)) {
				$likedEventStatus = $event[0]['status'];
	        }
	        $this -> view -> setvar('likedEventStatus', $likedEventStatus);
    	}
    }
} 