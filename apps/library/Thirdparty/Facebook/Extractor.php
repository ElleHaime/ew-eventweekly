<?php

namespace Thirdparty\Facebook;

use Thirdparty\Facebook\FacebookApiException,
    Core\Utils as _U;

class Extractor
{
	public $di;
    private $facebook;

    public function __construct($dependencyInjector = null)
    {
        if ($dependencyInjector) {
        	$this -> di = $dependencyInjector;
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
    
    public function getEventTicketUrl($fbUid, $eUrl = false)
    {
    	$ticketsUrl = false;
    	
		if ($this -> di) {
			$session = $this -> di -> getShared('session');
			if ($session -> has('user_token') && $session -> has('user_fb_uid')) {
				$res = $this -> getFQL(array('ticket' => 'SELECT ticket_uri FROM event WHERE eid = ' . $fbUid), $session -> get('user_token'));
			
				if ($res['STATUS'] && !is_null($res['MESSAGE'][0]['fql_result_set'][0]['ticket_uri'])) {
					$ticketsUrl = $res['MESSAGE'][0]['fql_result_set'][0]['ticket_uri'];
				} 
			} else {
				if ($eUrl) {
					$ticketsUrl = 'https://www.facebook.com/events/' . $fbUid;
				} 
			}
		}    	
		
		return $ticketsUrl;
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
