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
	Frontend\Models\Search\Grid\Event as EventGrid,
	Frontend\Models\Featured;

/**
 * @RouteRule(useCrud = true)
 */
class EventController extends \Core\Controllers\CrudController
{

    use \Core\Traits\TCMember;
    use \Core\Traits\Facebook;
    use \Core\Traits\Sliders;
    use \Sharding\Core\Env\Converter\Phalcon;

    protected $friendsUid 		= [];
    protected $friendsGoingUid 	= [];
    protected $userGoingUid 	= [];
    protected $userPagesUid 	= [];
    protected $pagesUid 		= [];
    protected $actualQuery 		= false;

    
    /**
     * @Route("/event/friends", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function listFriendAction()
    {
    	$eventsFriend = EventMemberFriend::find(['member_id = ' . $this -> session -> get('memberId')])->toArray();
    	if (!empty($eventsFriend)) {
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
    	$queryData = [];
    	
    	$eventsLiked = EventLike::find(['status = 1 and member_id = ' . $this -> session -> get('memberId')])->toArray();
		if (!empty($eventsLiked)) {
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
    	$queryData = [];
    	
    	$eventsJoined = EventMember::find(['member_status = 1 and member_id = ' . $this -> session -> get('memberId')])->toArray();
    	
    	if (!empty($eventsJoined)) {
    		foreach ($eventsJoined as $event) {
    			$searchEventsId[] = $event['event_id'];
    		}
    		$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
    		$queryData['searchId'] = $searchEventsId;
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
    	
    	$model = new Event();
    	$shards = $model -> getAvailableShards();
    	
		foreach ($shards as $cri) {
			$e = (new Event()) -> setShard($cri);
			/*$events = $e::find(['deleted != 1 and member_id = ' . $this -> session -> get('memberId'), 
								'order' => 'start_date ASC']); */ 
			$events = $e -> strictSqlQuery()
						 -> addQueryCondition('deleted != 1 AND member_id = ' . $this -> session -> get('memberId'))
						 -> addQueryFetchStyle('\Frontend\Models\Event')
						 -> selectRecords();

			if ($events) {
				foreach($events as $object) {
					$result[] = json_decode(json_encode($object -> toArray(), JSON_UNESCAPED_UNICODE), FALSE);
				}
			} 
		}
		
		$this -> view -> setVar('object', $result);
		$this -> view -> setVar('list', $result);
		$this -> view -> setVar('listTitle', 'Created');
		$this -> view -> pick('event/eventUserList');
		
		return array('eventListCreatorFlag' => true); 
    }
    
    
    protected function showListResults($queryData = [], $urlParams = '', $listType = 'liked', $listTitle = 'Events')
    {
		$result = [];
    	$pickFullTemplate = true;
    	$likedEvents = $unlikedEvents = [];
    	
    	if (!empty($queryData)) {
    		if ($this->session->has('memberId')) {
    			$this->fetchMemberLikes();
    			$likedEvents = $this -> view -> getVar('likedEventsIds');
    			$unlikedEvents = $this -> view -> getVar('unlikedEventsIds');
    		
    			if (!empty($unlikedEvents)) {
    				$queryData['searchNotId'] = $unlikedEvents;
    			}
    		}
    		
	    	$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
	    	$eventGrid -> setLimit(9);
	    	$eventGrid -> setSort('start_date');
	    	$eventGrid -> setSortDirection('ASC');

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
	    		$value -> cover = (new EventImageModel()) -> getCover($value);
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

    	$ev = (new Event()) -> setShardById($eventId);
    	$event = $ev::findFirst($eventId);
    	if ($event) {
	    	(new EventRating()) -> addEventRating($event);
	
	    	$event -> memberStatus = $this -> getJoinedStatus($event);
	    	$event -> likedStatus = $this -> getLikedStatus($event);
	    	
	    	if (!empty($event -> fb_uid)) {
	    		$event -> tickets_url = (new Extractor($this -> getDi())) -> getEventTicketUrl($event -> fb_uid, $event -> tickets_url);
	    	} 
	    	if (!empty($event -> eb_uid)) {
	    		$site_url = preg_replace('/<a[^>]*>((https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.#?=-]*)*\/?)<\/a>/ui', '$1', $event -> eb_url);
	    		$event -> eb_url = $site_url; 
	    	}
	    	 
	    	$images = (new EventImageModel()) -> setViewImages($event);
	    	$this->view->setVars($images);
	    	
	    	$sites = EventSite::find('event_id = "' . $event -> id . '"');
	    	$this -> view -> setVar('sites', $sites);
	    	
	        $this->view->setVar('event', $event);
	        $this->view->setVar('categories', Category::find() -> toArray());
	        $this->view->setVar('link_back_to_list', true);
	        
	        $eventTags = [];
	        if ($event->tag) {
		        foreach ($event->tag as $Tag) {
		            $eventTags[] = $Tag->name;
		        }
	        }
	
	        return array(
	            'currentWindowLocation' => urlencode('http://' . $_SERVER['HTTP_HOST'] . '/' . SUri::slug($event->name) . '-' . $event->id),
	            'eventMetaData' => $event,
	            'eventTags' => array_unique($eventTags)
	        );
    	} else {
    		$this -> view -> pick('event/eventPast');
    	}
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

            $eventMember = EventMember::findFirst(['member_id = ' . $this -> session -> get('memberId'). ' AND event_id = "' . $data['event_id']. '"']); 
            if(!$eventMember) {
            	$eventMember = new EventMember();
            }
            $eventMember->assign(array(
                'member_id' => $member->id,
                'event_id' => $data['event_id'],
                'member_status' => $status
            ));
            if ($eventMember -> save()) {
                $ret = ['status' => 'OK',
                        'event_member_status' => $data['answer']];
            }
        } else {
            $ret['error'] = 'not_logged';
        }

        echo json_encode($ret);
    }


    /**
     * @Route("/event/edit", methods={"GET", "POST"})
     * @Route("/event/edit/{id:[0-9_]+}", methods={"GET", "POST"})
     * @Acl(roles={'member'});
     */
    public function editAction($id = false)
    {
       	if ($id) {
       		$ev = new Event();
    		$ev -> setShardById($id);
    		$event = $ev::findFirst($id);
			
    		if ($event) {
	       		$event -> setExtraRelations($this -> getEditExtraRelations());
	       		$event -> getDependencyProperty();
	       		$images = (new EventImageModel()) -> setViewImages($event);
	       		$event -> site = EventSite::find(['event_id = "' . $event -> id . '"']);
	       		if (!empty($event -> tickets_url)) {
	       			$event -> tickets_url = preg_replace('/<a[^>]*>((https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.#?=-]*)*\/?)<\/a>/ui', '$1', $event -> tickets_url);
	       		} 
	       		$this -> view -> setVars($images);
	       		
	       		$this -> view -> setVar('editEvent', true);
    		} else {
    			$event = new Event();    			
    		}       		
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
        
        $this -> view -> setVar('hostName', 'http://' . $_SERVER['HTTP_HOST']);
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
                
                $grid = new \Frontend\Models\Search\Grid\EventSave(['location' => $event -> location_id], $this -> getDI(), null, ['adapter' => 'dbMaster']);
                $indexer = new \Frontend\Models\Search\Search\Indexer($grid);
                $indexer -> setDi($this -> getDI());
               	$indexer -> deleteData($event -> id);

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

        $this -> sendAjax($result);
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
            
            if ($event -> update()) {
            	$grid = new \Frontend\Models\Search\Grid\EventSave(['location' => $event -> location_id], $this -> getDI(), null, ['adapter' => 'dbMaster']);
            	$indexer = new \Frontend\Models\Search\Search\Indexer($grid);
            	$indexer -> setDi($this -> getDI());
            	if ($status == 1) {
	            	if (!$indexer->existsData($event -> id)) {
	            		$indexer->addData($event -> id);
	            	}
            	} else {
            		if ($indexer->existsData($event -> id)) {
            			$indexer->deleteData($event -> id);
            		}
            	}
            	 
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
            $eventExists = true;
        } else {
            $ev = new Event();
            if ($this -> session -> get('member') -> network) {
                $newEvent['fb_creator_uid'] = $this -> session -> get('member') -> network -> account_uid;
            }
            $newEvent['member_id'] = $this -> session -> get('memberId');
            $eventExists = false;
        }

        // process name and descirption
        $newEvent['name'] = $event['name'];
        $newEvent['deleted'] = '0';
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
            if ($file->getKey() == 'add-img-logo-upload' && $file->getName() != '') {
                $logo = $file;
            } else if ($file->getKey() == 'add-img-poster-upload' && $file->getName() != '') {
                $poster = $file;
            } else if ($file->getKey() == 'add-img-flyer-upload' && $file->getName() != '') {
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
			$this -> processFormRelatedData($ev, $event, $logo, $poster, $flyer);
			
			$grid = new \Frontend\Models\Search\Grid\EventSave(['location' => $ev -> location_id], $this -> getDI(), null, ['adapter' => 'dbMaster']);
			$indexer = new \Frontend\Models\Search\Search\Indexer($grid);
			$indexer -> setDi($this -> getDI());
			
			if (!empty($event['id'])) {
				if ($indexer->existsData($ev -> id)) {
					$indexer->updateData($ev -> id);
				} else {
					$indexer->deleteData($event['id']);					
					$indexer->addData($ev -> id);
				}
			} else {
				$indexer->addData($ev -> id);
			}
            
            //recurring
            if (isset($event['recurring']) && $event['recurring'] != 0) {
            	$eventsRecurring = [];
            	$nextStartDate = strtotime($event['start_date']);
            	$nextEndDate = strtotime($event['end_date']);
            	$finalDate = strtotime($event['recurring_end_date']);
            	
            	do {
            		$nextStartDate = strtotime(date('Y-m-d H:i:s', $nextStartDate) . ' + ' . $event['recurring'] . ' day');
            		$nextEndDate = strtotime(date('Y-m-d H:i:s', $nextEndDate) . ' + ' . $event['recurring'] . ' day');
            		
            		$nextEvent = $newEvent;
					$nextEvent['start_date'] = date('Y-m-d H:i:s', $nextStartDate);
					$nextEvent['end_date'] = date('Y-m-d H:i:s', $nextEndDate);
					
					$evRecurring = (new Event()) -> setShardByCriteria($nextEvent['location_id']);
					$evRecurring -> assign($nextEvent); 
					if ($evRecurring -> save()) {
						$this -> processFormRelatedData($evRecurring, $event, $logo, $poster, $flyer);
						
						$grid = new \Frontend\Models\Search\Grid\EventSave(['location' => $evRecurring -> location_id], $this -> getDI(), null, ['adapter' => 'dbMaster']);
						$indexer = new \Frontend\Models\Search\Search\Indexer($grid);
						$indexer -> setDi($this -> getDI());
						
						$addData = $indexer -> addData($evRecurring -> id);
					} 
            	} while($nextStartDate < $finalDate);
            }
            
            if (!empty($event['id'])) {
            	return ['id' => $ev -> id, 'type' => 'update'];
            } else {
            	return ['id' => $ev -> id, 'type' => 'new'];
            }
        }  
    }
    
    
    private function processFormRelatedData($ev, $event, $logo = null, $poster = null, $flyer = null)
    {
    	// save image
    	$file = ROOT_APP . 'public' . $this->config->application->defaultLogo;
    	
    	if (!is_null($logo)) {
    		$filename = (new EventImageModel()) -> uploadImageFile($ev->logo, $logo, $this->config->application->uploadDir . 'img/event/' . $ev->id);
    		if (file_exists($this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $filename)) {
    			$file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $filename;
    			$ev->logo = $filename;
    			$ev->update();
    		}
    	} else if ($ev->logo != '') {
    		$file = $this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $ev->logo;
    	} else {
    		$ev->logo = '';
    		$ev->update();
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
    				$eSites->assign(['event_id' => $ev->id,
    									'url' => $value]);
    				$eSites->save();
    			}
    		}
    	}
    	
    	// process categories
    	$eventCategories = (new EventCategory()) -> setShardById($ev->id);
    	$eCats = $eventCategories -> strictSqlQuery()
								   -> addQueryCondition('event_id = "' . $ev -> id . '"')
								   -> addQueryFetchStyle('\Frontend\Models\EventCategory')
								   -> selectRecords();
    	if (!empty($eCats)) {
    		foreach ($eCats as $ec) {
    			$ec -> setShardById($ev -> id);
    			$ec -> delete();
    		}
    	}
    	if (!empty($event['category'])) {
    		$aCats = explode(',', $event['category']);
    		foreach ($aCats as $key => $value) {
    			if (!empty($value)) {
    				$eCats = (new EventCategory())->setShardById($ev -> id);
    				$eCats->assign(['event_id' => $ev->id,
    								'category_id' => $value]);
    				$eCats->save();
    			}
    		}
    	}
    	
    	// process poster and flyer
    	$addEventImage = function ($image, $imageType) use ($ev) {
    		$eventImage = false;
    		$img = (new EventImageModel()) -> setShardById($ev -> id)
			    							-> strictSqlQuery()
											-> addQueryCondition('event_id = "' . $ev->id . '" AND type = "' . $imageType . '"')
											-> addQueryFetchStyle('\Frontend\Models\EventImage')
											-> selectRecords();
			if (!empty($img)) {
				$eventImage = $img[0];
			}
	
    		$filename = (new EventImageModel()) -> uploadImageFile(
    				$eventImage ? '' : $eventImage->image,
    				$image,
    				$this->config->application->uploadDir . 'img/event/' . $ev->id . '/' . $imageType);
    	
    		if ($eventImage) {
    			$eventImage -> setShardById($ev->id);
    			$eventImage->image = $filename;
    			$eventImage->update();
    		} else {
    			$eventImage = (new EventImageModel()) -> setShardById($ev -> id);
    			$eventImage->event_id = $ev->id;
    			$eventImage->image = $filename;
    			$eventImage->type = $imageType;
    			$eventImage->save();
    		}
    	};
    	
    	if (!is_null($poster)) {
    		$addEventImage($poster, 'poster');
    	}
    	
    	if (!is_null($flyer)) {
    		$addEventImage($flyer, 'flyer');
    	}
    	
    	return;
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
                    $this->view->setVar('eventPreviewLogo', $post['logo']);
                    $file->moveTo($filePath);
                    chmod($filePath, 0777);

                } else if ($file->getKey() == 'add-img-poster-upload' && $file->getName() != '') {
                    $filePath = $this->config->application->uploadDir . 'img/event/tmp/' . time() . rand(1000, 9999) . $file->getName();
                    $logoPieces = explode('/', $filePath);

                    $post['poster'] = end($logoPieces);
                    $file->moveTo($filePath);
                    chmod($filePath, 0777);
                } else if ($file->getKey() == 'add-img-flyer-upload' && $file->getName() != '') {
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
        } elseif (!empty($post['poster'])) {
       		$this->view->setVar('previewPoster', $post['poster']);
       		$this->view->setVar('eventPreviewPoster', $post['poster']);
        } 

        if (!empty($post['event_flyer'])) {
            $this->view->setVar('eventPreviewFlyerReal', $post['event_flyer']);
        } elseif (!empty($post['flyer'])) {
        	$this->view->setVar('previewFlyer', $post['flyer']);
        	$this->view->setVar('eventPreviewFlyer', $post['flyer']);
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
        //$this->view->pick('event/show');
        $this->view->pick('event/preview');
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
			//(new Cron()) -> createCreatorTask();
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