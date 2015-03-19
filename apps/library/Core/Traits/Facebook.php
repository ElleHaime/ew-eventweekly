<?php

namespace Core\Traits;

use Core\Utils as _U;

trait Facebook {
	
    public function checkFacebookExpiration()
    {
    	$http = $this->di->get('http');
    	$httpClient = $http::getProvider();
    	
    	$httpClient->setBaseUri('https://graph.facebook.com/');
    	$response = $httpClient->get('me?access_token=' . $this->session->get('user_token'));

    	if($response -> header -> statusCode == 200 && $response -> header -> statusMessage == 'OK') {
    		return true;
    	} else {
    		return false;
		}
    }
    
    
    public function sendToFacebook($url, $fbParams)
    {
        $http = $this -> di -> get('http');
        $httpClient = $http::getProvider();

        $httpClient->setBaseUri('https://graph.facebook.com/');
        $response = $httpClient -> post($url, $fbParams);
        $result = $response -> body;

        $id = null;
        if ($result) {
            $result = json_decode($result, true);

            if (!isset($result['error'])) {
                $id = $result['id'];
            }
        }

        return $id;
    }
}