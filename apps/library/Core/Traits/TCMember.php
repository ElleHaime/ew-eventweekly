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
        $query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.event_id
                                                FROM Frontend\Models\EventLike
                                                WHERE Frontend\Models\EventLike.status = 1
                                                    AND Frontend\Models\EventLike.member_id = " . $this -> session -> get('memberId'), 
                                            $this -> getDI());
        $event = $query -> execute();
        $likedEventsIds = [];

        if($event) {
            foreach ($event as $key) {
                $likedEventsIds[] = $key -> event_id;
            }
        }
        $this -> view -> setvar('likedEventsIds', $likedEventsIds);
    }


    public function fetchMemberLikeForEvent($eventId)
    {
        $query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.status
                                                FROM Frontend\Models\EventLike
                                                WHERE Frontend\Models\EventLike.event_id = " . $eventId . "
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