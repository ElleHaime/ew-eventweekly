<?php

namespace Frontend\Controllers;

use Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\MemberFilter,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Core\Utils as _U,
    Objects\Cron as Cron;

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
            	if ($elemExists('searchTitle', false) && !$elemExists('searchCategory') && !elemExists('searchTitle')) {
            		$startDate = date('Y-m-d H:i:s', strtotime('today -1 minute'));
            		$endDate = date('Y-m-d H:i:s', strtotime('today +3 days'));
            		
            		$queryData['searchStartDate'] = $startDate;
					$queryData['searchEndDate'] = $endDate;
            		
            		$pageTitle .= 'now and till "' . date('Y-m-d', strtotime('+3 days midnight')) . '" | ';
            	} 
            } 

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
					$results = $eventGrid->getData();
			
                    foreach($results['data'] as $id => $event) {
                    	$result[$event -> id] = (array)$event;

                    	if (isset($event -> logo) && file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event -> logo)) {
                    		$result[$event -> id]['logo'] = '/upload/img/event/' . $event -> id . '/' . $event -> logo;
                    	} else {
                    		$result[$event -> id]['logo'] = $this -> config -> application -> defaultLogo;
                    	}
                    	$result[$event -> id]['slugUri'] = \Core\Utils\SlugUri::slug($event -> name). '-' . $event -> id;
                    }

                    $result = json_encode($result, JSON_UNESCAPED_UNICODE);
                } else {
                    $page = $this->request->getQuery('page');
                    if (empty($page)) {
                    	$eventGrid->setPage(1);
                    } else {
                    	$eventGrid->setPage($page);
                    }
                    $results = $eventGrid->getData();

                    foreach($results['data'] as $key => $value) {
                    	$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
                    }
                    
	                if ($results['all_page'] > 1) {
			            $this -> view -> setVar('pagination', $results['array_pages']);
			            $this -> view -> setVar('pageCurrent', $results['page_now']);
			            $this -> view -> setVar('pageTotal', $results['all_page']);
			        }
                }
            }
            $countResults = $results['all_count'];
        }
        
//_U::dump($results);
        if ($elemExists('searchCategoriesType') && $postData['searchCategoriesType'] == 'global') {
            $this->session->set('userSearch', $postData);
        }

        $this->view->setVar('list', $result);
        $this->view->setVar('eventsTotal', $countResults);
        
        
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
            //$this->view->pick('event/mapEvent');
            $this->view->pick('event/map');
        } else {
        	$this->view->setVar('searchResultList', true);
            $this->view->pick('event/eventList');
        }
    }

}

