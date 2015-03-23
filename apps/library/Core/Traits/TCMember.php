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
    	if ($this -> session -> has('member')) {
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
    }
    
    
    public function getJoinedStatus($event)
    {
    	$result = false;	
    
    	if ($this -> session -> has('member')) {
	    	$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventMember.event_id,
									    				Frontend\Models\EventMember.member_status
									    			FROM Frontend\Models\EventMember
									    			WHERE Frontend\Models\EventMember.member_status in(1,3)
	    												AND Frontend\Models\EventMember.event_id = '" . $event -> id . "'
									    				AND Frontend\Models\EventMember.member_id = " . $this -> session -> get('memberId') . "
	    											LIMIT 1",
									    			$this -> getDI());
	    	$event = $query -> execute();
	    	
			if($event -> count() > 0) {
				$this -> view -> setVar('eventJoined', $event -> getFirst() -> member_status);
				$result = $event -> getFirst() -> member_status;
			} 
		}
		
		return $result;
    }
    
    
    public function getLikedStatus($event)
    {
    	$result = false;	
    
    	if ($this -> session -> has('member')) {
	    	$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.event_id,
									    				Frontend\Models\EventLike.status
									    			FROM Frontend\Models\EventLike
									    			WHERE Frontend\Models\EventLike.status in(0,1)
	    												AND Frontend\Models\EventLike.event_id = '" . $event -> id . "'
									    				AND Frontend\Models\EventLike.member_id = " . $this -> session -> get('memberId') . "
	    											LIMIT 1",
									    			$this -> getDI());
	    	$event = $query -> execute();
	    	
			if($event -> count() > 0) {
				$this -> view -> setVar('likedEventStatus', $event -> getFirst() -> status);
				$result = $event -> getFirst() -> status;
			} 
		}
		
		return $result;
   }
} 