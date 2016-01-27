<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Featured,
    Frontend\Models\Event,
    Frontend\Models\EventImage,
    Frontend\Models\EventRating,
	Core\Utils\DateTime as _UDT;

/**
 * @RoutePrefix('/')
 */
class IndexController extends \Core\Controller
{
	use \Core\Traits\Sliders;
	
	
	/**
	 * @Get('')
	 * @Get('home')
	 * @Acl(roles={'guest', 'member'}); 
	 */
    public function indexAction()
    {
        $this -> view -> setVar('hideYouAreNotLoggedInBtn', true);

	    if ($this -> session -> has('eventsTotal')) {
		    $this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
	    }
	    
	    $this -> composeSliders($this -> session -> get('location') -> id);
		//$this -> view -> pick('index/techworks');
    }

    
    /**
     * @Route("/freelisting", methods={"GET", "POST"})
     * @Acl(roles={'guest','member'});
     */
    public function freelistingAction()
    {
    	$this -> view -> setVar('hideYouAreNotLoggedInBtn', true);
    	$this -> view -> pick('index/promologin');
    }
    	

    /**
	 * @Get('ooops')
	 * @Acl(roles={'guest', 'member'}); 
	 */
    public function notfoundAction()
    {	
    }


    /**
     * @Route("/flush", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function flushAction()
    {
    	$this -> flushCache();
    }    
    
    
    /**
     * @Route("/syncounters", methods={'POST', 'GET'})
     * @Acl(roles={'guest', 'member'});
     */
    public function syncountersAction()
    {
    	$this -> syncTotalCounters();
    }

    /**
	 * @Acl(roles={'guest', 'member'}); 
	 */
    public function deniedAction()
    {	
    	$this -> view -> pick('index/denied');
    }
    
}

