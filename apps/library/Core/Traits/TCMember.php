<?php
/**
 * Class TMember
 *
 * @author Slava Basko <basko.slava@gmail.com>
 */

namespace Core\Traits;


trait TCMember {

    public function fetchMemberLikes()
    {
        $likedEvents = \Frontend\Models\EventLike::find('member_id = ' . $this->session->get('memberId') . ' AND status = 1');
        $likedEventsIds = array();
        foreach ($likedEvents as $likedEvent) {
            $likedEventsIds[] = $likedEvent->event_id;
        }
        $this->view->setvar('likedEventsIds', $likedEventsIds);
    }

} 