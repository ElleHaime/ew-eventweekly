<?php
/**
 * Class MemberListener
 *
 * @author   Slava Basko <basko.slava@gmail.com>
 */

namespace Frontend\Events;

use Thirdparty\Facebook\Extractor;

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
    }

    /**
     * Writer user params in session
     *
     * @param $subject
     */
    public function registerMemberSession($subject) {
        $this->subject = $subject->getSource();
        $params = $subject->getData();
        $this->subject->session->set('member', $params);
        $this->subject->session->set('role', $params->role);
        $this->subject->session->set('memberId', $params->id);
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