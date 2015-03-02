<?php

namespace Frontend\Controllers;

use Frontend\Models\MemberFilter, //<---for new filters
    Frontend\Models\Tag,          //<---for new filters
    Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\MemberFilter,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Core\Utils as _U,
    Frontend\Models\Cron as Cron;

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
		(new Cron()) -> createUserTask();
		    	
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

        // retrieve data from POST
        if (empty($postData)) {
            $postData = $this->request->getPost();
        }

        // retrieve data from GET
        if (empty($postData)) {
            $postData = $this->session->get('userSearch');
        }

/*
**********************
* =new --------------------------------
**********************
*/
//var_dump($postData);die;
//-------------------------------------


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
        
        $pageTitle = 'Search results: ';
        $queryData = [];
        
        // if income data not empty
        if (!empty($postData)) {
        	$this -> view -> setVar('userSearch', $postData);

            // add search condition by title
            if ($elemExists('searchTitle')) {
                $queryData['searchTtitle'] = $postData['searchTitle'];
                $pageTitle .= 'by title - "'.$postData['searchTitle'].'" | ';
            }

            // add search condition by location
        	// if no location specify - set from user location
        	if ($elemExists('searchLocationLatMin', false) || $elemExists('searchLocationLatMax', false) || $elemExists('searchLocationLngMin', false) || $elemExists('searchLocationLngMax', false)) 
        	{
        		if ($elemExists('searchTitle', false)) {
            		$queryData['searchLocationField'] = $this -> session -> get('location') -> id;
	        	}
            	
        	} else {
        		
                if (($elemExists('searchLocationField') && $postData['searchLocationField'] != '') ||
                    ($elemExists('searchLocationField', false) && $elemExists('searchCategoriesType') && 
                        $postData['searchCategoriesType'] == 'private' && $elemExists('searchTitle', false)))
                {
                    $lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
                    $lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;

                    $loc = new Location();
                    $newLocation = $loc -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));

                    if ($newLocation) {
                    	$queryData['searchLocationField'] = $newLocation -> id;
                    } 

                    $this->session->set('location', $newLocation);
                    $this->cookies->get('lastLat')->delete();
                    $this->cookies->get('lastLng')->delete();

                    $pageTitle .= 'by location - "'.$newLocation->alias.'" | ';
                }
            } 

            // add search condition by dates
            if ($elemExists('searchStartDate') && $elemExists('searchEndDate', false)) {
                $queryData['searchStartDate'] = $postData['searchStartDate'];
                
                $pageTitle .= 'from "'.$postData['searchStartDate'].'"  and later | ';

            } elseif ($elemExists('searchStartDate', false) && $elemExists('searchEndDate')) {
            	$queryData['searchEndDate'] = $postData['searchEndDate'];
            	
            	$pageTitle .= 'now and till "'.$postData['searchEndDate'].'" | ';
            	 
            } elseif($elemExists('searchStartDate') && $elemExists('searchEndDate')) {
				$queryData['searchStartDate'] = $postData['searchStartDate'];
				$queryData['searchEndDate'] = $postData['searchEndDate'];
				
                $pageTitle .= 'to "'.$postData['searchEndDate'].'" | ';
                $pageTitle .= 'from "'.$postData['searchStartDate'].'" | ';
                
            } else {
            	if ($elemExists('searchTitle', false) && !$elemExists('searchCategory')) {
            		$startDate = date('Y-m-d H:i:s', strtotime('today -1 minute'));
            		$endDate = date('Y-m-d H:i:s', strtotime('today +3 days'));
            		
            		$queryData['searchStartDate'] = $startDate;
					$queryData['searchEndDate'] = $endDate;
            		
            		$pageTitle .= 'now and till "' . date('Y-m-d', strtotime('+3 days midnight')) . '" | ';
            	} 
            } 
/*
<<<<<<< local
*/
			if ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'global') {
				$queryData['searchCategory'] = $postData['searchCategory'];
						
            } elseif ($postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
            	if ($elemExists('searchCategory')) {
					// get personalization, combine with selected categories and apply
					$filters = (new MemberFilter) -> compareById($this -> session -> get('memberId'), $postData['searchCategoriesType']);
            	} else {
	            	// get personalization and apply
					$filters = (new MemberFilter) -> getbyId($this -> session -> get('memberId'));
            	}
            	 
            	if ($filters) {
					if (!empty($filters['category']['value'])) {
						$queryData['searchCategory'] = $filters['category']['value']; 
					}
					if (!empty($filters['tag']['value'])) {
						$queryData['searchTag'] = $filters['tag']['value']; 
					}
				}
            } 

			$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);

			// search type
            if ($elemExists('searchType')) {
                if ($postData['searchType'] == 'in_map') {
                	$eventGrid->setLimit(100);
					$results = $eventGrid->getData();
/*					
=======
            // search type
            if ($elemExists('searchTypeResult')) {
                if (strtolower($postData['searchTypeResult']) == 'map') {
>>>>>>> other */

                    foreach($results['data'] as $id => $event) {
                    	$result[$event -> id] = (array)$event;

/*
<<<<<<< local
*/
                    	if (isset($event -> logo) && file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event -> logo)) {
                    		$result[$event -> id]['logo'] = '/upload/img/event/' . $event -> id . '/' . $event -> logo;
                    	} else {
                    		$result[$event -> id]['logo'] = $this -> config -> application -> defaultLogo;
                    	}
                    	$result[$event -> id]['slugUri'] = \Core\Utils\SlugUri::slug($event -> name). '-' . $event -> id;
/*=======
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
>>>>>>> other
*/
                    }

/*<<<<<<< local
=======
                    foreach($result as $id => $event) {
                    	if (file_exists(ROOT_APP . 'public/upload/img/event/' . $event['id'] . '/' . $event['logo'])) {
                    		$result[$id]['logo'] = '/upload/img/event/' . $event['id'] . '/' . $event['logo'];
                    	} else {
                    		$result[$id]['logo'] = $this -> config -> application -> defaultLogo;
                    	}
                    }
                    
                    
                    $countResults = count($result);
>>>>>>> other */
                    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
                } else {
                    $page = $this->request->getQuery('page');
                    if (empty($page)) {
                    	$eventGrid->setPage(1);
                    } else {
                    	$eventGrid->setPage($page);
                    }
                    $results = $eventGrid->getData();

/*                    
<<<<<<< local
*/
                    foreach($results['data'] as $key => $value) {
                    	$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
/*=======
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

                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 9],
                        								   false, [], false, false, false, true, true, $needTags);

                    } elseif ($elemExists('searchCategory') && $postData['searchCategoriesType'] == 'private' && $this->session->has('memberId')) {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 9], true, [],
                        								   false, false, false, true, true, $needTags, $postData['searchCategory']);
                    } else {
                        $fetchedData = $Event->fetchEvents(Event::FETCH_OBJECT, Event::ORDER_DESC, ['page' => $page, 'limit' => 9], false, [],
                        								   false, false, false, true, true, $needTags);
>>>>>>> other*/
                    }
/*<<<<<<< local
=======
                  
                    $result = $fetchedData->items;
>>>>>>> other*/
                    
	                if ($results['all_page'] > 1) {
			            $this -> view -> setVar('pagination', $results['array_pages']);
			            $this -> view -> setVar('pageCurrent', $results['page_now']);
			            $this -> view -> setVar('pageTotal', $results['all_page']);
			        }
                }
            }
            $countResults = $results['all_count'];
        }
        

        if ($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'global') {
            $this->session->set('userSearch', $postData);
        }

        $this->view->setVar('list', $result);
//<<<<<<< local
        $this->view->setVar('eventsTotal', $countResults);
        
        
/*=======
        $this->view->setVar('eventsTotal', $countResults); //echo $countResults;die;
        if (isset($fetchedData)) {
            $this->view->setVar('pagination', $fetchedData);
        }

>>>>>>> other*/
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

        
        /*
        **********************
        * =tagids, copied from MemberController listAction to acces user defined tags
        **********************
        */
        if ($this -> session -> has('passwordChanged') && $this -> session -> get('passwordChanged') === true) {
            $this -> session -> set('passwordChanged', false);
            $this -> view -> setVar('passwordChanged', true);
        } 
        
        $member = '\Frontend\Models\Member';
        //var_dump($member);
         $list = $member::findFirst($this -> session -> get('memberId'));
        // if (!$list -> location) {
        //     $list -> location = $this -> session -> get('location');
        // }
        //$memberForm = new MemberForm($list);
        
        if ($this -> session -> has('eventsTotal')) {
            $this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
        }
        $MemberFilter = new MemberFilter();
        $member_categories = $MemberFilter->getbyId($list->id);

        $tagIds = '';
        if ( isset($member_categories['tag']['value']) ) {
            $tagIds = implode(',', $member_categories['tag']['value']);
            $tagIds = '0,' . $tagIds . ',0';
        }

        $this->view->setVars(array(
                'member', $list,
                'categories' => Category::find()->toArray(),
                'tags' => Tag::find()->toArray(),
                'tagIds' => $tagIds,
                'member_categories' => $member_categories
            ));
        //var_dump($tagIds); die();
        /*
        **********************
        * =tagids
        **********************
        */
        
        if (strtolower($postData['searchTypeResult']) == 'map') {
        	$this->view->setVar('link_to_list', true);
        	$this->view->setVar('searchResult', true);
        	$this->view->setVar('searchResultMap', true);
//<<<<<<< local
            //$this->view->pick('event/mapEvent');
            $this->view->pick('event/map');
        } else {
        	$this->view->setVar('searchResultList', true);
            $this->view->pick('event/eventList');
/*=======
            $this->view->pick('event/mapEvent');
        } else {  
            if ($page >1 ) {
                $this->view->setVar('searchResultList', true);
                $this->view->pick('event/eventListPart');
            }
            else {
                $this->view->setVar('searchResultList', true);
                $this->view->setVar('totalPagesJs', $fetchedData -> total_pages);
                //echo $fetchedData -> total_pages;die;
                $this->view->pick('event/eventList');
            }
>>>>>>> other*/
        }
    }

}

