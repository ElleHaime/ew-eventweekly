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
	Objects\EventMember,
    Frontend\Models\EventLike;


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
			$this -> view -> setVar('events', array_merge($events[0], $events[1]));
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
                $this -> session -> set('eventsTotal', 0);
				$res['status'] = 'ERROR';
				$res['message'] = 'no events';
                if ($this->request->isAjax()) {
                    echo json_encode($res);
                    die();
                } else {
                    return array($events[0], $events[1]);
                }
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

					$newEv = array(
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
						'start_time' =>$ev -> event -> start_time,
						'start_date_nice' => $ev -> event -> start_date_nice,
						'end_time' => $ev -> event -> end_time,
						'end_date_nice' => $ev -> event -> end_date_nice,
					);

                    if (empty($newEv['venue']['latitude']) || empty($newEv['venue']['longitude'])) {
                        $newEv['venue']['latitude'] = $ev -> location_latitude;
                        $newEv['venue']['longitude'] = $ev -> location_longitude;
                    }

                    $events[$elem][] = $newEv;
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
		$event = Event::findFirst($eventId);
		
		if ($event -> fb_uid != '' && $event -> is_description_full != 1) {
			$descFull = $event -> grabEventsDescription($event -> fb_uid);
				
			if ($descFull && $descFull != '') {
				$description = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $descFull);
				$event -> assign(array('description' => $description,
									   'is_description_full' => 1));
				$event -> save();
			}
		}

        $memberpart = null;
		if ($this -> session -> has('member') && $event -> memberpart -> count() > 0) {
			foreach ($event -> memberpart as $mpart) {
				if ($mpart -> member_id == $this -> memberId) {
                    $memberpart = $mpart -> member_status;
					break;
				}
			}
		}
        $event -> memberpart = $memberpart;

        // TODO: refactor this. Get uploads dir and default logo url from config
        $logo = 'http://'.$_SERVER['HTTP_HOST'].'/upload/img/event/'. $event -> logo;
        if (!file_exists($logo)) {
            $logo = 'http://'.$_SERVER['HTTP_HOST'].'/img/logo200.png';
        }
        $this -> view -> setVar('logo', $logo);
		$this -> view -> setVar('event', $event);
        $categories = Category::find();
        $this -> view -> setVar('categories', $categories->toArray());

        $this -> view -> setVar('link_back_to_list', true);
	}

    /**
     * @Route("/suggest-event-category/{eventId:[0-9]+}/{categoryId:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member','guest'});
     */
    public function setEventCategoryAction($eventId, $categoryId)
    {
        $status['status'] = false;

        $existence = EventCategory::findFirst('event_id = '.$eventId);

        if ($existence && ($existence->eventpart2->key == 'other')) {

            $result = $this->modelsManager->executeQuery('UPDATE \Objects\EventCategory SET category_id = '.$categoryId.' WHERE event_id = '.$eventId);
            if ($result) {
                $status['status'] = true;
            }

        }else {
            $CategoryEvent = new EventCategory();

            if ($CategoryEvent->save(array(
                    'event_id' => $eventId,
                    'category_id' => $categoryId
                ))) {
                $status['status'] = true;
            }
        }

        if ($this->request->isAjax()) {
            exit(json_encode(array($status)));
        }

        return $status;
    }


	/**
	 * @Route("/event/answer", methods={"GET", "POST"})
	 * @Acl(roles={'member','guest'}); 
	 */
	public function answerAction()
	{
		$ret['status']='ERROR';
		
		if ($this -> session -> has('member')) {
			$data = $this -> request -> getPost();
			$member = $this -> session -> get('member');

			switch ($data['answer'])
			{
				case 'JOIN': $status = EventMember::JOIN; break;
				case 'MAYBE': $status = EventMember::MAYBE; break;
				case 'DECLINE': $status = EventMember::DECLINE; break;
			}

			$eventMember = new EventMember();
			$eventMember -> assign(array(
					'member_id' => $member -> id,
					'event_id' => $data['event_id'],
					'member_status' => $status
			));
			if ($eventMember -> save()) {
				$ret = array('status' => 'OK',
							 'event_member_status' => $data['answer']);
			} 
		} else {
			$ret['error'] = 'not_logged';	
		}
		
		echo json_encode($ret);
		//die;
	}

	/**
	 * @Route("/event/liked", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listLikedAction() 
	{
        $event = new Event();

		$this -> view -> setvar('listName', 'Liked Events');
		$event -> setCondition('event_like.member_id = ' . $this -> session -> get('memberId'));

		$events = $event -> listEvent();

		$this -> view -> setvar('list_type', 'like');
        $this -> view -> setvar('events', $events);
        $this -> view -> pick('event/userlist');
	}

	/**
	 * @Route("/event/joined", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listJoinedAction()
	{
		$event = new Event();
		$this -> view -> setvar('listName', 'Where I Go');
		$event -> setCondition('event_member.member_id = '.$this->session->get('memberId'));

		$events = $event->listEvent();
		
		$this -> view -> setvar('list_type', 'join');		
		$this -> view -> setvar('events', $events);
		$this -> view -> pick('event/userlist');		
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
	 * @Route("/event/edit", methods={"GET", "POST"})
	 * @Route("/event/edit/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function editAction()
	{
        $category = new Category();
        $this -> view -> setVar('categories', $category -> getDefaultIdsAsString());

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
	 * @Route("/event/delete}", methods={"GET"})
	 * @Route("/event/delete/{id:[0-9]+}", methods={"GET"})
	 * @Acl(roles={'member'});   	 
	 */
	public function deleteAction()
	{
		$data =  $this -> request -> getPost();
		$result['status'] = 'ERROR';

		if (isset($data['id']) && !empty($data['id'])) {
			$event = Event::findFirst((int)$id);
			if ($event) {
				$event -> delete();
				$result['status'] = 'OK';
			} 
		}

		echo json_encode($result);
	}


    /**
     * @Route("/event/like/{eventId:[0-9]+}/{status:[0-9]+}", methods={"GET","POST"})
     * @Acl(roles={'member','guest'});
     */
    public function likeAction($eventId, $status = 0)
    {
        $response = array(
            'status' => false
        );
        
        if ($this -> session -> has('member')) {
        	$memberId = $this -> session -> get('memberId');
        	$eventLike = EventLike::findFirst('event_id = '.$eventId.' AND member_id = '.$memberId);
        	if (!$eventLike) {
        		$eventLike = new EventLike();
        	}
        	$eventLike -> assign(array(
        		'event_id' => $eventId,
        		'member_id' => $memberId,
        		'status' => $status
        	));
        		 
        	if ($eventLike -> save()) {
       			$response['status'] = true;
       			$response['member_like'] = $status;
       			$response['event_id'] = $eventId;
       			
       			$this -> eventsManager -> fire('App.Event:afterLike', $this);
        	}
        } else {
        	$response['error'] = 'not_logged';
        }
        
        $this -> sendAjax($response);
	}
        
	/**
	 * @Route("/event/publish", methods={"GET", "POST"})
	 * @Route("/event/publish/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function publishAction()
	{
		$data =  $this -> request -> getPost();
		$result['status'] = 'ERROR';

		if (isset($data['id']) && !empty($data['id'])) {
			if ($res = $this -> updateStatus($data['id'], $data['event_status'])) {
				$result = array_merge($res, array('status' => 'OK'));
			}
		}

		echo json_encode($result);
	}
	
	/**
	 * @Route("/event/unpublish", methods={"GET", "POST"})
	 * @Route("/event/unpublish/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function unpublishAction()
	{
		$data =  $this -> request -> getPost();
		$result['status'] = 'ERROR';

		if (isset($data['id']) && !empty($data['id'])) {
			if ($res = $this -> updateStatus($data['id'], $data['event_status'])) {
				/* delete sites, event members, send mails etc */
				$result = array_merge($res, array('status' => 'OK'));
			}
		}

		echo json_encode($result);
	}


	private function updateStatus($id, $status)
	{
		$event = Event::findFirst((int)$id);
		$result = false;

		if ($event) {
			$event -> assign(array('event_status' => $status));
			if ($event -> save()) {
				$result = array('id' => $event -> id,
								'event_status' => $event -> event_status);
			} 
		} 

		return $result;
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
		$newEvent['event_status'] = !is_null($event['event_status']) ? 1 : 0;
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
		if (!empty($newEvent['location_id']) && $newEvent['location_id']) {
			$venueInfo['location_id'] = $newEvent['location_id'];
		}

		$venueInfo['name'] = $event['venue'];
		$venueInfo['address'] = $event['address'];

		$vn = $venue -> createOnChange($venueInfo);

        if ($vn) {
		    $newEvent['venue_id'] = $vn -> id;
        }
//_U::dump($newEvent);
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
				$aSites = explode(',', $event['event_site']);
				foreach($aSites as $key => $value) {
					if (!empty($value)) {
						$eSites = new EventSite();						
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
				$aCats = explode(',', $event['category']);
				foreach($aCats as $key => $value) {
					if (!empty($value)) {
						$eCats = new EventCategory();
						$eCats -> assign(array('event_id' => $ev -> id,
											   'category_id' => $value));
						$eCats -> save();
					}
				}
			}
		}

        $this -> loadRedirect();
	}
}		
