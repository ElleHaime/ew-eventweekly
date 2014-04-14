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
        /*$likedEvents = \Frontend\Models\EventLike::find('member_id = ' . $this->session->get('memberId') . ' AND status = 1');
        $likedEventsIds = array();
        foreach ($likedEvents as $likedEvent) {
            $likedEventsIds[] = $likedEvent->event_id;
        }
        $this->view->setvar('likedEventsIds', $likedEventsIds); */

    	$events = $this -> cacheData -> queryKeys();
    	$likedEventsIds = [];
    	
    	foreach ($events as $key) {
    		if (strpos($key, 'member.like.' . $this -> session -> get('memberId'))) {
    			$id = substr($key, strrpos($key, '.') + 1);
    			$likedEventsIds[] = $id;
    		}
    	}
    	
    	$this -> view -> setvar('likedEventsIds', $likedEventsIds);
    }

} 