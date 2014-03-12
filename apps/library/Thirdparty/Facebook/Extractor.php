<?php

namespace Thirdparty\Facebook;

use Thirdparty\Facebook\FacebookApiException,
    Core\Utils as _U;

class Extractor
{

    private $facebook;

    public function __construct($dependencyInjector = null)
    {
        if ($dependencyInjector) {
            $fb_config = $dependencyInjector -> get('facebook_config');
        } else {
            include(FACEBOOK_CONFIG_SOURCE);
            $fb_config = json_decode(json_encode($cfg_facebook), false);
        }            

        $config = array(
            'appId' => $fb_config -> facebook -> appId,
            'secret' => $fb_config -> facebook -> appSecret,
        );
        $this -> facebook = new \Thirdparty\Facebook\Facebook($config);
    }


    public function getQueriesScope()
    {
        $timelimit = strtotime(date('Y-m-d H:i:s', strtotime('today -1 minute')));
        //$timelimit = strtotime(date('Y-m-d H:i:s', strtotime('today -25 days')));

        $queries = array(
            array(
                'order' => 1,
                'name' => 'user_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                    WHERE eid IN (SELECT eid FROM event_member WHERE uid=$userUid)
                    AND creator = $userUid
                     AND start_time > ' . $timelimit . '
                    ORDER BY eid',
                'type' => 'final',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$userUid/')
            ),
            array(
                'order' => 2,
                'name' => 'friend_uid',
                'query' => 'SELECT uid2
              FROM friend 
              WHERE uid1 = $userUid',
                'type' => 'prepare',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$userUid/')
            ),
            array(
                'order' => 3,
                'name' => 'friend_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                  WHERE creator IN ($friendsUid)
                      AND start_time > ' . $timelimit . ' 
                  ORDER BY eid
                  LIMIT $start, $lim',
                'type' => 'final',
                'start' => 0,
                'limit' => 200,
                'patterns' => array('/\$start/',
                    '/\$lim/',
                    '/\$userUid/',
                    '/\$friendsUid/')
            ),
            array(
                'order' => 4,
                'name' => 'friend_going_eid',
                'query' => 'SELECT eid
              FROM event_member 
              WHERE uid IN($friendsUid)
                AND rsvp_status = "attending"',
                'type' => 'prepare',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$friendsUid/')
            ),
            array(
                'order' => 5,
                'name' => 'friend_going_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                    WHERE eid IN ($eventsUid)
                    AND creator != $userUid
                      AND start_time > ' . $timelimit . ' 
                    ORDER BY eid                  
                    LIMIT $start, $lim',
                'type' => 'final',
                'start' => 0,
                'limit' => 200,
                'patterns' => array('/\$start/',
                    '/\$lim/',
                    '/\$userUid/',
                    '/\$eventsUid/')
            ),

            array(
                'order' => 6,
                'name' => 'user_going_eid',
                'query' => 'SELECT eid
              FROM event_member 
              WHERE uid  = $userUid
                AND rsvp_status = "attending"',
                'type' => 'prepare',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$userUid/')
            ),
            array(
                'order' => 7,
                'name' => 'user_going_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                    WHERE eid IN ($userEventsUid)
                    AND creator != $userUid
                      AND start_time > ' . $timelimit . ' 
                    ORDER BY eid                  
                    LIMIT $start, $lim',
                'type' => 'final',
                'start' => 0,
                'limit' => 200,
                'patterns' => array('/\$start/',
                    '/\$lim/',
                    '/\$userUid/',
                    '/\$userEventsUid/')
            ),
            array(
                'order' => 8,
                'name' => 'user_page_uid',
                'query' => 'SELECT page_id
                      FROM page_admin
                      WHERE uid = $userUid',
                'type' => 'prepare',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$userUid/')
            ),
             array(
                'order' => 9,
                'name' => 'user_page_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                    WHERE creator IN ($userPageUid)
                    AND start_time > ' . $timelimit . ' 
                    ORDER BY eid                  
                    LIMIT $start, $lim',
                'type' => 'final',
                'start' => 0,
                'limit' => 200,
                'patterns' => array('/\$start/',
                    '/\$lim/',
                    '/\$userPageUid/')
            ),
            array(
                'order' => 10,
                'name' => 'page_uid',
                'query' => 'SELECT page_id
              FROM page_fan
              WHERE uid = $userUid',
                'type' => 'prepare',
                'start' => false,
                'limit' => false,
                'patterns' => array('/\$userUid/')
            ),
            array(
                'order' => 11,
                'name' => 'page_event',
                'query' => 'SELECT eid, name, description, location, venue, pic_big, pic_cover, creator, start_time, end_time
                    FROM event
                    WHERE creator IN ($pageUid)
                      AND start_time > ' . $timelimit . ' 
                    ORDER BY eid                  
                    LIMIT $start, $lim',
                'type' => 'final',
                'start' => 0,
                'limit' => 200,
                'patterns' => array('/\$start/',
                    '/\$lim/',
                    '/\$userUid/',
                    '/\$pageUid/')
            )
        );

        return $queries;
    }


    public function getFQL($query, $accessToken)
    {
        try {
            $ret_obj = $this->facebook->api(array(
                'method' => 'fql.multiquery',
                'queries' => $query,
                'access_token' => $accessToken,
            ));

            if (!empty($ret_obj)) {
                $ret['STATUS'] = TRUE;
                $ret['MESSAGE'] = $ret_obj;
                return $ret;
            } else {
                $ret['STATUS'] = FALSE;
                $ret['MESSAGE'] = 'Nothing found.';
                return $ret;
            }
        } catch (FacebookApiException $e) {
            if (190 == $e->getCode()) {
                $ret['STATUS'] = FALSE;
                $ret['MESSAGE'] = 'Access token expired';
                return $ret;
            } else {
                $ret['STATUS'] = FALSE;
                $ret['MESSAGE'] = 'Facebook throws an exception. Error code:' . $e->getCode() . ' ' . $e->getMessage();
                return $ret;
            }
        }
    }
}
