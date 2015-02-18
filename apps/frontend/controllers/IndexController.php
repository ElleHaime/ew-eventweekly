<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Featured,
    Frontend\Models\Event,
    Frontend\Models\EventImage,
    Frontend\Models\EventRating;

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

        if ($this -> session -> has('member')) {
            $this -> response -> redirect('/list');
        } else {
			// get featured events
        	$featuredId = $trendingId = $resultFe = [];
 			
        	if ($featuredEvents = Featured::find(['object_type="event"'])) {
				foreach ($featuredEvents as $fe) {
					$featuredId[$fe -> object_id] = $fe -> priority;
					$resultFe[$fe -> priority] = [];
				}
				
				$events = Event::find(['id in(' . implode(",", array_keys($featuredId)) .')']);
				foreach ($events as $ev) {
					foreach ($resultFe as $key => $val) {
						if ($featuredId[$ev -> id] == $key) {
							$ev -> getCover();
							$resultFe[$key][] = $ev;
						}
					}
				}				
        	} else {
				// get first 14 events in current location
				$events = Event::find(['location_id = ' . $this -> session -> get('location') -> id . '
											and (end_date > now() 
											or start_date > "' . date('Y-m-d H:i:s', strtotime('today midnight')). '")', 
										'limit' => ['number' => 14]]);
				foreach($events as $ev) {
					$resultFe[1][] = $ev;
				}	 				        		
        	}
        	$this -> view -> setVar('featuredEvents', $resultFe);
        	
        	// get trending events
			$trendingEvents = EventRating::find(['location_id = ' . $this -> session -> get('location') -> id,
												'order' => 'rank DESC']);
			if (!is_null($trendingEvents -> count)) {
				foreach ($trendingEvents as $te) {
					$trendingId[$te -> event_id] = $te -> rank;
				}

				$events = Event::find(['id in(' . implode(",", array_keys($trendingId)) .')']);
				foreach ($events as $ev) {
					$trendingId[$ev -> id] = $ev;
				}
				$this -> view -> setVar('trendingEvents', $trendingId);
			}
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

