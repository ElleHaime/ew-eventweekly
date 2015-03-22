<?php

namespace Frontend\Controllers;

use Core\Utils as _U,
	Core\Utils\DateTime as _UDT,
	Frontend\Models\MemberFilter, //<---for new filters
	Frontend\Models\Tag,          //<---for new filters	
    Frontend\Models\Location,
    Frontend\Models\Venue as Venue,
    Frontend\Models\MemberNetwork,
    Frontend\Models\Category as Category,
    Frontend\Models\EventCategory as EventCategory,
    Frontend\Models\Event as Event,
    Frontend\Models\EventSite,
    Frontend\Models\EventRating,
    Frontend\Models\Cron as Cron,
    Frontend\Models\EventMember,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\EventLike,
    Objects\EventTag AS EventTagObject,
    Objects\Tag AS TagObject,
    Core\Utils\SlugUri as SUri,
    Frontend\Models\EventImage as EventImageModel,
	Thirdparty\Facebook\Extractor,
	Categoryzator\Core\Inflector,
	\Frontend\Models\Search\Grid\Event as EventGrid;

/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{

    use \Core\Traits\TCMember;
    use \Core\Traits\Facebook;
    use \Sharding\Core\Env\Converter\Phalcon;

    protected $friendsUid = array();
    protected $friendsGoingUid = array();
    protected $userGoingUid = array();
    protected $userPagesUid = array();
    protected $pagesUid = array();
    protected $actualQuery = false;

    
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
     * @Route("/list&page={[0-9]+}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function eventlistAction()
    {
    	$result = [];
    	$pickFullTemplate = true;
    	
    	$likedEvents = [];
    	if ($this->session->has('memberId')) {
    		$this->fetchMemberLikes();
    		$likedEvents = $this -> view -> getVar('likedEventsIds'); 
    	}
    	
    	$queryData = ['searchStartDate' =>  _UDT::getDefaultStartDate(),
    				  'searchEndDate' =>  _UDT::getDefaultEndDate(),
    				  'searchLocationField' => $this -> session -> get('location') -> id];
    	$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
		$eventGrid->setLimit(9);
		
		$page = $this->request->getQuery('page');
		if (empty($page)) {
			$eventGrid -> setPage(1);
		} else {
			$pickFullTemplate = false;
			$eventGrid -> setPage((int)$page);
		}
		$results = $eventGrid->getData();

		foreach($results['data'] as $key => $value) {
			if (!empty($likedEvents) && in_array($value -> id, $likedEvents)) {
				$value -> disabled = 'disabled'; 
			}
			$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
		}
		$countResults = $results['all_count'];
		
    	if ($results['all_page'] > 1) {
            $this -> view -> setVar('pagination', $results['array_pages']);
            $this -> view -> setVar('pageCurrent', $results['page_now']);
            $this -> view -> setVar('pageTotal', $results['all_page']);
        }
        
        $tagIds = '';
        $member_categories = (new MemberFilter())->getbyId();
        if (isset($member_categories['tag'])) {
        	$tagIds = implode(',', $member_categories['tag']['value']);
        }
        
		$this->view->setVar('urlParams', 'list');
		$this->view->setVar('list', $result);
		if ($pickFullTemplate) {
    		$this->view->pick('event/eventList');
		} else {
			$this->view->pick('event/eventListPart');
		}
    }
    
    
    /**
     * @Route("/event/friends", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listFriendAction()
    {
    	$eventsFriend = EventMemberFriend::find(['member_id = ' . $this -> session -> get('memberId')])->toArray();
    	if (!is_null($eventsFriend)) {
    		foreach ($eventsFriend as $event) {
    			$searchEventsId[] = $event['event_id'];
    		}
    			
    		$queryData = ['searchStartDate' => _UDT::getDefaultStartDate(),
    					  'searchId' => $searchEventsId];
    	}
    	$this -> showListResults($queryData, 'friends', 'friends', 'Friend\'s events');
    }
    
    
    /**
     * @Route("/event/liked", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listLikedAction()
    {
    	$eventsLiked = EventLike::find(['member_id = ' . $this -> session -> get('memberId')])->toArray();
   	
		if (!is_null($eventsLiked)) {
			foreach ($eventsLiked as $event) {
				$searchEventsId[] = $event['event_id']; 
			}
			
			$queryData = ['searchStartDate' => _UDT::getDefaultStartDate(),
						  'searchId' => $searchEventsId]; 
		}

		$this -> showListResults($queryData, 'liked', 'liked', 'Liked events');
    }
    
    /**
     * @Route("/event/joined", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listJoinedAction()
    {
    	$eventsJoined = EventMember::find(['member_id = ' . $this -> session -> get('memberId')])->toArray();
    	if (!is_null($eventsJoined)) {
    		foreach ($eventsJoined as $event) {
    			$searchEventsId[] = $event['event_id'];
    		}
    			
    		$queryData = ['searchStartDate' => _UDT::getDefaultStartDate(),
			    		  'searchId' => $searchEventsId];
    	}
    	
    	$this -> showListResults($queryData, 'joined', 'join', 'Where I am going');
    }
    
    
    /**
     * @Route("/event/list", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listAction()
    {
    	$result = [];
    	$queryData = ['searchMember' => (int)$this -> session -> get('memberId')];
		   	
    	$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
		$results = $eventGrid->getData();
		
		if ($results['all_count'] > 0) {
			foreach($results['data'] as $key => $value) {
				$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
            }
    		$this -> view -> setVar('object', $result);
    		$this -> view -> setVar('list', $result);
    	}
    	
    	$this -> view -> setVar('listTitle', 'Created');
    	$this -> view -> pick('event/eventUserList');
    
    	return array('eventListCreatorFlag' => true);
    }
    
    
    protected function showListResults($queryData = [], $urlParams = '', $listType = 'liked', $listTitle = 'Events')
    {
		$result = [];
    	$pickFullTemplate = true;
    	$likedEvents = [];
    	if ($this->session->has('memberId')) {
    		$this->fetchMemberLikes();
    		$likedEvents = $this -> view -> getVar('likedEventsIds');
    	}
    	
    	if (!empty($queryData)) {
	    	$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
	    	$eventGrid->setLimit(9);
	    	
	    	$page = $this->request->getQuery('page');
	    	if (empty($page)) {
	    		$eventGrid -> setPage(1);
	    	} else {
	    		$pickFullTemplate = false;
	    		$eventGrid -> setPage((int)$page);
	    	}
	    	$results = $eventGrid->getData();
   	
	    	foreach($results['data'] as $key => $value) {
	    		if (!empty($likedEvents) && in_array($value -> id, $likedEvents)) {
	    			$value -> disabled = 'disabled';
	    		}
	    		$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
	    	}
	    	$this -> view -> setVar('list', $result);
	    	
	    	$countResults = $results['all_count'];
	    	if ($results['all_page'] > 1) {
	    		$this -> view -> setVar('pagination', $results['array_pages']);
	    		$this -> view -> setVar('pageCurrent', $results['page_now']);
	    		$this -> view -> setVar('pageTotal', $results['all_page']);
	    	}
    	}

   		$this -> fetchMemberLikes();
    	$this->view->setvar('list_type', $listType);
    	$this->view->setVar('listTitle', $listTitle);
    	$this->view->setVar('urlParams', $urlParams);
    	
    	if ($pickFullTemplate) {
    		$this->view->pick('event/eventUserList');
    	} else {
    		$this->view->pick('event/eventUserListPart');
    	}
    }
    
    
    /**
     * @Route("/{slugUri}-{eventId:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function showAction($slug, $eventId)
    {
    	if (isset($_SERVER['HTTP_REFERER'])) {
			$previousUri = str_replace($_SERVER['HTTP_HOST'], '', str_replace('http://', '', $_SERVER['HTTP_REFERER']));
    	} else {
    		$previousUri = $_SERVER['HTTP_HOST'];
    	}
		$this -> view -> setVar('back_position_url_params', $previousUri);
		
    	if ($this -> session -> has('eventViewForwardedNew') && $this -> session -> get('eventViewForwardedNew') == 1) {
    		$this -> session -> set('eventViewForwardedNew', 0);
    		$this -> view -> setVar('viewModeNew', true);
    	} elseif($this -> session -> has('eventViewForwardedUp') && $this -> session -> get('eventViewForwardedUp') == 1) {
    		$this -> session -> set('eventViewForwardedUp', 0);
    		$this -> view -> setVar('viewModeUp', true);
    	}

    	$ev = new Event();
    	$ev-> setShardById($eventId);
    	
    	$event = $ev::findFirst($eventId);
    	$event -> memberpart = (new EventMember()) -> getMemberpart($ev);
    	$event -> tickets_url = (new Extractor($this -> getDi())) -> getEventTicketUrl($event -> fbUid, $event -> tickets_url); 
    	
    	(new EventRating()) -> addEventRating($event);
    	
    	$images = (new EventImageModel()) -> setViewImages($event -> id);
    	$this->view->setVars($images);
        $this->view->setVar('event', $event);
        $this->view->setVar('categories', Category::find() -> toArray());
        $this->view->setVar('link_back_to_list', true);
        $this->fetchMemberLikeForEvent($event -> id);
        
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
            if ((new EventCategory())->save(array(
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
            }
        } else {
            $ret['error'] = 'not_logged';
        }

        echo json_encode($ret);
        //die;
    }


    /**
     * @Route("/event/edit", methods={"GET", "POST"})
     * @Route("/event/edit/{id:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function editAction($id = false)
    {
    	if ($this->session->has('user_token') && $this->session->has('user_fb_uid') && $this -> session -> has('memberId')) {
    		$isSessionActive = $this -> checkFacebookExpiration();

    		if (!$isSessionActive) {
    			$this -> view -> setVar('flashMsgText', 'Your facebook authorization has expired =/ <br>Please <a href=&quot;#&quot; class=&quot;fb-login-popup&quot; onclick=&quot;return false;&quot;>re-auth via Facebook</a> to be able to publish events there');
    			$this -> view -> setVar('flashMsgType', 'warning');
    		}
    	}

       	//parent::editAction();
       	if ($id) {
       		$ev = new Event();
    		$ev -> setShardById($id);
    		$event = $ev::findFirst($id);
       		
       		$event -> setExtraRelations($this -> getEditExtraRelations());
       		$event -> getDependencyProperty();
       		$images = (new EventImageModel()) -> setViewImages($event -> id);
       		$event -> site = EventSite::find(['event_id = "' . $event -> id . '"']);
       		$this -> view -> setVars($images);
       		
       		$this -> view -> setVar('editEvent', true);       		
       	} else {
       		$event = new Event();
       	}
		$form = $this -> loadForm($event);
       	
       	$this -> view -> setVar('event', $event);
       	$this -> view -> form = $form;
       	
       	if ($this -> request -> isPost() && !$this -> dispatcher -> wasForwarded()) {
       		if ($form -> isValid($this -> request -> getPost())) {
       			$redirectOptions = $this -> processForm($form);
       			if(is_array($redirectOptions)) {
       				$this -> loadRedirect($redirectOptions);
       			} else {
       				$this -> loadRedirect();
       			}
       		}
       	}
       	
        $this -> view -> setVar('categories', (new Category()) -> getDefaultIdsAsString());

        if ($this -> dispatcher -> wasForwarded()) {
        	$this -> view -> setVar('viewMode', true); 
        }
    }
    
    
    public function setEditExtraRelations()
    {
        $this->editExtraRelations = array(
            'venue' => array('latitude', 'longitude')
        );
    }


    /**
     * @Route("/event/delete", methods={"GET", "POST"})
     * @Route("/event/delete/{id:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function deleteAction()
    {
        $data = $this->request->getPost();
        $result['status'] = 'ERROR';

        if (isset($data['id']) && !empty($data['id'])) {
        	$ev = (new Event()) -> setShardById($data['id']); 
            $event = $ev::findFirst($data['id']);
            if ($event) {
            	$event->setShardById($data['id']);
                $event->event_status = 0;
                $event->deleted = 1;
                $event->update();

                $result['status'] = 'OK';
                $result['id'] = $data['id'];
            }
        }

        $this -> sendAjax($result);
    }


    /**
     * @Route("/event/like/{eventId:[0-9_]+}/{status:[0-9]}", methods={"GET","POST"})
     * @Acl(roles={'member','guest'});
     */
    public function likeAction($eventId, $status = 0)
    {
        $response = ['status' => false];

        if ($this->session->has('member')) {
            $memberId = $this->session->get('memberId');
            $eventLike = EventLike::findFirst('event_id = "' . $eventId . '" AND member_id = ' . $memberId);
            
            if (!$eventLike) {
                $eventLike = new EventLike();
            }
            $eventLike->assign(['event_id' => $eventId,
                				'member_id' => $memberId,
                				'status' => $status]);
            
            if ($eventLike -> save()) {
            	if ($status != 1) {
            		$eventGoing = EventMember::findFirst('event_id = "' . $eventId . '" AND member_id = ' . $memberId);
            		if ($eventGoing) {
            			$eventGoing->delete();
            		}
            	}

                $response = $this -> counters -> setUserCounters();
                $response['status'] = true;
                $response['member_like'] = $status;
                $response['event_id'] = $eventId;
            } 
        } else {
            $response['error'] = 'not_logged';
        }

        $this->sendAjax($response);
    }

    /**
     * @Route("/event/publish", methods={"GET", "POST"})
     * @Route("/event/publish/{id:[0-9_]+}", methods={"GET", "POST"})
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
     * @Route("/event/unpublish/{id:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function unpublishAction()
    {
        $data = $this->request->getPost();
        
        $result['status'] = 'ERROR';
        if (isset($data['id']) && !empty($data['id'])) {
            if ($res = $this -> updateStatus($data['id'], $data['event_status'])) {
//TODO!!! delete sites, event members, send mails etc 
                $result = array_merge($res, array('status' => 'OK'));
            }
        } 

        //echo json_encode($result);
        $this -> sendAjax($result);
    }


    private function updateStatus($id, $status)
    {
    	$ev = (new Event()) -> setShardById($id);
        $event = $ev::findFirst($id);
        $result = false;

        if ($event) {
            $event -> assign(array('event_status' => $status));
            $event -> setShardById($id);
            if ($event->update()) {
                $result = array('id' => $event->id,
                    			'event_status' => $event->event_status);
             
            }
        }

        return $result;
    }


    /**
     * @Route("/event/eventsave", methods={"POST"})
     * @Acl(roles={'member'});
     */
    public function processForm($form)
    {
        $event = $form->getFormValues();

        $loc = new Location();
        $venue = new Venue();
        $venueId = false;
        $newEvent = [];
        
        if (!empty($event['id'])) {
        	$e = (new Event()) -> setShardById($event['id']);
            $ev = $e::findFirst($event['id']);
        } else {
            $ev = new Event();
            if ($this -> session -> get('member') -> network) {
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
        $newEvent['location_id'] = $event['location_id'];

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
            $vn = $venue -> createOnChange($venueInfo);
        }

        $vn ? $newEvent['venue_id'] = $vn -> id : $newEvent['venue_id'] = ''; 

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

        $ev -> assign($newEvent);
        $ev -> setShardByCriteria($newEvent['location_id']);
        if ($ev -> id) {
        	$saveEvent = $ev -> update();  
        } else {
        	$saveEvent = $ev -> save();
        }
        if ($saveEvent) {
            // create event dir if not exists
            if (!is_dir($this -> config -> application -> uploadDir . 'img/event/' . $ev -> id)) {
                mkdir($this -> config -> application -> uploadDir . 'img/event/' . $ev -> id, 0777, true);
            }

            // start prepare params for FB event
            $fbParams = array(
                'access_token' => $this->session->get('user_token'),
                'name' => $newEvent['name'],
                'description' => $newEvent['description'],
                'start_time' => date('c', strtotime($newEvent['start_date'])),
                'privacy_type' => $newEvent['event_status'] == 0 ? 'SECRET' : 'OPEN'
            );
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
                $filename = $this -> uploadImageFile($ev->logo, $logo, $this->config->application->uploadDir . 'img/event/' . $ev->id);
                $file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $filename;
                $ev->logo = $filename;
                $ev->update();
            } else if ($ev->logo != '') {
                $file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $ev->logo;
            } else {
                $ev->logo = '';
                $ev->update();
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
                    $fbEventId = $this->sendToFacebook('me/events', $fbParams);

                    if (!is_null($fbEventId)) {
                        $ev->fb_uid = $fbEventId;
                        $ev->update();
                    }
                } else {
                    $this->sendToFacebook('/' . $ev->fb_uid, $fbParams);
                }
            }

            // process site
            $eSites = EventSite::find('event_id = "' . $ev->id . '"');
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
            $eventCategories = (new EventCategory())->setShardById($ev->id);
            $eCats = $eventCategories::find('event_id = "' . $ev->id . '"');
            if ($eCats) {
                foreach ($eCats as $ec) {
                    $ec->delete();
                }
            }
            if (!empty($event['category'])) {
                $aCats = explode(',', $event['category']);
                foreach ($aCats as $key => $value) {
                    if (!empty($value)) {
                        $eCats = (new EventCategory())->setShardById($ev -> id);
                        $eCats->assign(array('event_id' => $ev->id,
                            				 'category_id' => $value));
                        $eCats->save();
                    }
                }
            }

            // process poster and flyer
            $addEventImage = function ($image, $imageType) use ($ev) {
            	$img = (new EventImageModel()) -> setShardById($ev -> id); 
                $eventPoster = $img::findFirst('event_id = "' . $ev->id . '" AND type = "' . $imageType . '"');

                $filename = $this->uploadImageFile(
                    empty($eventPoster) ? '' : $eventPoster->image,
                    $image,
                    $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $imageType
                );

                if ($eventPoster) {
                    $eventPoster->image = $filename;
                } else {
                    $eventPoster = (new EventImageModel()) -> setShardById($ev -> id);
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
        }  

        if (!empty($event['id'])) {
        	return ['id' => $ev -> id, 'type' => 'update'];
        } else {
        	return ['id' => $ev -> id, 'type' => 'new'];
        }
    }
    
    
    public function loadRedirect($params = [])
    {
    	if (!empty($params) && $params['type'] == 'update') {
    		$event = (new Event()) -> setShardById($params['id']);
    		$ev = $event::findFirst($params['id']);
    		$name = SUri::slug($ev -> name);
    		$this -> session -> set('eventViewForwardedUp', 1);
    		
    		$this -> response -> redirect('/' . $name . '-' . $ev -> id);
    		
    	} elseif(!empty($params) && $params['type'] == 'new') {
    		$event = (new Event()) -> setShardById($params['id']);
    		$ev = $event::findFirst($params['id']);
    		$name = SUri::slug($ev -> name);
    		$this -> session -> set('eventViewForwardedNew', 1);
    		
    		$this -> response -> redirect('/' . $name . '-' . $ev -> id);
    		
    	} else {
    		$this -> response -> redirect('/event/list');
    	}
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
            mkdir($path, 0777, true);
        }

        $imgExts = array('image/jpeg', 'image/png', 'image/jpg');

        $filename = '';
        if (in_array($file->getType(), $imgExts)) {
            $parts = pathinfo($file->getName());

            $filename = $parts['filename'] . '_' . md5($file->getName() . date('YmdHis')) . '.' . $parts['extension'];
            $file->moveTo($path . '/' . $filename);
            chmod($path . '/' . $filename, 0777);

            if (!is_dir($path . '/' . $oldFilename) && file_exists($path . '/' . $oldFilename)) {
                unlink($path . '/' . $oldFilename);
            }
        }

        return $filename;
    }



    /**
     * @Route("/event/preview", methods={"POST"})
     * @Acl(roles={'member'});
     */
    public function eventPreviewAction()
    {
        $post = $this->request->getPost();
//_U::dump($post);
        $uploadedFiles = $this->request->getUploadedFiles();

        if (!empty($uploadedFiles)) {

            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->getKey() == 'add-img-logo-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['logo'] = end($logoPieces);
                    $this->view->setVar('eventPreviewLogo', $post['logo']);
                    $file->moveTo($filePath);
                    chmod($filePath, 0777);

                } else if ($file->getKey() == 'add-img-poster-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();
                    $logoPieces = explode('/', $filePath);

                    $post['poster'] = end($logoPieces);
                    $file->moveTo($filePath);
                    chmod($filePath, 0777);
                } else if ($file->getKey() == 'add-img-flyer-upload') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();

                    $logoPieces = explode('/', $filePath);

                    $post['flyer'] = end($logoPieces);
                    $file->moveTo($filePath);
                    chmod($filePath, 0777);
                }
            }
        } 

        if (!empty($post['event_poster'])) {
            $this->view->setVar('eventPreviewPosterReal', $post['event_poster']);
        } else {
        	if (!empty($post['poster'])) {
        		$this->view->setVar('previewPoster', $post['poster']);
        		$this->view->setVar('eventPreviewPoster', $post['poster']);
        	}	
        } 

        if (!empty($post['event_flyer'])) {
            $this->view->setVar('eventPreviewFlyerReal', $post['event_flyer']);
        } else {
        	if (!empty($post['flyer'])) {
        		$this->view->setVar('previewFlyer', $post['flyer']);
        		$this->view->setVar('eventPreviewFlyer', $post['flyer']);
        	}	
        }

        $Event = new \stdClass();
        if (isset($post['id'])) {
            $Event->id = $post['id'];
        } else {
            $Event->id = 0;
        }
        
        $Event->name = $post['name'];
        $Event->start_date = $post['start_date'];
        $Event->end_date = $post['end_date'];
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
        $this->view->pick('event/show');
    }

    

    /**
     * @Route("/event/test-get", methods={'GET'})
     * @Route("/event/test-get/{page:[0-9]+}", methods={"GET"})
     * @Route("/event/test-get/{page:[0-9]+}/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}", methods={"GET"})
     * @Route("/event/test-get/{page:[0-9]+}/{lat:[0-9\.-]+}/{lng:[0-9\.-]+}/{city}", methods={"GET"})
     * @Acl(roles={'guest', 'member'});
     */
    public function testGetAction($page = 1, $lat = null, $lng = null, $city = null, $needGrab = true, $withLocation = true, $applyPersonalization = false)
    {
    	$queryData = [];

    	if (!empty($lat) && !empty($lng) && !empty($city)) {
            $location = (new Location()) -> resetLocation($lat, $lng, $city);
        } else {
            $location = $this -> session -> get('location');
        }
      
    	if ($withLocation) {
    		$queryData['searchLocationField'] = $location -> id;
    	}
    	$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
    	$queryData['searchEndDate'] = _UDT::getDefaultEndDate();
	
    	$eventGrid = new EventGrid($queryData, $this -> getDi(), null, ['adapter' => 'dbMaster']);
		$eventGrid -> setPage($page);
		$results = $eventGrid -> getData();

		if ($results['all_count'] > 0) {
			foreach($results['data'] as $id => $event) {
				$eComposed = (array)$event;

				if (isset($event -> logo) && file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event -> logo)) {
					$eComposed['logo'] = '/upload/img/event/' . $event -> id . '/' . $event -> logo;
				} else {
					$eComposed['logo'] = $this -> config -> application -> defaultLogo;
				}
                $eComposed['slugUri'] = \Core\Utils\SlugUri::slug($event -> name). '-' . $event -> id;
                
                $result[] = $eComposed;
			}
			
			$res = ['events' => $result,
					'status' => true];

			if ($results['page_now'] < $results['all_page']) {
				$res['stop'] = false;
				$res['nextPage'] = $results['array_pages']['next'];
			} else {
				$res['stop'] = true;
			}
        } else {
			$res = ['status' => 'ERROR',
					'message' => 'no events'];
        }

		if ($needGrab === false) {
			return $events;
        } else {
        	$this -> sendAjax($res);
			(new Cron()) -> createUserTask();
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