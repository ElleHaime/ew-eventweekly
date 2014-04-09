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
