<?php
/**
 * Class MemberListener
 *
 * @author   Slava Basko <basko.slava@gmail.com>
 */

namespace Frontend\Events;

use Frontend\Models\Event,
    Frontend\Models\EventLike,
    Frontend\Models\EventMember,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\Location,
    Thirdparty\Facebook\Extractor;

class MemberListener {

    private $subject = null;

    /**
     * Delete latitude and longitude from cookie after user login
     *
     * @param $subject
     */
    public function deleteCookiesAfterLogin($subject)
    {
        $this->subject = $subject->getSource();
        $this->subject->cookies->get('lastLat')->delete();
        $this->subject->cookies->get('lastLng')->delete();
        $this->subject->cookies->get('lastCity')->delete();
    }

    /**
     * Writer user params in session
     *
     * @param $subject
     */
    public function registerMemberSession($subject) {
        $this->subject = $subject->getSource();
        $params = $subject->getData();

        // remove search global preset from session
        $this->subject->session->remove('userSearch');

        $location = $this->subject->session->get('location');
        if ($params != false && $location->id != $params->location_id) {
            $location = Location::findFirst('id = ' . $params->location_id);
            $this->subject->session->set('location', $location);
        }

        if ($params) {
            $this->subject->session->set('member', $params);
            $this->subject->session->set('role', $params->role);
            $this->subject->session->set('memberId', $params->id);
        }
    }


    /**
     * Writer custom, liked, following event counts in session
     *
     * @param $subject
     */
    public function setEventsCounters($subject) {
        $this->subject = $subject->getSource();
        $params = $subject->getData();

        if ($params) {
            $userId = $params->id;

            $model = new Event();
            $this->subject->session->set('userEventsCreated', $model->getCreatedEventsCount($userId));

            $model = new EventLike();
            $elSummary = $model->getLikedEventsCount($userId);
            // set counter 
            $this->subject->session->set('userEventsLiked', $elSummary -> count());
            // set cache
            foreach ($elSummary as $item) {
                if (!$this -> subject -> cacheData -> exists('member.like.' . $userId . '.' . $item -> id)) {
                    $this -> subject -> cacheData -> save('member.like.' . $userId . '.' . $item -> id, $item -> fb_uid);
                }
            }

            $model = new EventMember();
            $emSummary = $model->getEventMemberEventsCount($userId);
            // set counter
            $this->subject->session->set('userEventsGoing', $emSummary->count());
            // set cache
            foreach ($emSummary as $item) {
                if (!$this -> subject -> cacheData -> exists('member.go.' . $userId . '.' . $item -> id)) {
                    $this -> subject -> cacheData -> save('member.go.' . $userId . '.' . $item -> id, $item -> fb_uid);
                }
            }

            $model = new EventMemberFriend();
            $emfSummary = $model -> getEventMemberFriendEventsCount($userId);
            // set counter
            $this->subject->session->set('userFriendsEventsGoing', $emfSummary -> count());
            // set cache
            foreach ($emfSummary as $item) {
                if (!$this -> subject -> cacheData -> exists('member.friends.go.' . $userId . '.' . $item -> event_id)) {
                    $this -> subject -> cacheData -> save('member.friends.go.' . $userId . '.' . $item -> event_id, $item -> event_id);
                }
            }
        }
    }


    public function checkLocationMatch($subject)
    {
        $this->subject = $subject->getSource();
        $data = $subject->getData();

        $params = $data['member'];
        $fbUId = $data['uid'];
        $token = $data['token'];

        $fbE = new Extractor();
        $res = $fbE->getFQL(array('me' => 'SELECT current_location FROM user WHERE uid = '.$fbUId), $token);

        if (isset($res['MESSAGE'][0]['fql_result_set'][0])) {
            $fblocation = $res['MESSAGE'][0]['fql_result_set'][0]['current_location'];
            $memberLocation = $params->location;

            if ((strtolower($fblocation['country']) != strtolower($memberLocation->country)) || (strtolower($fblocation['city']) != strtolower($memberLocation->city))) {
                $this->subject->session->set('location_conflict', true);
                $this->subject->session->set('location_conflict_profile_flag', true);
            }
        }
    }

} 