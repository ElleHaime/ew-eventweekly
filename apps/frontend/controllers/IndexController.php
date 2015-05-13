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


			// get featured events
        	$featuredId = $trendingId = $resultFe = [];
        	$featuredEvents = Featured::find(['object_type="event" and location_id=' . $this -> session -> get('location') -> id]);
        	
        	if ($featuredEvents -> count() != 0) {
				foreach ($featuredEvents as $fe) {
					$featuredId[$fe -> object_id] = $fe -> priority;
					$resultFe[$fe -> priority] = [];
				}
				
				$evIds = "'" . implode("','", array_keys($featuredId)) . "'";
				$ev = (new Event()) -> setShardByCriteria($this -> session -> get('location') -> id);
				$events = $ev::find(['id in(' . $evIds .')']);
				
				foreach ($events as $ev) {
					foreach ($resultFe as $key => $val) {
						if ($featuredId[$ev -> id] == $key) {
							$ev -> cover = (new EventImage()) -> getCover($ev -> id);
							$resultFe[$key][] = $ev;
						}
					}
				}
        	} else {
        		// get last 14 events from current location
        		$queryData['searchLocationField'] = $this -> session -> get('location') -> id;
        		$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
        		$queryData['searchEndDate'] = _UDT::getDefaultEndDate();
        		
        		$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
        		$eventGrid -> setLimit(14);
        		$eventGrid -> setSort('start_date');
	    		$eventGrid -> setSortDirection('ASC');
	    		
        		$results = $eventGrid->getData();
        		
        		foreach($results['data'] as $ev) {
        			$ev -> cover = (new EventImage()) -> getCover($ev -> id);
        			$resultFe[1][] = $ev;
        		} 
        	}
        	$this -> view -> setVar('featuredEvents', $resultFe);

        	// get trending events
			$trendingEvents = EventRating::find(['location_id = ' . $this -> session -> get('location') -> id,
												'order' => 'rank DESC']);
			
			if ($trendingEvents -> count() != 0) {
				$resultTre = [];
				foreach ($trendingEvents as $te) {
					$trendingId[$te -> event_id] = $te -> rank;
				}
				
				$queryData = ['searchId' => array_keys($trendingId),
							  'searchStartDate' => _UDT::getDefaultStartDate()];				
				$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
				$eventGrid -> setSort('start_date');
				$eventGrid -> setSortDirection('ASC');
				$results = $eventGrid->getData();
				foreach($results['data'] as $ev) {
					$ev -> cover = (new EventImage()) -> getCover($ev -> id);
					$resultTre[] = $ev;
				}
				
				$this -> view -> setVar('trendingEvents', $resultTre);
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

