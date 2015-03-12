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
        	$featuredEvents = Featured::find(['object_type="event" and location_id=' . $this -> session -> get('location') -> id]);
        	
        	if ($featuredEvents -> count() != 0) {
				foreach ($featuredEvents as $fe) {
					$featuredId[$fe -> object_id] = $fe -> priority;
					$resultFe[$fe -> priority] = [];
				}
				
				$ev = (new Event()) -> setShardByCriteria($this -> session -> get('location') -> id);
				$events = $ev::find(['id in(' . implode(",", array_keys($featuredId)) .')']);
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
        		$ev = (new Event()) -> setShardByCriteria($this -> session -> get('location') -> id);
				$events = $ev::find(['location_id = ' . $this -> session -> get('location') -> id . '
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
			if ($trendingEvents -> count() != 0) {
				foreach ($trendingEvents as $te) {
					$trendingId[$te -> event_id] = $te -> rank;
				}
				$trendingEvents = [];
				foreach ($trendingId as $index => $event) {
					$e = new Event();
					$e -> setShardById($index);
					$trendingEvents[] = $e::findFirst($index);
				}
				
				$this -> view -> setVar('trendingEvents', $trendingEvents);
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

