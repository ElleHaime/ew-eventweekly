<?php

namespace Frontend\Controllers;

use Core\Utils as _U;

/**
 * @RoutePrefix('/')
 */
class IndexController extends \Core\Controller
{
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

        if ($this->session->has('member')) {
            $this->response->redirect('/map');
        }
    }


    /**
	 * @Get('ooops')
	 * @Acl(roles={'guest', 'member'}); 
	 */
    public function notfoundAction()
    {	
    }



    /**
	 * @Acl(roles={'guest', 'member'}); 
	 */
    public function deniedAction()
    {	
    	$this -> view -> pick('index/denied');
    }

}

