<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Thirdparty\Facebook\Extractor,
    Frontend\Models\Location,
    Frontend\Models\Venue as Venue,
    Frontend\Models\MemberNetwork,
    Frontend\Models\Category as Category,
    Frontend\Models\EventCategory as EventCategory,
    Frontend\Models\Event as Event,
    Objects\EventImage,
    Objects\EventSite,
    Objects\EventMember,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\EventLike,
    Objects\EventTag AS EventTagObject,
    Objects\Tag AS TagObject,
	Core\Utils\SlugUri as SUri,
	Frontend\Models\EventImage as EventImageModel;


/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{

    use \Core\Traits\TCMember;

    protected $friendsUid 		= array();
    protected $friendsGoingUid	= array();
    protected $pagesUid 		= array();


	public function initialize()
	{
		parent::initialize();

		if (!$this -> session -> has('isGrabbed')) {
			$this -> session -> set('isGrabbed', false);
			$this -> session -> set('grabOnce', false);
			$this -> session -> set('lastFetchedEvent', 0);
		}

		$f = fopen('/var/tmp/pthread_log.txt', 'a+');
		if ($this -> session -> get('grabOnce') === true) {
			fwrite($f, "init grabOnce is true\r\n");
		} elseif($this -> session -> get('grabOnce') === false) {
			fwrite($f, "init grabOnce is false\r\n");
		} else {
			fwrite($f, "init grabOnce is oooops\r\n");
		}
		if ($this -> session -> get('isGrabbed') === true) {
			fwrite($f, "init isGrabbed is true\r\n");
		} elseif($this -> session -> get('isGrabbed') === false) {
			fwrite($f, "init isGrabbed is false\r\n");
		} else {
			fwrite($f, "init isGrabbed is oooops\r\n");
		}
		fclose($f);
	}	

	/**
	 * @Route("/map", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 
	 */
	public function mapAction()
	{
		$this -> session -> set('lastFetchedEvent', 0);
		$this -> view -> setVar('view_action', $this -> request -> getQuery('_url'));
		$this -> view -> setVar('link_to_list', true);
	}	
    

	/**
	 * @Route("/eventmap", methods={"GET", "POST"})
	 * @Route("/eventmap/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}", methods={"GET", "POST"})
	 * @Route("/eventmap/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}/{city}", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventmapAction($lat = null, $lng = null, $city = null)
	{
		$events = $this -> searchAction($lat, $lng, $city);

		if (count($events) > 0) {
			$res['status'] = 'OK';
			$res['message'] = $events;
            $this->sendAjax($res);
		} else {
			$res['status'] = 'ERROR';
			$res['message'] = 'no events';

            $this->sendAjax($res);
		}
	}


	/**
	 * @Route("/list", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function eventlistAction()
	{
		$this -> session -> set('lastFetchedEvent', 0);
		$events = $this -> searchAction();

		if (isset($events[0]) || isset($events[1])) {
			$this -> view -> setVar('events', array_merge($events[0], $events[1]));
			$this -> view -> setVar('eventsTotal', count($events[0]) + count($events[1]));
			$this -> session -> set('eventsTotal', count($events[0]) + count($events[1]));
		}else {
            $this -> view -> setVar('eventsTotal', 0);
            $this -> session -> set('eventsTotal', 0);
        }

        if ($this->session->has('memberId')) {
            $this->fetchMemberLikes();
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
            $newLocation = new Location();
            $newLocation = $newLocation->createOnChange(array('latitude' => $lat, 'longitude' => $lng));
            $this->session->set('location', $newLocation);

            $loc = $newLocation;
        }
		if(!empty($city)) {
            $newLocation->city = $city;
            $newLocation->alias = $city;
            $this->session->set('location', $newLocation);
		}

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
			$eventsList = $eventModel -> grabEventsByCoordinatesScale($loc -> latitude , $loc -> longitude, $this->session->get('memberId'));

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
						'pic_big' => '',
						'address' => $ev -> event -> address,
						'name' => $ev -> event -> name,
						'venue' => array('latitude' => $ev -> venue_latitude,
										 'longitude' => $ev -> venue_longitude),
						'location_id' => $ev -> event -> location_id,
                        'location' => $ev -> location,
						'description' => $ev -> event -> description,
						'logo' => $ev -> logo,
						'start_time' =>$ev -> event -> start_time,
						'start_date_nice' => $ev -> event -> start_date_nice,
						'end_time' => $ev -> event -> end_time,
						'end_date_nice' => $ev -> event -> end_date_nice,
                        'slugUri' => $ev->event->slugUri
					);

                    if (empty($newEv['venue']['latitude']) || empty($newEv['venue']['longitude'])) {
                        $newEv['venue']['latitude'] = $ev -> location_latitude;
                        $newEv['venue']['longitude'] = $ev -> location_longitude;
                    }

                    $events[$elem][] = $newEv;
				}
			} else {
				$checkLoc = new Location();
				$checkLoc -> createOnChange(array('latitude' => $loc -> latitude, 
												  'longitude' => $loc -> longitude));
			}
			return $events;
		} 
	}

	
	/**
	 * @Route("/event/{eventId:[0-9]+}-{slugUri}", methods={"GET", "POST"})
	 * @Acl(roles={'guest', 'member'});   	 	 	 
	 */
	public function showAction($eventId)
	{
		$event = Event::findFirst($eventId);
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
        $cfg = $this -> di -> get('config');

        $logoFile = '';
        if ($event -> logo != '') {
           $logoFile = $cfg -> application -> uploadDir . 'img/event/'. $event -> logo;
        }

        $logo = 'http://'.$_SERVER['HTTP_HOST'].'/upload/img/event/'. $event -> logo;
        if (!file_exists($logoFile)) {
            $logo = 'http://'.$_SERVER['HTTP_HOST'].'/img/logo200.png';
        }
        $this -> view -> setVar('logo', $logo);
		$this -> view -> setVar('event', $event);
        $categories = Category::find();
        $this -> view -> setVar('categories', $categories->toArray());

        $this -> view -> setVar('link_back_to_list', true);

        $posters = $flyers = $gallery = [];
        if (isset($event->id)) {
            $eventImages = EventImageModel::find('event_id = ' . $event->id);

            foreach ($eventImages as $eventImage) {
                if ($eventImage->type == 'poster') {
                    $posters[] = $eventImage;
                } else if ($eventImage->type == 'flyer') {
                    $flyers[] = $eventImage;
                } else if ($eventImage->type == 'gallery') {
                    $gallery[] = $eventImage;
                }
            }
        }

        $this->view->setVar('poster', isset($posters[0]) ? $posters[0] : null );
        $this->view->setVar('flyer', isset($flyers[0]) ? $flyers[0] : null );
        $this->view->setVar('gallery', $gallery);

        return array(
            'currentWindowLocation' => 'http://'.$_SERVER['HTTP_HOST'].'/event/'.$event->id.'-'.SUri::slug($event->name),
            'eventMetaData' => $event
        );
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

        } else {
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

                if ($status == EventMember::JOIN) {
                    $userEventsGoing = $this -> session -> get('userEventsGoing') + 1;
                    $this -> session -> set('userEventsGoing', $userEventsGoing);
                }
			} 
		} else {
			$ret['error'] = 'not_logged';	
		}
		
		echo json_encode($ret);
		//die;
	}


	/**
	 * @Route("/event/friends", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listFriendAction()
	{
        $event = new Event();

		$this -> view -> setvar('listName', 'Friends events');

        $event->addCondition('Frontend\Models\EventMemberFriend.member_id = ' . $this -> session -> get('memberId'));
        $event->addCondition('Frontend\Models\Event.start_date > now()');
        $events = $event->fetchEvents();

        $this->view->setvar('list', $events);
        $this->view->setVar('listTitle', 'Friends events');
        $this->view->pick('event/eventList');
	}


	/**
	 * @Route("/event/liked", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listLikedAction()
	{
        $event = new Event();

		$this -> view -> setvar('listName', 'Liked Events');

		//$event -> setCondition('event_like.member_id = ' . $this -> session -> get('memberId'));
		//$events = $event -> listEvent();

        $event->addCondition('Frontend\Models\EventLike.member_id = '.$this->session->get('memberId'));
        $event->addCondition('Frontend\Models\EventLike.status = 1');
        $events = $event->fetchEvents();

        if ($this->session->has('memberId')) {
            $this->fetchMemberLikes();
        }

        $this->view->setvar('list', $events);
        $this->view->setVar('listTitle', 'Liked');
        $this->view->pick('event/eventList');
	}

	/**
	 * @Route("/event/joined", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listJoinedAction()
	{
		$event = new Event();
		$this -> view -> setvar('listName', 'Where I Go');

		$event->addCondition('Objects\EventMember.member_id = '.$this->session->get('memberId'));
		$event->addCondition('Objects\EventMember.member_status = 1');
		$events = $event->fetchEvents();

        if ($this->session->has('memberId')) {
            $this->fetchMemberLikes();
        }

		$this -> view -> setvar('list_type', 'join');
		//$this -> view -> setvar('events', $events);
		//$this -> view -> pick('event/userlist');

        $this->view->setvar('list', $events);
        $this->view->setVar('listTitle', 'Where I Go');
        $this->view->pick('event/eventList');
	}


	/**
	 * @Route("/event/list", methods={"GET", "POST"})
	 * @Acl(roles={'member'});
	 */
	public function listAction()
	{
		parent::listAction();

        $this->view->setVar('listTitle', 'Created');

        $this->eventListCreatorFlag = true;
        $this->view->pick('event/eventList');

        return array('eventListCreatorFlag' => $this->eventListCreatorFlag);
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

        $posters = $flyers = $gallery = [];
        if (isset($this->obj->id)) {
            $eventImages = EventImageModel::find('event_id = ' . $this->obj->id);

            foreach ($eventImages as $eventImage) {
                if ($eventImage->type == 'poster') {
                    $posters[] = $eventImage;
                } else if ($eventImage->type == 'flyer') {
                    $flyers[] = $eventImage;
                } else if ($eventImage->type == 'gallery') {
                    $gallery[] = $eventImage;
                }
            }
        }

        $this->view->setVar('poster', isset($posters[0]) ? $posters[0] : null );
        $this->view->setVar('flyer', isset($flyers[0]) ? $flyers[0] : null );
        $this->view->setVar('gallery', $gallery);
	}

	public function setEditExtraRelations()
	{
		$this -> editExtraRelations = array(
			'venue' => array('latitude', 'longitude')
		);
	}


	/**
	 * @Route("/event/delete", methods={"GET", "POST"})
	 * @Route("/event/delete/{id:[0-9]+}", methods={"GET", "POST"})
	 * @Acl(roles={'member'});   	 
	 */
	public function deleteAction()
	{
		$data =  $this -> request -> getPost();
		$result['status'] = 'ERROR';

		if (isset($data['id']) && !empty($data['id'])) {
			$event = Event::findFirst((int)$data['id']);
			if ($event) {
                //$event -> delete();
                $event->event_status = 0;
                $event->save();

				$result['status'] = 'OK';
                $result['id'] = $data['id'];

                $result['userEventsLiked'] = EventLike::find(array('member_id = ' . $data['id'] . " AND status = 1"))->count();
                $result['userEventsGoing'] = $this -> session -> get('userEventsGoing');

                $userEventsCreated = $this -> session -> get('userEventsCreated') - 1;
                $this -> session -> set('userEventsCreated', $userEventsCreated);
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

                $response['likeCounter'] = EventLike::find(array('member_id = ' . $memberId . " AND status = 1"))->count();
                $this -> session -> set('userEventsLiked', $response['likeCounter']);

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

    public function saveEventAtFacebook($url, $fbParams)
    {
        $http = $this->di->get('http');
        $httpClient = $http::getProvider();

        $httpClient->setBaseUri('https://graph.facebook.com/');
        $response = $httpClient->post($url, $fbParams);
        $result = $response->body;

        $id = null;
        if ($result) {
            $result = json_decode($result, true);

            if (!isset($result['error'])) {
                $id = $result['id'];
            }
        }

        return $id;
    }

    /**
     * @Route("/event/facebook", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function facebookAction()
    {

        $http = $this->di->get('http');
        $httpClient = $http::getProvider();
        $httpClient->setBaseUri('https://graph.facebook.com/');


        $response = $httpClient->post('me/events', array(
            'access_token' => $this->session->get('user_token'),
            'name' => "!!!",
            'description' => "!!!",
            //'start_time' => date('c', strtotime('2012-02-01 13:00:00')),
            //'end_time' => date('c', strtotime('2012-02-01 14:00:00')),
            'location' => 'Moldova',
            'privacy_type' => 'SECRET'
        ));
        $result = $response->body;
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
        $newEvent['tickets_url'] = $event['tickets_url'];
		$newEvent['member_id'] = $this -> session -> get('memberId');
		$newEvent['is_description_full'] = 1;
		$newEvent['event_status'] = !is_null($event['event_status']) ? 1 : 0;
        $newEvent['event_fb_status'] = !is_null($event['event_fb_status']) ? 1 : 0;
		$newEvent['recurring'] = $event['recurring'];
		//$newEvent['logo'] = $event['logo'];
		$newEvent['campaign_id'] = $event['campaign_id'];
		if (isset($this -> session -> get('member') -> network)) {
			$newEvent['fb_creator_uid'] = $this -> session -> get('member') -> network -> account_uid;
		}
		
		// process location
		if (!empty($event['location_id'])) {
			$newEvent['location_id'] = $event['location_id']; 
		} elseif (!empty($event['location_latitude']) && !empty($event['location_longitude'])) {
			// check location by coordinates
			$location = $loc -> createOnChange(array('latitude' => $event['location_latitude'], 
													 'longitude' => $event['location_longitude']));
			$newEvent['location_id'] = $location -> id;
			$newEvent['latitude'] = $event['location_latitude'];
			$newEvent['longitude'] = $event['location_longitude'];
		} 
		// location coordinates wasn't set. Try to get location from venue coordinates 
		if (!empty($event['venue_latitude']) && !empty($event['venue_longitude'])) {
			if (!isset($newEvent['location_id'])) {
				$location = $loc -> createOnChange(array('latitude' => $event['venue_latitude'],
														 'longitude' => $event['venue_longitude']));
				$newEvent['location_id'] = $location -> id;
			}
			$newEvent['latitude'] = $event['venue_latitude'];
			$newEvent['longitude'] = $event['venue_longitude'];
		}

		// location coordinates wasn't set. Try to get location from address coordinates 
		if (!empty($event['address_latitude']) && !empty($event['address_longitude'])) {
			if (!isset($newEvent['location_id'])) {
				$location = $loc -> createOnChange(array('latitude' => $event['address_latitude'],
														 'longitude' => $event['address_longitude']));
				$newEvent['location_id'] = $location -> id;
			}
			$newEvent['latitude'] = $event['address_latitude'];
			$newEvent['longitude'] = $event['address_longitude'];
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

		//process images
        $logo = null; $poster = null; $flyer = null;
		foreach ($this -> request -> getUploadedFiles() as $file) {
            if ($file->getKey() == 'add-img-logo-upload') {
                $logo = $file;
            } else if ($file->getKey() == 'add-img-poster-upload') {
                $poster = $file;
            } else if ($file->getKey() == 'add-img-flyer-upload') {
                $flyer = $file;
            }
		}
//_U::dump($newEvent);	

		if (!empty($event['id'])) {
			$ev = Event::findFirst($event['id']);
		} else {
			$ev = new Event();
		}
		$ev -> assign($newEvent);
		if ($ev -> save()) {
            // create event dir if not exists
            if (!is_dir($this->config->application->uploadDir . 'img/event/' . $ev->id)) {
                mkdir($this->config->application->uploadDir . 'img/event/' . $ev->id);
            }

            // start prepare params for FB event
            $fbParams = array(
                'access_token' => $this->session->get('user_token'),
                'name' => $newEvent['name'],
                'description' => $newEvent['description'],
                'start_time' => date('c', strtotime($newEvent['start_date'])),
                'privacy_type' => $newEvent['event_status'] == 0 ? 'SECRET' : 'OPEN'
            );

            /*if ($newEvent['event_fb_status'] == 1) {
                $fbParams['privacy_type'] = 'OPEN';
            }*/

            if ($newEvent['start_date'] !== $newEvent['end_date']) {
                $fbParams['end_time'] = date('c', strtotime($newEvent['end_date']));
            }

            if ($event['venue'] != '') {
                $fbParams['location'] = $event['venue'];
            } else if ($event['address'] != '') {
                $fbParams['location'] = $event['address'];
            } else {
                $fbParams['location'] = $event['location'];
            }

			// save image
            $file = ROOT_APP . 'public' . $this->config->application->defaultLogo;
			if (isset($logo)) {
                $filename = $this->uploadImageFile($ev->logo, $logo, $this->config->application->uploadDir . 'img/event/' . $ev->id);
                $file = $this -> config -> application -> uploadDir . 'img/event/' . $ev->id . '/' . $filename;
                $ev->logo = $filename;
                $ev->save();
			} else if ($ev->logo != '') {
                $file = $this -> config -> application -> uploadDir . 'img/event/' . $ev->id . '/' . $ev->logo;

            } else {
                $ev->logo = '';
                $ev->save();
            }

            list($width, $height, $type, $attr) = getimagesize($file);
            if ($width < 180 || $height < 60) {
                $fbParams['cover.jpg'] = '@' . ROOT_APP . 'public' . $this->config->application->defaultLogo;
            } else {
                $fbParams['cover.jpg'] = '@' . $file;
            }
            // finish prepare params for FB event

            if ($newEvent['event_fb_status'] == 1) {
                // add/edit event to facebook
                if (!isset($ev->fb_uid) || $ev->fb_uid == '') {
                    $fbEventId = $this->saveEventAtFacebook('me/events', $fbParams);

                    if (!is_null($fbEventId)) {
                        $ev->fb_uid = $fbEventId;
                        $ev->save();
                    }
                } else {
                    $this->saveEventAtFacebook('/' . $ev->fb_uid, $fbParams);
                }
            }

			// process site
			$eSites = EventSite::find('event_id = '. $ev -> id);
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
			$eCats = EventCategory::find('event_id = '. $ev -> id);
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

            // process poster and flyer
            $addEventImage = function($image, $imageType) use ($ev) {
                $eventPoster = EventImageModel::findFirst('event_id = ' . $ev->id . ' AND type = "' . $imageType . '"');

                $filename = $this->uploadImageFile(
                    empty($eventPoster) ? '' : $eventPoster->image,
                    $image,
                    $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $imageType
                );

                if ($eventPoster) {
                    $eventPoster->image = $filename;
                } else {
                    $eventPoster = new EventImageModel();
                    $eventPoster->event_id = $ev->id;
                    $eventPoster->image = $filename;
                    $eventPoster->type = $imageType;
                }

                $eventPoster->save();
            };

            if (!empty($poster)) {
                $addEventImage($poster, 'poster');
            }

            if (!empty($flyer)) {
                $addEventImage($flyer, 'flyer');
            }

            if (empty($event['id'])) {
                $userEventsCreated = $this -> session -> get('userEventsCreated') + 1;
                $this -> session -> set('userEventsCreated', $userEventsCreated);
            }
		}

        $this -> loadRedirect();
	}

    /**
     * @param $oldFilename string
     * @param $file \Phalcon\Http\Request\FileInterface
     * @param $path string
     *
     * Upload Image of type jpeg, png
     *
     * @return string
     */
    protected function uploadImageFile($oldFilename, $file, $path)
    {
        if (!is_dir($path)) {
            mkdir($path);
        }

        $imgExts = array('image/jpeg', 'image/png');

        $filename = '';
        if (in_array($file->getType(), $imgExts)) {
            $parts = pathinfo($file->getName());

            $filename = $parts['filename'] . '_' . md5($file->getName() . date('YmdHis')) . '.' . $parts['extension'];
            $file->moveTo($path . '/' . $filename);

            if (!is_dir($path . '/' . $oldFilename) && file_exists($path . '/' . $oldFilename)) {
                unlink($path . '/' . $oldFilename);
            }
        }

        return $filename;
    }

    /**
     * @Route("/event/import-categories", methods={"GET", "POST"})
     * @Acl(roles={'member','guest'});
     */
    /*public function importCategoriesAction()
    {
        $Parser = new \Categoryzator\Core\Parser();
        $categories = $Parser->getCategories();

        if (!empty($categories)) {
            foreach ($categories as $categoryKey => $children) {
                $Category = new Category();

                $Category->key = $categoryKey;
                $Category->name = ucfirst($categoryKey);
                $Category->parent_id = 0;

                if ($categoryKey === 'other') {
                    $Category->is_default = 1;
                }

                $Category->save();
            }

            foreach ($categories as $categoryKey => $children) {
                $parent = Category::findFirst('key = "'.$categoryKey.'"');
                if (!empty($children)) {
                    unset($children[0]);
                    foreach ($children as $key => $cat) {
                        $Tag = new Tag();

                        $Tag->key = $cat;
                        $Tag->name = ucfirst($cat);
                        $Tag->category_id = $parent->id;

                        $Tag->save();
                    }
                }
            }
        }

        exit('DONE');
    }*/


    /** 
     * @Route("/event/preview", methods={"POST"})
     * @Acl(roles={'member'});
     */
	public function eventPreviewAction()
    {
        $post = $this->request->getPost();

        $uploadedFiles = $this->request->getUploadedFiles();

        if (!empty($uploadedFiles)) {

            foreach ($this -> request -> getUploadedFiles() as $file) {
                if ($file->getKey() == 'add-img-logo-upload') {
                    $filePath = $this->config->application->uploadDir.'img/event/tmp/'.time().rand(1000, 9999).$file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['logo'] = end($logoPieces);
                    $file->moveTo($filePath);

                } else if ($file->getKey() == 'add-img-poster-upload') {
                    $filePath = $this->config->application->uploadDir.'img/event/tmp/'.time().rand(1000, 9999).$file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['poster'] = end($logoPieces);
                    $file->moveTo($filePath);
                } else if ($file->getKey() == 'add-img-flyer-upload') {
                    $filePath = $this->config->application->uploadDir.'img/event/tmp/'.time().rand(1000, 9999).$file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['flyer'] = end($logoPieces);
                    $file->moveTo($filePath);
                }
            }

        }

        if (!empty($post['event_logo'])) {
            $this->view->setVar('eventPreviewLogo', $post['event_logo']);
        }

        if (!empty($post['event_poster'])) {
            $this->view->setVar('eventPreviewPoster', $post['event_poster']);
        }

        if (!empty($post['event_flyer'])) {
            $this->view->setVar('eventPreviewFlyer', $post['event_flyer']);
        }

        $Event = new \stdClass();

        if (isset($post['id'])) {
            $Event->id = $post['id'];
        }else {
            $Event->id = 0;
        }
        $Event->name = $post['name'];
        $Event->start_date = $post['start_date'];
        $Event->start_time = $post['start_time'];
        $Event->end_date = $post['end_date'];
        $Event->end_time = $post['end_time'];
        $Event->description = $post['description'];
        $Event->tickets_url = $post['tickets_url'];
        $loc = new \stdClass();
        $loc->alias = $post['location'];
        $Event->location = $loc;
        $Event->address = $post['address'];
        $Event->venue = $post['venue'];
        $Event->event_site = $post['event_site'];
        $Event->logo = $post['logo'];
        $site = [];
        foreach (explode(',', $post['event_site']) as $s) {
            if (!empty($s)) {
                $ss = new \stdClass();
                $ss->url = $s;
                $site[] = $ss;
            }
        }
        $Event->site = $site;
        $Event->category = Category::find('id = '.(int)$post['category']);
        $Event->memberpart = null;

        $this->view->setVar('currentWindowLocation', 'http://'.$_SERVER['HTTP_HOST'].'/event/'.$Event->id.'-'.SUri::slug($Event->name));
        $this->view->setVar('eventPreview', 'http://'.$_SERVER['HTTP_HOST'].'/event/'.$Event->id.'-'.SUri::slug($Event->name));

        $this->view->setVar('event', $Event);
        $this->view->setVar('poster', $post['poster']);
        $this->view->setVar('flyer', $post['flyer']);

        $this->view->pick('event/show');
    }

        /**
     * @Route("/event/test-get", methods={'GET'})
     * @Route("/event/test-get/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}", methods={"GET", "POST"})
     * @Route("/event/test-get/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}/{city}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function testGetAction($lat = null, $lng = null, $city = null)
    {
        $Event = new Event();
        $EventFriend = new EventMemberFriend();
		$loc = $this -> session -> get('location');

        if(!empty($lat) && !empty($lng)) {
            $newLocation = new Location();
            $newLocation = $newLocation -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));
		
			if ($newLocation -> id != $loc -> id) {
				if(!empty($city)) {
		            $newLocation -> city = $city;
		            $newLocation -> alias = $city;
				}

	            $this -> session -> set('location', $newLocation);

	            // check cache and reset if needed
	            $locationsScope = $this -> cacheData -> get('locations');

	            if (!isset($locationsScope[$newLocation -> id])) {
	            	$locationsScope[$newLocation -> id] = array(
	                                    'latMin' => $newLocation -> latitudeMin,
	                                    'lonMin' => $newLocation -> longitudeMin,
	                                    'latMax' => $newLocation -> latitudeMax,
	                                    'lonMax' => $newLocation -> longitudeMax,
	                                    'city' => $newLocation -> city,
	                                    'country' => $newLocation -> country);
		            $this -> cacheData -> delete($locations);
		            $this -> cacheData -> save('locations', $locationsScope);
	            }
	            $this -> logIt("location changed");

	            $this -> session -> set('isGrabbed', false);
	            $this -> session -> set('grabOnce', false);
	            $this -> session -> set('lastFetchedEvent', 0);

	            $loc = $this -> session -> get('location');
	        }
        }

        $Event -> addCondition('Frontend\Models\Event.latitude BETWEEN ' . $loc -> latitudeMin . ' AND ' . $loc -> latitudeMax.' 
        						AND Frontend\Models\Event.longitude BETWEEN '.$loc -> longitudeMin.' AND '.$loc -> longitudeMax .'
        						AND Frontend\Models\Event.start_date > now()');
		$Event -> addCondition('Frontend\Models\Event.id > ' . $this -> session -> get('lastFetchedEvent'));
        $events = $Event -> fetchEvents(Event::FETCH_ARRAY, Event::ORDER_ASC);

		if ($this -> session -> has('user_token') && $this -> session -> has('user_fb_uid') && $this -> session -> has('memberId')) {
			$res['eventsCreated'] = $Event -> getCreatedEventsCount($this -> session -> get('memberId'));
			$res['eventsFriendsGoing'] = $EventFriend -> getEventMemberFriendEventsCount($this -> session -> get('memberId'));

			$this -> session -> set('userEventsCreated', $res['eventsCreated']);
	        $this -> session -> set('userFriendsEventsGoing', $res['eventsFriendsGoing']);

			$this -> view -> setVar('userEventsCreated', $res['eventsCreated']);
	        $this -> view -> setVar('userFriendsGoing', $res['eventsFriendsGoing']);
	        $this -> session -> set('lastFetchedEvent', $events[count($events)-1]['id']);

	        if (count($events) > 0) {
            	$this -> session -> set('lastFetchedEvent', $events[count($events)-1]['id']);
            }
		} else {
			$this -> session -> set('lastFetchedEvent', 0);
			$this -> session -> set('isGrabbed', true);
		}

		$f = fopen('/var/tmp/pthread_log.txt', 'a+');
		if ($this -> session -> get('grabOnce') === true) {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st grabOnce is true\r\n");
		} elseif($this -> session -> get('grabOnce') === false) {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st grabOnce is false\r\n");
		} else {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st grabOnce is oooops\r\n");
		}
		if ($this -> session -> get('isGrabbed') === true) {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st isGrabbed is true\r\n");
		} elseif($this -> session -> get('isGrabbed') === false) {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st isGrabbed is false\r\n");
		} else {
			fwrite($f, date('Y-m-d H:i:s') . ": 1-st isGrabbed is oooops\r\n");
		}
		fclose($f);


        if (count($events) > 0) {
            $res['status'] = true;
            $res['events'] = $events;
        } else {
            $res['status'] = 'ERROR';
            $res['message'] = 'no events';
        }

		if ($this -> session -> get('grabOnce') === true) {
			$this -> logIt("2-nd grabOnce is true");
		} elseif($this -> session -> get('grabOnce') === false) {
			$this -> logIt("2-nd grabOnce is false");
		} else {
			$this -> logIt("2-nd grabOnce is ooops");
		}
		if ($this -> session -> get('isGrabbed') === true) {
			$this -> logIt("2-nd isGrabbed is true");
		} elseif($this -> session -> get('isGrabbed') === false) {
			$this -> logIt("2-nd isGrabbed is false");
		} else {
			$this -> logIt("2-nd isGrabbed is oooops");
		}

        $res['stop'] = $this -> session -> get('isGrabbed');
        $this -> sendAjax($res);


		if ($this -> session -> get('grabOnce') === true) {
			$this -> logIt("3-rd grabOnce is true");
		} elseif($this -> session -> get('grabOnce') === false) {
			$this -> logIt("3-rd grabOnce is false");
		} else {
			$this -> logIt("3-rd grabOnce is oooops");
		}

		if ($this -> session -> get('isGrabbed') === true) {
			$this -> logIt("3-rd isGrabbed is true");
		} elseif($this -> session -> get('isGrabbed') === false) {
			$this -> logIt("3-rd isGrabbed is false");
		} else {
			$this -> logIt("3-rd isGrabbed is oooops");
		}

       	if ($this -> session -> has('user_token') 
       		&& $this -> session -> has('user_fb_uid')
    		&& $this -> session -> get('isGrabbed') === false
    		&& $this -> session -> get('grabOnce') === false) 
        {
        	$this -> session -> set('grabOnce', true);
			$this -> logIt("in pointer");
        	$this -> grabNewEvents();	
        } 
       // $this -> grabNewEvents();
    }



    public function grabNewEvents()
    {
    	$loc = $this -> session -> get('location');
    	$fb = new Extractor();
    	$queries = $fb -> getQueriesScope();
    	$e = new Event();

		foreach ($queries as $key => $query) {

			if ($query['name'] == 'user_event') {
				$this -> logIt("user_event");
				$replacements = array($loc -> latitudeMin, 
									  $loc -> latitudeMax, 
									  $loc -> longitudeMin, 
									  $loc -> longitudeMax, 
									  $loc -> city, 
									  $this -> session -> get('user_fb_uid'));

				$fql = array($query['name'] => preg_replace($query['patterns'], $replacements, $query['query']));
				$result = $fb -> getFQL($fql, $this -> session -> get('user_token'));

				if ($result['STATUS'] !== false && count($result['MESSAGE'][0]['fql_result_set']) > 0) {
					$events = $e -> parseNewEvents($result['MESSAGE'][0]['fql_result_set'], 
													array('id' => $loc -> id,
														  'latitudeMin' => $loc -> latitudeMin,
														  'latitudeMax' => $loc -> latitudeMax,
														  'longitudeMin' => $loc -> longitudeMin,
														  'longitudeMax' => $loc -> longitudeMax));
				} 
				continue;
			}

			if ($query['name'] == 'friend_uid') {
				$this -> logIt("friend_uid");
				$replacements = array($this -> session -> get('user_fb_uid'));
				$fql = array($query['name'] => preg_replace($query['patterns'], $replacements, $query['query']));
				$result = $fb -> getFQL($fql, $this -> session -> get('user_token'));

				if ($result['STATUS'] !== false && count($result['MESSAGE'][0]['fql_result_set']) > 0) {
					foreach ($result['MESSAGE'][0]['fql_result_set'] as $f => $v) {
						$this -> friendsUid[] = $v['uid2'];
					}
				}
				continue;
			} 

/*			if ($query['name'] == 'friend_event' && !empty($this -> friendsUid)) {
				$this -> logIt("friend_event");
				$start = $query['start'];
				$limit = $query['limit'];
				$fUids = implode(',', $this -> friendsUid);

				do {
					$replacements = array($start, 
										  $limit, 
										  $loc -> latitudeMin, 
										  $loc -> latitudeMax, 
										  $loc -> longitudeMin, 
										  $loc -> longitudeMax, 
										  $loc -> city, 
										  $this -> session -> get('user_fb_uid'), 
										  $fUids);
					$fql = array($query['name'] => preg_replace($query['patterns'], $replacements, $query['query']));
					$result = $fb -> getFQL($fql, $this -> session -> get('user_token'));
					if ($result['STATUS'] !== false) {
						if (count($result['MESSAGE'][0]['fql_result_set']) > 0) {
							$events = $e -> parseNewEvents($result['MESSAGE'][0]['fql_result_set'], 
															array('id' => $loc -> id,
																  'latitudeMin' => $loc -> latitudeMin,
																  'latitudeMax' => $loc -> latitudeMax,
																  'longitudeMin' => $loc -> longitudeMin,
																  'longitudeMax' => $loc -> longitudeMax));

							if (count($result['MESSAGE'][0]['fql_result_set']) < (int)$limit) {
								$start = false;
							} else {
								$start = $start + $limit;
							}
						} else {
							$start = false;
						}
					} else {
						$start = false;
					}
				} while($start !== false);

				continue;
			} */

			if ($query['name'] == 'friend_going_eid' && !empty($this -> friendsUid)) {
				$this -> logIt("friend_going_eid");
				$replacements = array(implode(',', $this -> friendsUid));
				$fql = array($query['name'] => preg_replace($query['patterns'], $replacements, $query['query']));
				$result = $fb -> getFQL($fql, $this -> session -> get('user_token'));
//_U::dump($result, true);
				if ($result['STATUS'] !== false && count($result['MESSAGE'][0]['fql_result_set']) > 0) {
					foreach ($result['MESSAGE'][0]['fql_result_set'] as $f => $v) {
						$this -> friendsGoingUid[] = $v['eid'];
					}
				}
				continue;
			}

/*			if ($query['name'] == 'friend_going_event' && !empty($this -> friendsGoingUid)) {
				$f = fopen('/var/tmp/pthread_log.txt', 'a+');
				fwrite($f, "friend_going_event\r\n");
				fclose($f);
				$start = $query['start'];
				$limit = $query['limit'];
				$eids = implode(',', $this -> friendsGoingUid);
				$friends = implode(',', $this -> friendsUid);

				do {
					$replacements = array($start, 
										  $limit, 
										  $loc -> latitudeMin, 
										  $loc -> latitudeMax, 
										  $loc -> longitudeMin, 
										  $loc -> longitudeMax, 
										  $loc -> city, 
										  $this -> session -> get('user_fb_uid'), 
										  $eids, 
										  $friends);
					$fql = array($query['name'] => preg_replace($query['patterns'], $replacements, $query['query']));
					
					$result = $fb -> getFQL($fql, $this -> session -> get('user_token'));
//_U::dump($result, true);					
					if ($result['STATUS'] !== false) { 
						if (count($result['MESSAGE'][0]['fql_result_set']) > 0) {
							$events = $e -> parseNewEvents($result['MESSAGE'][0]['fql_result_set'],
															array('id' => $loc -> id,
																  'latitudeMin' => $loc -> latitudeMin,
																  'latitudeMax' => $loc -> latitudeMax,
																  'longitudeMin' => $loc -> longitudeMin,
																  'longitudeMax' => $loc -> longitudeMax));

							foreach ($events as $i => $ev) {
								$friendsEvents = array('member_id' => $this -> session -> get('memberId'),
														 'event_id' => $ev);
								$emf = new EventMemberFriend();
								$emf -> assign($friendsEvents);
								$emf -> save();								
							}

							if (count($result['MESSAGE'][0]['fql_result_set']) < (int)$limit) {
								$start = false;
							} else {
								$start = $start + $limit;
							}
						} else {
							$start = false;
						}
					} else {
						$start = false;
					}
				} while($start !== false);

				break;
			} */
		}

		$this -> session -> set('isGrabbed', true);
		$this -> logIt("end of grab, isGrabbed = " . $this -> session -> get('isGrabbed'));

		exit; 
    } 


    public function logIt($mess)
    {
		$f = fopen('/var/tmp/pthread_log.txt', 'a+');
		fwrite($f, date('Y-m-d H:i:s') . ": " . $mess . "\r\n");
		fclose($f);
    }
}		

