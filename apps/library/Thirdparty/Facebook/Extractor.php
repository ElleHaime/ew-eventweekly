<?php

namespace Thirdparty\Facebook;

use Thirdparty\Facebook\FacebookApiException,
	Thirdparty\FacebookGraph\FacebookSession,
	Thirdparty\FacebookGraph\FacebookRequest,
	Thirdparty\FacebookGraph\FacebookRequestException,
    Core\Utils as _U,
	Frontend\Models\Cron as Cron;


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
        
        try {
        	FacebookSession::setDefaultApplication($fb_config -> facebook -> appId,
        										   $fb_config -> facebook -> appSecret);
        	FacebookSession::enableAppSecretProof();
        	$this -> facebook = FacebookSession::newAppSession();
        	
        } catch(\Exception $e) {
        	print_r($e);
        }
        
    }
    
    
    public function getEventTicketUrl($fbUid, $eUrl = false)
    {
    	$ticketsUrl = false;
    	
    	if ($this -> di) {
    		$uToken = Cron::getLastActiveToken();

    		if ($uToken = Cron::getLastActiveToken()) {
    			$request = '/' . $fbUid . '?fields=ticket_uri&access_token=' . $uToken;
    			try {
    				$request = new FacebookRequest($this -> facebook, 'GET', $request);
    				$event = $request -> execute() -> getGraphObject() -> asArray();

					if (isset($event['ticket_uri'])) {
						$ticketsUrl = $event['ticket_uri'];
					}     			
    			} catch (FacebookRequestException $ex) {
    				$error = json_decode($ex -> getRawResponse() -> error -> code);
    			}
    		} else {
    			if ($eUrl) {
					$ticketsUrl = 'https://www.facebook.com/events/' . $fbUid;
				}
    		}
    	} 
    	
    	return $ticketsUrl;
    }
}
