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
        $this -> view -> setVar('categories', $categories);

        $form = new SearchForm();
        $this -> view -> form = $form;

        $result = array();
        $countResults = 0;

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
                $queryData['searchEndDate'] = date('Y-m-d H:i:s', strtotime('today +30 days'));
                
                $pageTitle .= 'from "'.$postData['searchStartDate'].'"  and later | ';
            }  else {
            	$startDate = date('Y-m-d H:i:s', strtotime('today -1 minute'));
            	$queryData['searchStartDate'] = $startDate;
            	
            	if ($elemExists('searchTitle', false)) {
            		$queryData['searchEndDate'] = date('Y-m-d H:i:s', strtotime('today +30 days'));
            		$pageTitle .= 'now and till "' . date('Y-m-d', strtotime('+3 days midnight')) . '" | ';
            	} 
			}

			if ($elemExists('searchTags')) {
				$this -> session -> set('userSearchFilters', $postData['searchTags']);
				$this -> filters -> loadUserFilters(false); 
				$queryData['searchTag'] = array_keys($postData['searchTags']); 
            } 

			$eventGrid = new \Frontend\Models\Search\Grid\Event($queryData, $this->getDi(), null, ['adapter' => 'dbMaster']);

			// search type
            if ($elemExists('searchTypeResult')) {
                if ($postData['searchTypeResult'] == 'Map') {
                	$eventGrid -> setLimit(100);
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

        $this->view->setVars([
                'member', $list,
                'member_categories' => $member_categories,
        		'tagIds' => $tagIds
		]);
        
        if (strtolower($postData['searchTypeResult']) == 'Map') {
        	$this->view->setVar('link_to_list', true);
        	$this->view->setVar('searchResult', true);
        	$this->view->setVar('searchResultMap', true);
            $this->view->pick('event/mapEvent');
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

