<?php

namespace Frontend\Controllers;

use Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Core\Utils as _U;

/**
 * @RoutePrefix('/')
 */
class SearchController extends \Core\Controller
{
    use \Core\Traits\TCMember;

    /**
     * @Route("/search", methods={"GET", "POST"})
     * @Route("/search/list", methods={"GET", "POST"})
     * @Route("/search/map", methods={"GET", "POST"})
     * @Acl(roles={'guest', 'member'});
     */
    public function searchAction()
    {
    	if ($this->session->has('user_token') && $this->session->has('user_fb_uid') && $this -> session -> has('memberId')) {
            $newTask = null;
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
    	
        $categories = Category::find();
        $categories = $categories->toArray();
        $this->view->setVar('categories', $categories);

        $form = new SearchForm();
        $this -> view -> form = $form;

        $result = array();
        $countResults = 0;
        $Event = new Event();
        $needTags = false;
        $postData = $this->request->getQuery();
//_U::dump($postData);
        // retrieve data from POST
        if (empty($postData)) {
            $postData = $this->request->getPost();
        }

        // retrieve data from GET
        if (empty($postData)) {
            $postData = $this->session->get('userSearch');
        }

        // delete url url and page params from income data
        unset($postData['_url']);
        if (isset($postData['page'])) {
            unset($postData['page']);
        }

        // check for existence in income data function
        $elemExists = function($elem, $empty = true) use (&$postData) {
            if (array_key_exists($elem, $postData) && !is_array($postData[$elem])) {
                $postData[$elem] = trim(strip_tags($postData[$elem]));
            }

            if ($empty === true) {
                $answer = (array_key_exists($elem, $postData) && !empty($postData[$elem]));
            }else {
                $answer = (array_key_exists($elem, $postData) && empty($postData[$elem]));
            }
            return $answer;
        };

        // if no location specify - set from user location
        if ($elemExists('searchLocationLatMin', false) || $elemExists('searchLocationLatMax', false) || $elemExists('searchLocationLngMin', false) || $elemExists('searchLocationLngMax', false)) {
            $location = $this->session->get('location');
            
            $postData['searchLocationLatMin'] = $location->latitudeMin;
            $postData['searchLocationLatMax'] = $location->latitudeMax;
            $postData['searchLocationLngMin'] = $location->longitudeMin;
            $postData['searchLocationLngMax'] = $location->longitudeMax;
        }

        $pageTitle = 'Search results: ';

        // if income data not empty
        if (!empty($postData)) {
            $this->view->setVar('userSearch', $postData);

            // add search condition by title
            if ($elemExists('searchTitle')) {
                $Event->addCondition('Frontend\Models\Event.name LIKE "%'.$postData['searchTitle'].'%"');

                $pageTitle .= 'by title - "'.$postData['searchTitle'].'" | ';
            }

            // add search condition by location
            if ($elemExists('searchLocationLatCurrent') && $elemExists('searchLocationLngCurrent')) {
            	$Event->addCondition('Frontend\Models\Event.latitude = '.$postData['searchLocationLatCurrent'].' 
            			AND Frontend\Models\Event.longitude = '.$postData['searchLocationLngCurrent']);
            	
            } elseif ($elemExists('searchLocationLatMin') && $elemExists('searchLocationLatMax') 
            	&& $elemExists('searchLocationLngMin') && $elemExists('searchLocationLngMax')) {
            	
                if (($elemExists('searchLocationField') && $postData['searchLocationField'] != '') ||
                    ($elemExists('searchLocationField', false) && $elemExists('searchCategoriesType') && 
                        $postData['searchCategoriesType'] == 'private' && $elemExists('searchTitle', false)))
                {
//_U::dump($postData);                	
                    $Event->addCondition('Frontend\Models\Event.latitude BETWEEN '.$postData['searchLocationLatMin'].' 
                    		AND '.$postData['searchLocationLatMax'].' AND Frontend\Models\Event.longitude BETWEEN '.$postData['searchLocationLngMin'].' 
                    		AND '.$postData['searchLocationLngMax']);

                    $lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
                    $lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;

                    $loc = new Location();
                    $newLocation = $loc -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

                    $this->session->set('location', $newLocation);

                    $this->cookies->get('lastLat')->delete();
                    $this->cookies->get('lastLng')->delete();

                    $pageTitle .= 'by location - "'.$newLocation->alias.'" | ';
                }
            } 

            // add search condition by dates
            if ($elemExists('searchStartDate') && $elemExists('searchEndDate', false)) {
                $Event->addCondition('((Frontend\Models\Event.start_date <= "'.$postData['searchStartDate'].' 00:00:00" AND Frontend\Models\Event.end_date >= "'.$postData['searchStartDate'].' 23:59:59")');
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('Frontend\Models\Event.start_date >= "'.$postData['searchStartDate'].' 00:00:00")', Event::CONDITION_SIMPLE);

                $pageTitle .= 'from "'.$postData['searchStartDate'].'"  and later | ';

            } elseif ($elemExists('searchStartDate', false) && $elemExists('searchEndDate')) {
            	$Event->addCondition('(Frontend\Models\Event.end_date BETWEEN "' . date('Y-m-d H:m:i', time()). '" AND "'.$postData['searchEndDate'].' 23:59:59")');
            	
            	$pageTitle .= 'now and till "'.$postData['searchEndDate'].'" | ';
            	 
            } elseif($elemExists('searchStartDate') && $elemExists('searchEndDate')) {
                $Event->addCondition('((Frontend\Models\Event.start_date BETWEEN "'.$postData['searchStartDate'].' 00:00:00" AND "'.$postData['searchEndDate'].' 23:59:59")');
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('(Frontend\Models\Event.end_date BETWEEN "'.$postData['searchStartDate'].' 00:00:00" AND "'.$postData['searchEndDate'].' 23:59:59")', Event::CONDITION_SIMPLE);
                $Event->addCondition('OR', Event::CONDITION_SIMPLE);
                $Event->addCondition('(Frontend\Models\Event.start_date <= "'.$postData['searchStartDate'].' 00:00:00" AND Frontend\Models\Event.end_date >= "'.$postData['searchEndDate'].' 23:59:59"))', Event::CONDITION_SIMPLE);

                $pageTitle .= 'from "'.$postData['searchStartDate'].'" | ';
                $pageTitle .= 'to "'.$postData['searchEndDate'].'" | ';
                
            } else {
            	if ($elemExists('searchTitle', false) && !$elemExists('searchCategory')) {
            		$startDate = date('Y-m-d H:i:s', strtotime('today -1 minute'));
            		$endDate = date('Y-m-d H:i:s', strtotime('today +3 days'));
            		
            		$Event->addCondition('((Frontend\Models\Event.start_date BETWEEN "' . $startDate .'" AND "'. $endDate .'")');
            		$Event->addCondition('OR', Event::CONDITION_SIMPLE);
            		$Event->addCondition('(Frontend\Models\Event.end_date BETWEEN "'.$startDate .'" AND "'.$endDate .'")', Event::CONDITION_SIMPLE);
            		$Event->addCondition('OR', Event::CONDITION_SIMPLE);
            		$Event->addCondition('(Frontend\Models\Event.start_date <= "'.$startDate .'" AND Frontend\Models\Event.end_date >= "'.$endDate .'"))', Event::CONDITION_SIMPLE);
            		
            		$pageTitle .= 'now and till "' . date('Y-m-d', strtotime('+3 days midnight')) . '" | ';
            	}
            }
            
            // set order by start date
            $Event->addOrder('Frontend\Models\Event.start_date ASC');

            // search type
            if ($elemExists('searchType')) {
                if ($postData['searchType'] == 'in_map') {

                	if ($elemExists('searchTag')) {
						$Event->addCondition('Frontend\Models\EventTag.tag_id IN (33,34,67)');
						$needTags = true;
					}

                    if ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'global') {
                        $Event->addCondition('Frontend\Models\EventCategory.category_id IN ('.implode(',', $postData['searchCategory']).')');

                        $pageTitle .= 'by categories - ';

                        foreach ($categories as $node) {
                            if (in_array($node['id'], $postData['searchCategory'])) {
                                $pageTitle .= ' '.$node['name'];
                            }
                        }

                        if (count($postData['searchCategory']) == 1) {
                            $this->view->setVar('primaryCategory', $postData['searchCategory'][0]);
                        }

                        $result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_DESC, [], false, [],
                                                           false, false, false, false, false, $needTags);
                    } elseif ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                        $result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_DESC, [], true, [],
                        								   false, false, false, false, false, $needTags, $postData['searchCategory']);
                    } elseif (!$elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                    	$result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_DESC, [], true, [],
                    										false, false, false, false, false, $needTags, []);
                	} else {
                        $result = $Event->fetchEvents(Event::FETCH_ARRAY, Event::ORDER_ASC, [], false, [],
                        								   false, false, false, false, false, $needTags);
                    }

                    foreach($result as $id => $event) {
                    	if (file_exists(ROOT_APP . 'public/upload/img/event/' . $event['id'] . '/' . $event['logo'])) {
                    		$result[$id]['logo'] = '/upload/img/event/' . $event['id'] . '/' . $event['logo'];
                    	} else {
                    		$result[$id]['logo'] = $this -> config -> application -> defaultLogo;
                    	}
                    }
                    
                    
                    $countResults = count($result);
                    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
                } else {
                    $page = $this->request->getQuery('page');
                    if (empty($page)) {
                        $page = 1;
                    }

                    if ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'global') {
                        $Event->addCondition('Frontend\Models\EventCategory.category_id IN ('.implode(',', $postData['searchCategory']).')');

                        $pageTitle .= 'by categories - ';

                        foreach ($categories as $node) {
                            if (in_array($node['id'], $postData['searchCategory'])) {
                                $pageTitle .= ' '.$node['name'];
                            }
                        }

                        if (count($postData['searchCategory']) == 1) {
                            $this->view->setVar('primaryCategory', $postData['searchCategory'][0]);
                        }

                        if ($elemExists('searchTag')) {
							$Event->addCondition('Frontend\Models\EventTag.tag_id IN (34,33,67)');
							$needTags = true;
						}

                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10],
                        								   false, [], false, false, false, true, true, $needTags);

                    } elseif ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10], true, [],
                        								   false, false, false, true, true, $needTags, $postData['searchCategory']);
                    } else {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 10], false, [],
                        								   false, false, false, true, true, $needTags);
                    }
                    $result = $fetchedData->items;
                    
                    unset($fetchedData->items);
                    $countResults = $fetchedData->total_items;
                }
            }

        }

        if ($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'global') {
            $this->session->set('userSearch', $postData);
        }

        $this->view->setVar('list', $result);
        $this->view->setVar('eventsTotal', $countResults);
        if (isset($fetchedData)) {
            $this->view->setVar('pagination', $fetchedData);
        }
        
        if ($elemExists('searchLocationLatCurrent') && $elemExists('searchLocationLngCurrent')) {
        	if (isset($fetchedData) && ($fetchedData -> current == $fetchedData -> total_pages)) {
	        	unset($postData['searchLocationLatCurrent']);
	        	unset($postData['searchLocationLngCurrent']);
        	}
        }

        if ($this->session->has('memberId')) {
            $this->fetchMemberLikes();
        }

        $this->view->setVar('listTitle', $pageTitle);
     
        $urlParams = http_build_query($postData);
        $urlParamsPaginate = $urlParams;
        
        if ($postData['searchType'] == 'in_map') {
        	$urlParams = str_replace(['in_map'], ['in_list'], $urlParams); 
        } else {
        	$urlParamsPaginate = $urlParams;
        	$this->view->setVar('urlParamsPaginate', $urlParamsPaginate);
        	$urlParams = str_replace(['in_list'], ['in_map'], $urlParams);
        }
        $this->view->setVar('urlParams', $urlParams);
        
        if ($postData['searchType'] == 'in_map') {
        	$this->view->setVar('link_to_list', true);
        	$this->view->setVar('searchResult', true);
        	$this->view->setVar('searchResultMap', true);
            $this->view->pick('event/mapEvent');
        } else {
        	$this->view->setVar('searchResultList', true);
            $this->view->pick('event/eventList');
        }
    }

}

