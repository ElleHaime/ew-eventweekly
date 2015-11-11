<?php

namespace Frontend\Controllers;

use Frontend\Models\EventImage;

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
//_U::dump($postData);
//_U::dump($this -> session -> get('userSearch'));

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
        
        $pageTitle['type'] = 'Search results';
        $queryData = [];
        
        if (!isset($postData['personalPresetActive'])) {
        	$postData['personalPresetActive'] = 0;
        	$prevSearch = [];
        	if ($this -> session -> has('userSearch')) $prevSearch = $this -> session -> get('userSearch');
        		 
        	if (isset($prevSearch['personalPresetActive'])) {
        		$postData['personalPresetActive'] = $prevSearch['personalPresetActive'];
        	}
        }

        if (isset($postData['personalPresetActive']) && $postData['personalPresetActive'] == 1 && $this -> session -> has('memberId')) {
            $pageTitle['type'] = 'Personalized events';
        } else {
            $pageTitle['type'] = 'All events';
        }

        // if income data not empty
        if (!empty($postData)) {
        	$this -> view -> setVar('userSearch', $postData);

            // add search condition by location
        	// if no location specify - set from user location
        	if (($elemExists('searchLocationField') && $postData['searchLocationField'] != '')) {
        		$lat = ($postData['searchLocationLatMin'] + $postData['searchLocationLatMax']) / 2;
				$lng = ($postData['searchLocationLngMin'] + $postData['searchLocationLngMax']) / 2;
				$formattedAddress = get_object_vars(json_decode($postData['searchLocationFormattedAddress'])); 				
				
				$newLocation = (new Location()) -> createOnChange(['latitude' => $lat, 'longitude' => $lng, 'city' => $formattedAddress['locality'], 'country' => $formattedAddress['country']]);

				if (isset($newLocation -> id)) {
					$queryData['searchLocationField'] = $newLocation -> id;
				} 
				$this->session->set('location', $newLocation);
				$this->cookies->get('lastLat')->delete();
				$this->cookies->get('lastLng')->delete();

				$pageTitle['location'] = 'in ' . $newLocation->alias;
        	}
        		
            // add search condition by dates
            if ($elemExists('searchStartDate')) {
                $queryData['searchStartDate'] = date('Y-m-d H:i:s', strtotime($postData['searchStartDate']));
            }  else {
            	$queryData['searchStartDate'] = _UDT::getDefaultStartDate();
			}
            if ($elemExists('searchEndDate')) {
                $queryData['searchEndDate'] = date('Y-m-d H:i:s', strtotime($postData['searchEndDate'] . ' +1 day'));
            }  else {
                $queryData['searchEndDate'] = _UDT::getDefaultEndDate();
            }
        	$pageTitle['date'] = 'from '. date('jS F', strtotime($queryData['searchStartDate'])).' to ' . date('jS F', strtotime($queryData['searchEndDate'] . ' -1 day'));

//             if ($elemExists('searchStartDate')) {
//             	$searchStartDate = date('Y-m-d H:i:s', strtotime($postData['searchStartDate']));
//             }  else {
//             	$searchStartDate = _UDT::getDefaultStartDate();
//             }
//             if ($elemExists('searchEndDate')) {
//             	$searchEndDate = date('Y-m-d H:i:s', strtotime($postData['searchEndDate'] . ' +1 day'));
//             }  else {
//             	$searchEndDate = _UDT::getDefaultEndDate();
//             }
//             $queryData['searchStartDate'] = [$searchStartDate, $searchEndDate];
//             //$queryData['searchEndDate'] = ['min' => $searchStartDate, 'max' => $searchEndDate];
// 			$pageTitle['date'] = 'from '. date('jS F', strtotime($searchStartDate)).' to ' . date('jS F', strtotime($searchEndDate . ' -1 day'));

			
			
			// add search condition by title or tag
			$searchTitleTags = [];
			if ($elemExists('searchTitle')) {
				$searchTitleSanitized = (new \Phalcon\Filter()) -> sanitize($postData['searchTitle'], 'string');
				
				$tags = Tag::find(['name like "%' . $searchTitleSanitized . '%"']);
				if ($tags) {
					foreach ($tags as $searchTag) {
						$searchTitleTags[] = (int)$searchTag -> id;
					}
				}

				// :, \\, {, }, " 
				if (!empty($searchTitleTags)) {
					$queryData['compoundTitle'] = preg_replace('/([\[\]\{\}\\:\!]+)/i', ' ', $searchTitleSanitized);
				} else {
					$queryData['searchTitle'] = preg_replace('/([\[\]\{\}\\:\!]+)/i', ' ', $searchTitleSanitized);
				}
				$pageTitle['title'] = 'for "'.$postData['searchTitle'].'"';
			}

			if (($elemExists('searchTags') || $elemExists('searchCategories'))) {
				if ($postData['personalPresetActive'] != 1) {
				
					if ($elemExists('searchTags')) {
						$userSearchFilters['tag'] = $postData['searchTags'];
						if (!$elemExists('searchTitle')) {
							$queryData['compoundTag'] = array_keys($postData['searchTags']);
						} else {
							if (!empty($searchTitleTags)) {
								$queryData['compoundTag'] = $searchTitleTags;
							}
						}
					} else {
						$userSearchFilters['tag'] = [];
					}
					
					if ($elemExists('searchCategories')) {
						$userSearchFilters['category'] = $postData['searchCategories'];
						if (!$elemExists('searchTitle')) {
							$queryData['compoundCategory'] = array_keys($postData['searchCategories']);
						}
					} else {
						$userSearchFilters['category'] = [];
					}
						
					$this -> filters -> setSessionFilters($userSearchFilters)
									 -> loadUserFilters(false);
				} else {
					$searchTags = $this -> filters -> getActiveTags();
					$searchCategories = $this -> filters -> getActiveCategories();
			
					if (!empty($searchTags)) {
						if ($elemExists('searchTitle')) {
							if (!empty($searchTitleTags)) {
								$postData['personalPresetActive'] = 0;
								$pageTitle['type'] = 'All events';
									
								$queryData['compoundTag'] = $searchTitleTags;
							}
						} else {
							$queryData['compoundTag'] = $searchTags;
						}
					}
					if (!empty($searchCategories) && !$elemExists('searchTitle')) {
						if ($postData['personalPresetActive'] != 0) {
							$queryData['compoundCategory'] = $searchCategories;
						}
					}
				}
			} else {
				if ($postData['personalPresetActive'] == 1) {
					$this -> filters -> loadUserFilters();
				} else {
					$this -> filters -> loadUserFilters(false);
				}
				
				$searchCategories = $this -> filters -> getActiveCategories();
				if (!empty($searchCategories)) {
					$queryData['compoundCategory'] = $searchCategories;
				}
				$searchTags = $this -> filters -> getActiveTags();
				if (!empty($searchTags)) {
					$queryData['compoundTag'] = $searchTags;
				}
				
				if ($this -> session -> has('memberId') && !$elemExists('searchTitle') && $postData['personalPresetActive'] == 1) {
					$pageTitle['type'] = 'Personalized events';
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
                    	$result[$event -> id]['description'] = trim($event -> description);
                    	$result[$event -> id]['cover'] = (new EventImage()) -> getCover($event);
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
						$value -> cover = (new EventImage()) -> getCover($value);
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

		$this->session->set('userSearch', $postData);

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
        $this->view->setVar('listTitle', implode($pageTitle, ' '));
     
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