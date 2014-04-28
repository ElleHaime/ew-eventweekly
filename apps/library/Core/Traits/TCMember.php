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