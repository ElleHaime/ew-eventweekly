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
            $this->subject->session->set('userEventsLiked', $model->getLikedEventsCount($userId));

            $model = new EventMember();
            $this->subject->session->set('userEventsGoing', $model->getEventMemberEventsCount($userId));
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