<?php

namespace Frontend\Controllers;

use Frontend\Models\MemberFilter, //<---for new filters
    Frontend\Models\Tag,          //<---for new filters
    Frontend\Form\SearchForm,
    Frontend\Models\Event as EventModel,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Core\Utils as _U,
    Core\Utils\DateTime as _UDT,
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
		//(new Cron()) -> createCreatorTask();
		    	
        $form = new SearchForm();
        $this -> view -> form = $form;

        $result = array();
        $countResults = 0;
        $likedEvents = $unlikedEvents = [];

        $postData = $this->request->getQuery();
        // retrieve data from POST
        if (empty($postData)) {
            $postData = $this->request->getPost();
        }
        // retrieve data from GET
        if (empty($postData)) {
            $postData = $this->session->get('userSearch');
        }

//_U::dump($this -> view -> getVar('userFilters'));        
_U::dump($postData, true);

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
                $queryData['searchTitle'] = $postData['searchTitle'];
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
        		
                if (($elemExists('searchLocationField') && $postData['searchLocationField'] != '') && $elemExists('searchTitle', false))
                {
                    $lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
                    $lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;

                    $newLocation = (new Location()) -> createOnChange(array('latitude' => $lat, 'longitude' => $lng));
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
            if ($elemExists('searchStartDate')) {
                $startDate = date('Y-m-d H:i:s', strtotime($postData['searchStartDate']));
                $queryData['searchStartDate'] = $startDate;
                $queryData['searchEndDate'] = _UDT::getDefaultEndDate();
                
                $pageTitle .= 'from "'.$postData['searchStartDate'].'"  and later | ';
            }  else {
            	$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
            	
            	if ($elemExists('searchTitle', false)) {
            		$queryData['searchEndDate'] = _UDT::getDefaultEndDate();
            	} 
			}
	
			if ($elemExists('searchTags') || $elemExists('searchCategories')) {
				if ($postData['personalPresetActive'] != 1) {
					if ($elemExists('searchCategories')) {
						$userSearchFilters['category'] = $postData['searchCategories'];
						$queryData['searchCategory'] = array_keys($postData['searchCategories']);
					} else {
						$userSearchFilters['category'] = [];
					}
					if ($elemExists('searchTags')) {
						$userSearchFilters['tag'] = $postData['searchTags'];
						$queryData['searchTag'] = array_keys($postData['searchTags']);
					} else {
						$userSearchFilters['tag'] = [];
					}
					$this -> session -> set('userSearchFilters', $userSearchFilters);
					$this -> filters -> loadUserFilters(false);
				} else {
					$searchTags = $this -> filters -> getActiveTags();
					$searchCategories = $this -> filters -> getActiveCategories();
					if (!empty($searchTags)) {
						$queryData['searchTag'] = $searchTags;
					}
					if (!empty($searchCategories)) {
						$queryData['searchCategory'] = $searchCategories;
					}
				}
            } else {
            	$filterTags = $this -> filters -> getActiveTags();
            	if (!empty($filterTags)) {
            		$queryData['searchTag'] = $filterTags;
            	} 
            	$filterCategories = $this -> filters -> getActiveCategories();
            	if (!empty($filterCategories)) {
            		$queryData['searchCategory'] = $filterCategories;
            	}
            }
            
	        if ($this->session->has('memberId')) {
	    		$this->fetchMemberLikes();
	    		$likedEvents = $this -> view -> getVar('likedEventsIds');
	    		$unlikedEvents = $this -> view -> getVar('unlikedEventsIds');
	    		
	    		if (!empty($unlikedEvents)) {
	    			$queryData['searchNotId'] = $unlikedEvents;
	    		}
	    	}
            
//_U::dump($queryData);
			$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);
			
			// search type
            if ($elemExists('searchTypeResult')) {
            	$page = $this -> request -> getQuery('page');
            	empty($page) ?	$eventGrid->setPage(1) : $eventGrid->setPage($page);
            	 
                if ($postData['searchTypeResult'] == 'Map') {
                	$eventGrid -> setLimit(50);
					$results = $eventGrid->getData();

                    foreach($results['data'] as $id => $event) {
                    	$result[$event -> id] = (array)$event;
                    	if (!empty($likedEvents) && in_array($event -> id, $likedEvents)) {
                    		$result[$event -> id]['disabled'] = 'disabled';
                    	}

                    	if (isset($event -> logo) && file_exists(ROOT_APP . 'public/upload/img/event/' . $event -> id . '/' . $event -> logo)) {
                    		$result[$event -> id]['logo'] = '/upload/img/event/' . $event -> id . '/' . $event -> logo;
                    	} else {
                    		$result[$event -> id]['logo'] = $this -> config -> application -> defaultLogo;
                    	}
                    	$result[$event -> id]['slugUri'] = \Core\Utils\SlugUri::slug($event -> name). '-' . $event -> id;
                    }
                   	$result = json_encode($result, JSON_UNESCAPED_UNICODE);

                    
                } else {
                    $eventGrid -> setLimit(9);
	    			$eventGrid -> setSort('start_date');
	    			$eventGrid -> setSortDirection('ASC');
                    $results = $eventGrid->getData();

                    foreach($results['data'] as $key => $value) {
						if (!empty($likedEvents) && in_array($value -> id, $likedEvents)) {
							$value -> disabled = 'disabled';
						}
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
        
        if ($postData['searchTypeResult'] == 'Map') {
        	$urlParams = str_replace(['in_map'], ['in_list'], $urlParams); 
        } else {
        	$urlParamsPaginate = $urlParams;
        	$this->view->setVar('urlParamsPaginate', $urlParamsPaginate);
        	$urlParams = str_replace(['in_list'], ['in_map'], $urlParams);
        }
        $this->view->setVar('urlParams', $urlParams);
        
        $member = '\Frontend\Models\Member';
		$list = $member::findFirst($this -> session -> get('memberId'));
        
        if ($this -> session -> has('eventsTotal')) {
            $this -> view -> setVar('eventsTotal', $this -> session -> get('eventsTotal'));
        }
        
        $tagIds = '';        
        $member_categories = (new MemberFilter())->getbyId();
        if (isset($member_categories['tag'])) {
        	$tagIds = implode(',', $member_categories['tag']['value']);
        }
        if (isset($member_categories['category'])) {
        	$categoryIds = implode(',', $member_categories['category']['value']);
        }

        $this->view->setVars([
                'member', $list,
                'member_categories' => $member_categories,
        		'tagIds' => $tagIds,
        		'categoryIds' => $categoryIds
		]);
        $categories = Category::find();
        $categories = $categories->toArray();
        $this -> view -> setVar('categories', $categories);
    
        if (strtolower($postData['searchTypeResult']) == 'map') {
        	$this->view->setVar('link_to_list', true);
        	$this->view->setVar('searchResult', true);
        	$this->view->setVar('searchResultMap', true);
        	
        	if ((int)$page > 1) {
	        	if ($results['page_now'] < $results['all_page']) {
					$res['stop'] = false;
				} else {
					$res['stop'] = true;
				}
        		$res['data'] = json_decode($result);
        		$res['page_now'] = $results['page_now'];
        		$res['page_all'] = $results['all_page'];
				$this -> sendAjax($res);
				exit();        		
        	} else {
        		$this->view->pick('event/map');
        	}
        } else {
            if ($page >1 ) {
                $this->view->setVar('searchResultList', true);
                $this->view->pick('event/eventListPart');
            }
            else {
                $this->view->setVar('searchResultList', true);
                $this->view->setVar('totalPagesJs', $fetchedData -> total_pages);
                $this->view->pick('event/eventList');
            }
        }
    }
}

