<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Thirdparty\Facebook\Extractor,
	Frontend\Models\Location,
	Frontend\Models\Venue as Venue,	
	Frontend\Models\MemberNetwork,
	Frontend\Models\Category,
	Frontend\Models\EventCategory,
	Frontend\Models\Event as Event,
	Objects\EventImage,
	Objects\EventSite,
	Objects\EventMember;

/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{
	/**
	 * @Route("/map", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 
	 */
	public function mapAction()
	{
		$this -> view -> setVar('view_action', $this -> request -> getQuery('_url'));
		$this -> view -> setVar('link_to_list', true);
	}	
    

	/**
	 * @Route("/eventmap", methods={"GET", "POST"})
	 * @Route("/eventmap/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}", methods={"GET", "POST"})
	 * @Route("/eventmap/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}/{city:[a-zA-Z ]+}", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventmapAction($lat = null, $lng = null, $city = null)
	{
		$events = $this -> searchAction($lat, $lng, $city);

		if (count($events) > 0) {
			$res['status'] = 'OK';
			$res['message'] = $events;
			echo json_encode($res);				
			die();
		} else {
			$res['status'] = 'ERROR';
			$res['message'] = 'no events';
			echo json_encode($res);
			die();
		}
	}


	/**
	 * @Route("/list", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventlistAction()
	{
		$events = $this -> searchAction();

		if (isset($events[0]) || isset($events[1])) {
			$this -> view -> setVar('userEvents', $events[0]);
			$this -> view -> setVar('friendEvents', $events[1]);
			$this -> view -> setVar('eventsTotal', count($events[0]) + count($events[1]));
			$this -> session -> set('eventsTotal', count($events[0]) + count($events[1]));
		} 
		$this -> view -> pick('event/events');
	}



	/**
	 * @Route("/search", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function searchAction($lat = null, $lng = null, $city = null)
	{
        $loc = $this -> session -> get('location');
        
        if(!empty($lat) && !empty($lng)) {
            $loc -> latitude = $lat;
            $loc -> longitude = $lng ;
        }
		if(!empty($city)) {
			$loc -> city = $city;
			$loc -> alias = $city;
		}
		
		$this -> session -> set('location', $loc);
		$eventModel = new Event();

		if ($this -> session -> has('user_token') && $this -> session -> get('user_token') != null) {

			// user registered via facebook and has facebook account
			$events = $eventModel -> grabEventsByFbToken($this -> session -> get('user_token'), $this -> session -> get('location'));

			if (!empty($events['STATUS']) && ($events['STATUS'] == FALSE)) {
				echo $events['MESSAGE'];
				die;
			}

			if ((count($events[0]) > 0) || (count($events[1]) > 0)) {
				$totalEvents = count($events[0]) + count($events[1]);
				$this -> view -> setVar('eventsTotal', $totalEvents);
				$this -> session -> set('eventsTotal', $totalEvents);
				$events = $eventModel -> parseEvent($events);
				
				return $events;

			} else {
				$res['status'] = 'ERROR';
				$res['message'] = 'no events';
				echo json_encode($res);
				die();
			} 

		} else {

			// user registered via email
			$events = array();
			$scale = $this -> geo -> buildCoordinateScale($loc -> latitude , $loc -> longitude);
			$eventsList = $eventModel -> grabEventsByCoordinatesScale($scale, $this->session->get('memberId'));

			if ($eventsList -> count() > 0) {
				$events[0] = array();
				$events[1] = array();

				foreach ($eventsList as $ev) {
					if ($ev -> event -> member_id == $this -> session -> get('memberId')) {
						$elem = 0;
					} else {
						$elem = 1;
					}

					$events[$elem][] = array(
						'id' => $ev -> event -> id,
						'eid' => $ev -> event -> fb_uid,
						'pic_square' => '',
						'address' => $ev -> event -> address,
						'name' => $ev -> event -> name,
						'venue' => array('latitude' => $ev -> venue_latitude,
										 'longitude' => $ev -> venue_longitude),
						'location_id' => $ev -> event -> location_id,
                        'location' => $ev -> location,
						'anon' => $ev -> event -> description,
						'logo' => $ev -> logo,
						'start_time' => date('F, l d, H:i', strtotime($ev -> event -> start_date)),
						'end_time' => date('F, l d, H:i', strtotime($ev -> event -> end_date)),
					);
				}
			}
			return $events;
		} 
	}

	
	/**
	 * @Route("/event/show/{eventId:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function showAction($eventId)
	{
		$eventModel = new Event();

		$eventObj = $eventModel -> grabEventsByEwId($eventId);
		$event = array(
			'id' => $eventObj -> id,
			'eid' => $eventObj -> fb_uid,
			'name' => $eventObj -> name,
			'description' => $eventObj -> description,
			'start_time' => date('F, l d, H:i', strtotime($eventObj -> start_date)),
			'end_time' => date('F, l d, H:i', strtotime($eventObj -> end_date)),
			'logo' => $eventObj -> logo,
            'categories' => $eventObj->event_category->toArray()
		);
		
		if ($event['eid'] != '' && $eventObj -> is_description_full != 1) { 
			$descFull = $eventModel -> grabEventsDescription($event['eid']);
			
			if ($descFull && $descFull != '') {
				$event['description'] = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $descFull);
				$eventObj -> assign(array('description' => $event['description'],
                                          'is_description_full' => 1));
				$eventObj -> save();				
			}
		}
		
		$event['answer'] = 0;
		if ($this -> session -> has('memberId')) {
			$conditions = 'member_id = ' . $this -> session -> get('memberId') . ' AND event_id = ' . $eventId;
			$eventMember = EventMember::findFirst($conditions);
			
			if ($eventMember) {
				$event['answer'] = (int)$eventMember -> member_status;
			} 
		}

		$this -> view -> setVar('logo', $event['logo']);
		$this -> view -> setVar('event', $event);
        $categories = Category::find();
        $this->view->setVar('categories', $categories->toArray());
	}

    /**
     * @Route("/suggest-event-category/{eventId:[0-9]+}/{categoryId:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function setEventCategoryAction($eventId, $categoryId)
    {
        $status = false;

        if ($this->session->has('member')) {
            $CategoryEvent = new EventCategory();

            if ($CategoryEvent->save(array(
                    'event_id' => $eventId,
                    'category_id' => $categoryId
                ))) {
                $status = true;
            }
        }

        if ($this->request->isAjax()) {
            exit(json_encode(array('status' => $status)));
        }

        return $status;
    }


	/**
	 * @Route("/event/answer", methods={"GET", "POST"})
	 * @Acl(roles={'member'}); 
	 */
	public function answerAction()
	{
		if ($this -> session -> has('member')) {
			$member = $this -> session -> get('member');

			switch ($this -> request -> getPost('answer', 'string'))
			{
				case 'JOIN': $status = EventMember::JOIN; break;
				case 'MAYBE': $status = EventMember::MAYBE; break;
				case 'DECLINE': $status = EventMember::DECLINE; break;
			}
			$event_id = $this -> request -> getPost('event_id', 'string');

			$eventMember = new EventMember();
			$eventMember -> member_id =  $member -> id;
			$eventMember -> event_id  =  $event_id;
			$eventMember -> member_status = $status;
			$eventMember -> save();

			$ret['STATUS']='OK';
			echo json_encode($ret);
			die;
		}
	}


	/**
	 * @Route("/event/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 	 	 
	 */
	public function listAction()
	{
		parent::listAction();
	}

	/**
	 * @Route("/event/getLocations", methods={"POST"})
	 * @Acl(roles={'member'});
	 */
	public function getLocations()
	{

	}

	/**
	 * @Route("/event/edit", methods={"GET", "POST"})
	 * @Route("/event/edit/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction()
	{	
		parent::editAction();
	}

	public function setEditExtraRelations()
	{
		$this -> editExtraRelations = array(
			'location' => array('latitude', 'longitude'),
			'venue' => array('latitude', 'longitude')
		);
	}


	/**
	 * @Route("/event/delete/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function deleteAction()
	{
		parent::deleteAction();
	}
	
	
	public function processForm($form) 
	{
		_U::dump($form -> getFormValues(), true);
		_U::dump($this -> request -> getUploadedFiles(), true);
//die();
		$event = $form -> getFormValues();
		$loc = new Location();
		$venue = new Venue();
		$coords = array();
		$venueId = false;
		$newEvent = array();

		// process name and descirption
		$newEvent['name'] = $event['name'];
		$newEvent['description'] = $event['description'];
		$newEvent['member_id'] = $this -> session -> get('memberId');
		$newEvent['is_description_full'] = 1;
		$newEvent['event_status'] = $event['event_status'];
		$newEvent['recurring'] = $event['recurring'];
		$newEvent['logo'] = $event['logo'];
		$newEvent['campaign_id'] = $event['campaign_id'];
		if (isset($this -> session -> get('member') -> network)) {
			$newEvent['fb_creator_uid'] = $this -> session -> get('member') -> network -> account_uid;
		}
		
		// process location
		if (!empty($event['location_latitude']) && !empty($event['location_longitude'])) {
			// check location by coordinates
			$location = $loc -> createOnChange(array('latitude' => $event['location_latitude'], 
													 'longitude' => $event['location_longitude']), 
													 array('latitude', 'longitude'));
			$newEvent['location_id'] = $location -> id;

		} 
		// location coordinates wasn't set. Try to get location from venue or address coordinates 
		if (!isset($newEvent['location_id'])) {
			if (!empty($event['venue_latitude']) && !empty($event['venue_longitude'])) {
				if (!empty($coords)) {
					$scale = $geo -> buildCoordinateScale($event['venue_latitude'], $event['venue_longitude']);
					$query = 'latitude between ' . $scale['latMin'] . ' and ' . $scale['latMax'] . ' 
										and longitude between ' . $scale['lonMin'] . ' and ' . $scale['lonMax'];
					$location =  $loc::findFirst($query);
					$newEvent['location_id'] = $location -> id;
				}
			}
		}
		// venue/address coordinates wasn't set or location wasn't found
		if (!isset($newEvent['location_id'])) {
			if (!empty($event['location'])) {
				$location = $loc -> createOnChange(array('city' => $event['location']), array('city'));
				$newEvent['location_id'] = $location -> id; 
			}
		}

		// process venue
		if (!empty($event['venue_latitude']) && !empty($event['venue_longitude'])) {
			$venueInfo = array('latitude' => $event['venue_latitude'],
						       'longitude' => $event['venue_longitude']);
		}
		if ($newEvent['location_id']) {
			$venueInfo['location_id'] = $newEvent['location_id'];
		}
		$venueInfo['name'] = $event['venue'];
		$venueInfo['address'] = $event['address'];

		$vn = $venue -> createOnChange($venueInfo);
		$newEvent['venue_id'] = $vn -> id;

		// process address
		$newEvent['address'] = $event['address'];

		// process date and time
		if (!empty($event['start_date'])) {
			$newEvent['start_date'] = implode('-', array_reverse(explode('/', $event['start_date'])));  
			if (!empty($event['start_time'])) {
				$newEvent['start_date'] = $newEvent['start_date'] . ' ' . $event['start_time'];  
			} 
		}
		
		if (!empty($event['end_date'])) {
			$newEvent['end_date'] = implode('-', array_reverse(explode('/', $event['end_date'])));
			if (!empty($event['end_time'])) {
				$newEvent['end_date'] = $newEvent['end_date'] . ' ' . $event['end_time'];
			}
		}

		//process image
		foreach ($this -> request -> getUploadedFiles() as $file) {
			$newEvent['logo'] = $file -> getName();
			$logo = $file;
		}
//_U::dump($newEvent);	

		if (!empty($event['id'])) {
			$ev = Event::findFirst($event['id']);
		} else {
			$ev = new Event();
		}
		$ev -> assign($newEvent);
		if ($ev -> save()) {
			// save image
			if (isset($logo)) {
				$logo -> moveTo($this -> config -> application -> uploadDir . 'img/event/' . $logo -> getName());
			}

			// process site
			$eSites = EventSite::find(array('event_id' => $ev -> id));
			if ($eSites) {
				foreach ($eSites as $es) {
					$es -> delete();
				}
			}
			if (!empty($event['event_site'])) {
				$eSites = new EventSite();
				$aSites = explode(',', $event['event_site']);
				foreach($aSites as $key => $value) {
					if (!empty($value)) {
						$eSites -> assign(array('event_id' => $ev -> id,
										 		'url' => $value));
						$eSites -> save();
					}
				}
			}

			// process categories
			$eCats = EventCategory::find(array('event_id' => $ev -> id));
			if ($eCats) {
				foreach ($eCats as $ec) {
					$ec -> delete();
				}
			}
			if (!empty($event['category'])) {
				$eCats = new EventCategory();
				$aCats = explode(',', $event['category']);
				foreach($aCats as $key => $value) {
					if (!empty($value)) {
						$eCats -> assign(array('event_id' => $ev -> id,
											   'category_id' => $value));
						$eCats -> save();
					}
				}
			}
		}
		
		$this -> response -> redirect('/event/list');
	}

}		
