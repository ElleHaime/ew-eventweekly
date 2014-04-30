<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
    Frontend\Models\Location,
    Frontend\Models\Venue as Venue,
    Frontend\Models\MemberNetwork,
    Frontend\Models\Category as Category,
    Frontend\Models\EventCategory as EventCategory,
    Frontend\Models\Event as Event,
    Objects\EventImage,
    Objects\EventSite,
    Frontend\Models\EventMember,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\EventMemberCounter,
    Frontend\Models\EventLike,
    Objects\EventTag AS EventTagObject,
    Objects\Tag AS TagObject,
    Core\Utils\SlugUri as SUri,
    Frontend\Models\EventImage as EventImageModel,
	Thirdparty\Facebook\Extractor,
	Categoryzator\Core\Inflector;

/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{

    use \Core\Traits\TCMember;

    protected $friendsUid = array();
    protected $friendsGoingUid = array();
    protected $userGoingUid = array();
    protected $userPagesUid = array();
    protected $pagesUid = array();
    protected $actualQuery = false;


    public function initialize()
    {
        parent::initialize();

        if (!$this->session->has('lastFetchedEvent')) {
            $this->session->set('lastFetchedEvent', 0);
        }
    }

    /**
     * @Route("/map", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function mapAction()
    {
        $this->session->set('lastFetchedEvent', 0);
        $this->view->setVar('view_action', $this->request->getQuery('_url'));
        $this->view->setVar('link_to_list', true);
    }

    
    /**
     * @Route("/list", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function eventlistAction()
    {
    	$this->session->set('lastFetchedEvent', 0);
    	
    	$postData = $this->request->getQuery();
    	$page = $this->request->getQuery('page');
    	if (empty($page)) {
    		$page = 1;
    	}
    	if ($this->session->get('memberId')) {
    		$applyPersonalization = true;
    	}else {
    		$applyPersonalization = false;
    	}
    	 
    	$loc = $this->session->get('location');
    	$event = new Event();
    	$event-> addCondition('Frontend\Models\Event.latitude BETWEEN ' . $loc->latitudeMin . ' AND ' . $loc->latitudeMax);
    	$event-> addCondition('Frontend\Models\Event.longitude BETWEEN ' . $loc->longitudeMin . ' AND ' . $loc->longitudeMax);
    	$event-> addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
    	$event-> addCondition('Frontend\Models\Event.start_date < "' . date('Y-m-d H:i:s', strtotime('today +3 days')) . '"');
    	$event-> addCondition('Frontend\Models\Event.id > ' . $this->session->get('lastFetchedEvent'));
    	$event-> addCondition('Frontend\Models\Event.event_status = 1');
    	
    	$result = $event->fetchEvents(Event::FETCH_OBJECT,
    			Event::ORDER_ASC,
    			['page' => $page, 'limit' => 10],
    			$applyPersonalization, [], false, false, false, true, true);
		$events = $result -> items;
		unset($result -> items);
		 
		if (isset($events)) {
			$this->view->setVar('pagination', $result);
		}
		$this->view->setVar('urlParams', http_build_query($postData));		
		$this->view->setVar('list', $events);
    	$this->view->pick('event/eventList');
    }
    
    /**
     * @Route("/event/friends", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listFriendAction()
    {
    	$postData = $this->request->getQuery();
    	$page = $this->request->getQuery('page');
    	if (empty($page)) {
    		$page = 1;
    	}
    	
    	$event = new Event();
    
    	$event->addCondition('Frontend\Models\EventMemberFriend.member_id = ' . $this->session->get('memberId'));
    	$event->addCondition('Frontend\Models\Event.event_status = 1');
    	$event->addCondition('Frontend\Models\Event.deleted = 0');
    	$event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
    	$result = $event->fetchEvents(Event::FETCH_OBJECT,
    			Event::ORDER_ASC,
    			['page' => $page, 'limit' => 10],
    			false, [], true, false, false, true, true);

    	$events = $result -> items;
    	unset($result -> items);
    	
    	if (isset($events)) {
    		$this->view->setVar('pagination', $result);
    	}
    	$this->view->setVar('urlParams', http_build_query($postData));
    	
    	$this->view->setvar('listName', 'Friend\'s events');
    	$this->view->setvar('list', $events);
    	$this->view->setVar('listTitle', 'Friend\'s events');
    	$this->view->pick('event/eventList');
    }
    
    
    /**
     * @Route("/event/liked", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listLikedAction()
    {
    	$postData = $this->request->getQuery();
    	$page = $this->request->getQuery('page');
    	if (empty($page)) {
    		$page = 1;
    	}
    	$event = new Event();
    
    	$this->view->setvar('listName', 'Liked Events');
    
    	$event->addCondition('Frontend\Models\EventLike.member_id = ' . $this->session->get('memberId'));
    	$event->addCondition('Frontend\Models\EventLike.status = 1');
    	$event->addCondition('Frontend\Models\Event.event_status = 1');
    	$event->addCondition('Frontend\Models\Event.deleted = 0');
    	$event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
    	$result = $event->fetchEvents(Event::FETCH_OBJECT,
    			Event::ORDER_ASC,
    			['page' => $page, 'limit' => 10],
    			false, [], false, false, true, true, true);
    	
    	$events = $result -> items;
    	unset($result -> items);
    	
    	if ($this->session->has('memberId')) {
    		$this->fetchMemberLikes();
    	}
    
    	if (isset($events)) {
    		$this->view->setVar('pagination', $result);
    	}
    	
    	$this->view->setVar('urlParams', http_build_query($postData));
    	
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
    	$postData = $this->request->getQuery();
    	$page = $this->request->getQuery('page');
    	if (empty($page)) {
    		$page = 1;
    	}
    	
    	$event = new Event();
		    
    	$event->addCondition('Frontend\Models\EventMember.member_id = ' . $this->session->get('memberId'));
    	$event->addCondition('Frontend\Models\EventMember.member_status = 1');
    	$event->addCondition('Frontend\Models\Event.event_status = 1');
    	$event->addCondition('Frontend\Models\Event.deleted = 0');
    	$event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
    	$result = $event->fetchEvents(Event::FETCH_OBJECT,
						    			Event::ORDER_ASC,
						    			['page' => $page, 'limit' => 10],
						    			false, [], false, true, false, true, true);
		$events = $result -> items;
		unset($result -> items);
		    	
    	if ($this->session->has('memberId')) {
    		$this->fetchMemberLikes();
    	}
    
    	$this->view->setvar('list_type', 'join');
    
    	if (isset($events)) {
    		$this->view->setVar('pagination', $result);
    	}
    	$this->view->setVar('list', $events);
    	$this->view->setVar('listTitle', 'Where I am going');
    	$this->view->setVar('urlParams', http_build_query($postData));
    	
    	$this->view->pick('event/eventList');
    }
    
    
    /**
     * @Route("/event/list", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listAction()
    {
    	$event = new Event();
    
    	$event->addCondition('Frontend\Models\Event.member_id = ' . $this->session->get('memberId'));
    	$event->addCondition('Frontend\Models\Event.deleted = 0');
    	$event->addCondition('Frontend\Models\Event.event_status IN (0, 1)');
    	$event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
    	$events = $event->fetchEvents();
    
    	if ($events->count()) {
    		$this->view->setVar('object', $events);
    		$this->view->setVar('list', $events);
    	}
    
    	$this->view->setVar('listTitle', 'Created');
    
    	$this->eventListCreatorFlag = true;
    	$this->view->pick('event/eventList');
    
    	return array('eventListCreatorFlag' => $this->eventListCreatorFlag);
    }
    
    
    /**
     * @Route("/{slugUri}-{eventId:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function showAction($slug, $eventId)
    {
        $event = Event::findFirst($eventId);
        $memberpart = null;
        if ($this->session->has('member') && $event->memberpart->count() > 0) {
            foreach ($event->memberpart as $mpart) {
                if ($mpart->member_id == $this->memberId) {
                    $memberpart = $mpart->member_status;
                    break;
                }
            }
        }
        $event->memberpart = $memberpart;

        $cfg = $this->di->get('config');
        $logoFile = '';
        if ($event->logo != '') {
            $logoFile = $cfg->application->uploadDir . 'img/event/' . $event->id . '/' . $event->logo;
        }

        $logo = 'http://' . $_SERVER['HTTP_HOST'] . '/upload/img/event/' . $event->id . '/' . $event->logo;
        if (!file_exists($logoFile)) {
            $logo = 'http://' . $_SERVER['HTTP_HOST'] . '/img/logo200.png';
        }
         
        if ($this -> session -> has('user_token') && $this -> session -> has('user_fb_uid')) {
        	$fb = new Extractor($this -> getDi());
        	$res = $fb -> getFQL(array('ticket' => 'SELECT ticket_uri FROM event WHERE eid = ' . $event -> fb_uid), $this -> session -> get('user_token'));

        	if (!is_null($res['MESSAGE'][0]['fql_result_set'][0]['ticket_uri'])) {
        		$event -> tickets_url = $res['MESSAGE'][0]['fql_result_set'][0]['ticket_uri'];
        	} else {
        		$event -> tickets_url = false;
        	}  
        } else {
        	if ($event -> tickets_url) {
        		$event -> tickets_url = 'https://www.facebook.com/events/' . $event -> fb_uid;
        	} else {
        		$event -> tickets_url = false;
        	}
        }
        
        $this->view->setVar('logo', $logo);
        $this->view->setVar('event', $event);
        $categories = Category::find();
        $this->view->setVar('categories', $categories->toArray());

        $this->view->setVar('link_back_to_list', true);

        $posters = $flyers = $gallery = [];
        if (isset($event->image)) {
            foreach ($event -> image as $eventImage) {
                if ($eventImage -> type == 'poster') {
                    $posters[] = $eventImage;
                } else if ($eventImage -> type == 'flyer') {
                    $flyers[] = $eventImage;
                } else if ($eventImage -> type == 'gallery') {
                    $gallery[] = $eventImage;
                } else if ($eventImage -> type == 'cover') {
                    $cover = $eventImage;
                } 
            }
        }
        $this->view->setVar('poster', isset($posters[0]) ? $posters[0] : null);
        $this->view->setVar('flyer', isset($flyers[0]) ? $flyers[0] : null);
        $this->view->setVar('cover', isset($cover) ? $cover : null);
        $this->view->setVar('gallery', $gallery);

        $eventTags = [];
        foreach ($event->tag as $Tag) {
            $eventTags[] = $Tag->name;
        }

        return array(
            'currentWindowLocation' => urlencode('http://' . $_SERVER['HTTP_HOST'] . '/' . SUri::slug($event->name) . '-' . $event->id),
            'eventMetaData' => $event,
            'eventTags' => array_unique($eventTags)
        );
    }

    /**
     * @Route("/suggest-event-category/{eventId:[0-9]+}/{categoryId:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member','guest'});
     */
    public function setEventCategoryAction($eventId, $categoryId)
    {
        $status['status'] = false;

        $existence = EventCategory::findFirst('event_id = ' . $eventId);

        if ($existence && ($existence->eventpart2->key == 'other')) {

            $result = $this->modelsManager->executeQuery('UPDATE \Objects\EventCategory SET category_id = ' . $categoryId . ' WHERE event_id = ' . $eventId);
            if ($result) {
                $status['status'] = true;
            }

        } else {
            $CategoryEvent = new EventCategory();

            if ($CategoryEvent->save(array(
                'event_id' => $eventId,
                'category_id' => $categoryId
            ))
            ) {
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
        $ret['status'] = 'ERROR';

        if ($this->session->has('member')) {
            $data = $this->request->getPost();
            $member = $this->session->get('member');

            switch ($data['answer']) {
                case 'JOIN':
                    $status = EventMember::JOIN;
                    break;
                case 'MAYBE':
                    $status = EventMember::MAYBE;
                    break;
                case 'DECLINE':
                    $status = EventMember::DECLINE;
                    break;
            }

            $eventMember = new EventMember();
            $eventMember->assign(array(
                'member_id' => $member->id,
                'event_id' => $data['event_id'],
                'member_status' => $status
            ));
            if ($eventMember->save()) {
                $ret = ['status' => 'OK',
                        'event_member_status' => $data['answer']];

                if ($status == EventMember::JOIN) {
                    $this -> counters -> increaseUserCounter('userEventsGoing');
                    $this -> counters -> setUserCounters();
                }
            }
        } else {
            $ret['error'] = 'not_logged';
        }

        echo json_encode($ret);
        //die;
    }


    /**
     * @Route("/event/edit", methods={"GET", "POST"})
     * @Route("/event/edit/{id:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function editAction()
    {
        $category = new Category();
        $this->view->setVar('categories', $category->getDefaultIdsAsString());

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

        $this->view->setVar('poster', isset($posters[0]) ? $posters[0] : null);
        $this->view->setVar('flyer', isset($flyers[0]) ? $flyers[0] : null);
        $this->view->setVar('gallery', $gallery);
    }

    public function setEditExtraRelations()
    {
        $this->editExtraRelations = array(
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
        $data = $this->request->getPost();
        $result['status'] = 'ERROR';

        if (isset($data['id']) && !empty($data['id'])) {
            $event = Event::findFirst((int)$data['id']);
            if ($event) {
                $event->event_status = 0;
                $event->deleted = 1;
                $event->save();

                $this -> counters -> decreaseUserCounter('userEventsCreated');
             
                $result = $this -> counters -> setUserCounters();
                $result['status'] = 'OK';
                $result['id'] = $data['id'];
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

        if ($this->session->has('member')) {
            $memberId = $this->session->get('memberId');
            $eventLike = EventLike::findFirst('event_id = ' . $eventId . ' AND member_id = ' . $memberId);
            if (!$eventLike) {
                $eventLike = new EventLike();
            }
            $eventLike->assign(array(
                'event_id' => $eventId,
                'member_id' => $memberId,
                'status' => $status
            ));

            if ($eventLike->save()) {
                if ($status == 1) {
                   $this -> counters -> increaseUserCounter('userEventsLiked');
                } else {
                   $this -> counters -> decreaseUserCounter('userEventsLiked');
                }

                $response = $this -> counters -> setUserCounters();
                $response['status'] = true;
                $response['member_like'] = $status;
                $response['event_id'] = $eventId;

                $this->eventsManager->fire('App.Event:afterLike', $this);
            }
        } else {
            $response['error'] = 'not_logged';
        }

        $this->sendAjax($response);
    }

    /**
     * @Route("/event/publish", methods={"GET", "POST"})
     * @Route("/event/publish/{id:[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function publishAction()
    {
        $data = $this->request->getPost();
        $result['status'] = 'ERROR';

        if (isset($data['id']) && !empty($data['id'])) {
            if ($res = $this->updateStatus($data['id'], $data['event_status'])) {
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
        $data = $this->request->getPost();
        $result['status'] = 'ERROR';

        if (isset($data['id']) && !empty($data['id'])) {
            if ($res = $this->updateStatus($data['id'], $data['event_status'])) {
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
            $event->assign(array('event_status' => $status));
            if ($event->save()) {
                $result = array('id' => $event->id,
                    'event_status' => $event->event_status);
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
        $event = $form->getFormValues();
        $loc = new Location();
        $venue = new Venue();
        $coords = array();
        $venueId = false;
        $newEvent = array();
        if (!empty($event['id'])) {
            $ev = Event::findFirst($event['id']);
        } else {
            $ev = new Event();
            if (isset($this -> session -> get('member') -> network)) {
                $newEvent['fb_creator_uid'] = $this -> session -> get('member') -> network -> account_uid;
            }
            $newEvent['member_id'] = $this -> session -> get('memberId');
        }

        // process name and descirption
        $newEvent['name'] = $event['name'];
        $newEvent['description'] = $event['description'];
        $newEvent['tickets_url'] = $event['tickets_url'];
        $newEvent['event_status'] = !is_null($event['event_status']) ? 1 : 0;
        $newEvent['event_fb_status'] = !is_null($event['event_fb_status']) ? 1 : 0;
        $newEvent['recurring'] = $event['recurring'];
        $newEvent['campaign_id'] = $event['campaign_id'];

        // process location
        if (empty($event['location_id']) && !empty($event['location_latitude']) && !empty($event['location_longitude'])) {
            // check location by coordinates
            $location = $loc->createOnChange(['latitude' => $event['location_latitude'],
                                              'longitude' => $event['location_longitude']]);
            if ($location) {
                $newEvent['location_id'] = $location->id;
                $newEvent['latitude'] = $event['location_latitude'];
                $newEvent['longitude'] = $event['location_longitude'];
            }
        }

        // location coordinates wasn't set. Try to get location from venue coordinates
        if (!empty($event['venue_latitude']) && !empty($event['venue_longitude'])) {
            if (!isset($newEvent['location_id'])) {
                $location = $loc -> createOnChange(['latitude' => $event['venue_latitude'],
                                                  'longitude' => $event['venue_longitude']]);
                if ($location) {
                    if (!isset($newEvent['location_id'])) {
                        $newEvent['location_id'] = $location->id;
                    }
                }
            }
            $newEvent['latitude'] = $event['venue_latitude'];
            $newEvent['longitude'] = $event['venue_longitude'];
        }

        // location coordinates wasn't set. Try to get location from address coordinates
        if (!empty($event['address_latitude']) && !empty($event['address_longitude'])) {
            if (!isset($newEvent['location_id'])) {
                $location = $loc->createOnChange(['latitude' => $event['address_latitude'],
                                                  'longitude' => $event['address_longitude']]);
                $newEvent['location_id'] = $location->id;
            }
            if (!isset($newEvent['latitude']) && !isset($newEvent['longitude'])) {
                $newEvent['latitude'] = $event['address_latitude'];
                $newEvent['longitude'] = $event['address_longitude'];
            }
        }

        // process venue
        if (!empty($event['venue_latitude']) && !empty($event['venue_longitude'])) {
            $venueInfo = ['latitude' => $event['venue_latitude'],
                          'longitude' => $event['venue_longitude']];
        }
        if (!empty($newEvent['location_id']) && $newEvent['location_id']) {
            $venueInfo['location_id'] = $newEvent['location_id'];
        } else {
            $venueInfo['location_id'] = '';
        }

        $venueInfo['name'] = $event['venue'];
        $venueInfo['address'] = $event['address'];

        $vn = false;
        if ($event['venue_latitude'] != '' || $event['venue_longitude'] != '') {
            $vn = $venue->createOnChange($venueInfo);
        }


        if ($vn) {
            $newEvent['venue_id'] = $vn->id;
        } else {
            $newEvent['venue_id'] = '';
        }

        // process address
        $newEvent['address'] = $event['address'];

        // process date and time
        if (!empty($event['start_date'])) {
            $newEvent['start_date'] = $event['start_date'];
        }

        if (!empty($event['end_date'])) {
            $newEvent['end_date'] = $event['end_date'];
        }

        //process images
        $logo = null;
        $poster = null;
        $flyer = null;
        foreach ($this->request->getUploadedFiles() as $file) {
            if ($file->getKey() == 'add-img-logo-upload') {
                $logo = $file;
            } else if ($file->getKey() == 'add-img-poster-upload') {
                $poster = $file;
            } else if ($file->getKey() == 'add-img-flyer-upload') {
                $flyer = $file;
            }
        }
//_U::dump($newEvent);  

        $ev->assign($newEvent);
        if ($ev->save()) {
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
                $file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $filename;
                $ev->logo = $filename;
                $ev->save();
            } else if ($ev->logo != '') {
                $file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $ev->logo;

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

            if ($newEvent['event_fb_status'] == 1 || (isset($ev->fb_uid) && $ev->fb_uid != '')) {
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
            $eSites = EventSite::find('event_id = ' . $ev->id);
            if ($eSites) {
                foreach ($eSites as $es) {
                    $es->delete();
                }
            }
            if (!empty($event['event_site'])) {
                $aSites = explode(',', $event['event_site']);
                foreach ($aSites as $key => $value) {
                    if (!empty($value)) {
                        $eSites = new EventSite();
                        $eSites->assign(array('event_id' => $ev->id,
                            'url' => $value));
                        $eSites->save();
                    }
                }
            }

            // process categories
            $eCats = EventCategory::find('event_id = ' . $ev->id);
            if ($eCats) {
                foreach ($eCats as $ec) {
                    $ec->delete();
                }
            }
            if (!empty($event['category'])) {
                $aCats = explode(',', $event['category']);
                foreach ($aCats as $key => $value) {
                    if (!empty($value)) {
                        $eCats = new EventCategory();
                        $eCats->assign(array('event_id' => $ev->id,
                            'category_id' => $value));
                        $eCats->save();
                    }
                }
            }

            // process poster and flyer
            $addEventImage = function ($image, $imageType) use ($ev) {
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
                $this -> counters -> increaseUserCounter('userEventsCreated');
            }
        }

        $this->loadRedirect();
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
     * @Acl(roles={'guest'});
     */
    /*public function importCategoriesAction()
    {
        $Parser = new \Categoryzator\Core\Parser();
        $categories = $Parser->getCategories();

        // categories
        if (!empty($categories)) {
            foreach ($categories as $categoryKey => $children) {
                $Category = new Category();

                $Category->key = strtolower($categoryKey);
                $Category->name = ucfirst($categoryKey);
                $Category->parent_id = 0;

                if ($categoryKey === 'other') {
                    $Category->is_default = 1;
                }

                $Category->save();
            }

            // tags (subcategories)
            foreach ($categories as $categoryKey => $children) {
                $parent = Category::findFirst('key = "'.$categoryKey.'"');
                if (!empty($children)) {
                    unset($children[0]);
                    foreach ($children as $key => $cat) {
                        $Tag = new \Frontend\Models\Tag();
                        $Tag->category_id = $parent->id;

                        if (is_string($cat)) {
                            $catk = strtolower(str_replace(' ', '_', $cat));
                            $Tag->key = $catk;
                            $Tag->name = ucfirst($cat);
                        } elseif (is_array($cat)) {
                            $keyk = strtolower(str_replace(' ', '_', $key));
                            $Tag->key = $keyk;
                            $Tag->name = ucfirst($key);

                            // keywords
                            $keywords = [];
                            foreach ($cat as $index => $keyword) {
                                $keywords[$index] = new \Frontend\Models\Keyword();
                                $keywords[$index]->key = $keyword;
                            }

                            $Tag->tag_keyword = $keywords;

                        }

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

            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getKey() == 'add-img-logo-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['logo'] = end($logoPieces);
                    $file->moveTo($filePath);

                } else if ($file->getKey() == 'add-img-poster-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['poster'] = end($logoPieces);
                    $file->moveTo($filePath);
                } else if ($file->getKey() == 'add-img-flyer-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();

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
        } else {
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
        $Event->category = Category::find('id = ' . (int)$post['category']);
        $Event->memberpart = null;

        $this->view->setVar('currentWindowLocation', urlencode('http://' . $_SERVER['HTTP_HOST'] . '/' . SUri::slug($Event->name) . '-' . $Event->id));
        $this->view->setVar('eventPreview', 'http://' . $_SERVER['HTTP_HOST'] . '/event/' . $Event->id . '-' . SUri::slug($Event->name));

        $this->view->setVar('event', $Event);
        $this->view->setVar('poster', $post['poster']);
        $this->view->setVar('flyer', $post['flyer']);

        $this->view->pick('event/show');
    }


    public function resetLocation($lat = null, $lng = null, $city = null)
    {
        $loc = $this->session->get('location');
        $newLocation = new Location();
        $newLocation = $newLocation->createOnChange(array('latitude' => $lat, 'longitude' => $lng));

        if ($newLocation->id != $loc->id) {
            if (!empty($city)) {
                $newLocation->city = $city;
                $newLocation->alias = $city;
            }

            $this->session->set('location', $newLocation);
            $this->session->set('lastFetchedEvent', 0);
            $loc = $this->session->get('location');
        }

        return $loc;
    }

    /**
     * @Route("/event/test-get", methods={'GET'})
     * @Route("/event/test-get/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}", methods={"GET", "POST"})
     * @Route("/event/test-get/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}/{city}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function testGetAction($lat = null, $lng = null, $city = null, $needGrab = true, $withLocation = false, $applyPersonalization = false)
    {
        $Event = new Event();
        $loc = $this->session->get('location');

        if (!empty($lat) && !empty($lng)) {
            $loc = $this->resetLocation($lat, $lng, $city);
        } else {
            $loc = $this->session->get('location');
        }

        if ($withLocation) {
            $Event->addCondition('Frontend\Models\Event.latitude BETWEEN ' . $loc->latitudeMin . ' AND ' . $loc->latitudeMax . '
        						AND Frontend\Models\Event.longitude BETWEEN ' . $loc->longitudeMin . ' AND ' . $loc->longitudeMax . '
        						AND Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"' . '
        						AND Frontend\Models\Event.start_date < "' . date('Y-m-d H:i:s', strtotime('today +3 days')) . '"');
        } else {
            $Event->addCondition('Frontend\Models\Event.start_date > "' . date('Y-m-d H:i:s', strtotime('today -1 minute')) . '"');
            $Event->addCondition('Frontend\Models\Event.start_date < "' . date('Y-m-d H:i:s', strtotime('today +3 days')) . '"');
        }
        $Event->addCondition('Frontend\Models\Event.id > ' . $this->session->get('lastFetchedEvent'));
        $Event->addCondition('Frontend\Models\Event.event_status = 1');
        
        $events = $Event->fetchEvents(Event::FETCH_ARRAY,
        							  Event::ORDER_ASC, 
        							  array(), 
        							  $applyPersonalization,
        							  array('start' => $this -> session -> get('lastFetchedEvent'), 'limit' => $this -> config -> application -> limitFetchEvents));

        if (count($events) ==  $this -> config -> application -> limitFetchEvents) {
            $this->session->set('lastFetchedEvent', (int)$this -> session -> get('lastFetchedEvent') + (int)$this -> config -> application -> limitFetchEvents);
            $res['status'] = true;
            $res['stop'] = false;
            $res['events'] = $events;
        } elseif (count($events) >= 0 && count($events) < (int)$this->config->application->limitFetchEvents) {
        	$res['stop'] = true;
        	$res['status'] = true;
        	$res['events'] = $events;
        } else {
        	$res['status'] = 'ERROR';
        	$res['message'] = 'no events';
        }
        
        if ($needGrab === false) {
			return $events;
        }
        
        $this->sendAjax($res);

        if ($this->session->has('user_token') && $this->session->has('user_fb_uid') && $this -> session -> has('memberId')) {
            $newTask = false;

            $taskSetted = \Objects\Cron::find(array('member_id = ' . $this -> session -> get('memberId') . ' and name =  "extract_facebook_events"'));
            if ($taskSetted -> count() > 0) {
                $tsk = $taskSetted -> getLast();
                if (time()-($tsk -> hash) > $this -> config -> application -> pingFbPeriod) {
                    $newTask = new \Objects\Cron();
                }
            } else {
                $newTask = new \Objects\Cron();
            }

            if ($newTask) {
                $params = ['user_token' => $this -> session -> get('user_token'),
                           'user_fb_uid' => $this -> session -> get('user_fb_uid'),
                           'member_id' => $this -> session -> get('memberId')];
                $task = ['name' => 'extract_facebook_events',
                         'parameters' => serialize($params),
                         'state' => 0,
                         'member_id' => $this -> session -> get('memberId'),
                         'hash' => time()];
                
                $newTask -> assign($task);
                $newTask -> save();
            }
        }
    }


    /**
     * @Route("/event/get-counter", methods={'GET'})
     * @Acl(roles={'guest', 'member'});
     */
    public function getCounterAction()
    {
        $res = $this -> counters -> setUserCounters();
        $this -> sendAjax($res);
    }


    /**
     * @Route("/event/delete-logo", methods={"POST"})
     * @Acl(roles={'member'});
     */
    public function deleteEventLogoAction()
    {
        $post = $this->request->getPost();

        $event = Event::findFirst('id = ' . $post['id']);

        if ($event) {
            $file = $this->config->application->uploadDir . 'img/event/' . $event->id . '/' . $event->logo;

            if (file_exists($file)) {
                unlink($file);

                $event->logo = "";
                $event->save();
            }

        }
    }

    /**
     * @Route("/event/delete-image", methods={"POST"})
     * @Acl(roles={'member'});
     */
    public function deleteEventImageAction()
    {
        $post = $this->request->getPost();

        $eventImage = EventImageModel::findFirst('id = ' . $post['id']);

        if ($eventImage) {
            $file = $this->config->application->uploadDir . 'img/event/' . $eventImage->event_id . '/' . $eventImage->type . '/' . $eventImage->image;
            if (file_exists($file)) {
                unlink($file);

                $eventImage->delete();
            }
        }
    }
}	