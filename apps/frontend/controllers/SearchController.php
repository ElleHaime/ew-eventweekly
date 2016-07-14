<?php

namespace Frontend\Controllers;

use Frontend\Models\EventImage,
	Frontend\Models\MemberFilter,
    Frontend\Models\Tag,          
    Frontend\Form\SearchForm,
    Phalcon\Mvc\Model\Resultset,
    Frontend\Models\Category,
    Frontend\Models\Location,
    Frontend\Models\Event,
    Frontend\Models\EventRating,
    Frontend\Models\Featured,
    Core\Utils as _U,
    Core\Utils\Location as _L,
    Core\Utils\DateTime as _UDT,
    Frontend\Models\Cron as Cron;

class SearchController extends \Core\Controller
{
    use \Core\Traits\TCMember;
    
    const RQ_PERSONALISED		= 'personalised';  
    
    public static $today		= 'today';
    public static $tomorrow	= 'tomorrow';
    public static $week	 	= 'this-week';
    public static $weekend	 	= 'this-weekend';
    
   	protected $pageTitle		= ['type' => 'All events',
   								   'date' => '',
   								   'location' => '',
   								   'title' => ''];
   	
   	protected $postData		= [];
   	protected $queryData		= [];
   	protected $actionUrl		= '';
   	protected $currentGrid		= 'event';
  	
   	
    
	/**
	 * @Route('/search/addSearchParam', methods={'POST','GET'})
	 * @Acl(roles={'guest', 'member'})
	 */    
    public function addSearchParamAction()
    {
    	$result = ['errors' => '',
    			   'actionUrl' => '',
    			   'status' => 'error'];
    	$this -> postData = $this -> request -> getPost();
    	
		foreach ($this -> postData as $key => $val) {
			if ($value = $this -> postElemExists($key)) {
				$this -> filtersBuilder -> addFilter($key, $val);
			} 
		}
 		$this -> filtersBuilder -> applyFilters();

 		if ($this -> composeActionUrl()) { 
 			$result['status'] = 'OK';
 			$result['actionUrl'] = $this -> actionUrl;
 		}
		$result['status'] = 'OK';
    	$result['search'] = $this -> filtersBuilder -> getSearchFilters();
    	$result['form'] = $this -> filtersBuilder -> getFormFilters();
    	$result['location'] = $this -> session -> get('location');
		    	 
		$this -> sendAjax($result);
    }

    
    /**
     * @Route("/{city:[A_Za-z\-]+}/trending", methods={"GET", "POST"})
     * @Acl(roles={'guest','member'});
     */
    public function trendingSearchAction()
    {
    	_U::dump('trending');
    }

    
    /**
     * @Route("/{location:[A_Za-z\-]+}", methods={"GET", "POST"})
     * @Acl(roles={'guest','member'});
     */
    public function featuredSearchAction()
    {
    	_U::dump('featured');
    }
    
    
    /**
     * @Route('/{location:[A-Za-z\-]+}/{arg:(whats-on-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(things-to-do-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(today)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(tomorrow)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{dateDay:[0-9]{1,2}[a-z]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{dateStart:[0-9]{1,2}[a-z]+}-{dateEnd:[0-9]{1,2}[a-z]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(this-week)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(this-weekend)}', methods={'GET'})
     * @Acl(roles={'guest','member'});
     */
    public function globalSearchAction($location, $arg1, $arg2 = '')
    {
// http://events.apppicker.loc/las%20vegas-us/1mar-28may
// $re = '/[A-Za-z\-]+';
// $str = '';
//$str = 'las%20vegas-us/today';    	
    	$this -> setLocationByCity($location);
    	if (!$this -> setSearchDateVars($arg1)) $this -> setSearchDateCustom($arg1, $arg2);

    	$this -> searchAction();
    }
    

    /**
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(whats-on-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(things-to-do-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(today)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(tomorrow)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{dateDay:[a-z0-9]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{dateStart:[a-z0-9]+}-{dateEnd:[a-z0-9]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(this-week)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(this-weekend)}', methods={'GET'})
     * @Acl(roles={'guest','member'});
     */
    public function personalisedSearchAction($location, $arg1, $arg2 = '')
    {
    	$this -> setLocationByCity($location);
    	$this -> filtersBuilder -> addFilter('personalPresetActive', 1) -> applyFilters();
    	if (!$this -> setSearchDateVars($arg1)) $this -> setSearchDateCustom($arg1, $arg2);
    
    	$this -> searchAction();
    }
    

    private function searchAction()
    {
    	$countResults = 0;
    	$likedEvents = $unlikedEvents = [];
    	$this -> currentGrid  = $this -> filtersBuilder -> getFormFilters()['searchGrid'];
    	$this -> view -> form = new SearchForm();

    	if ($this -> currentGrid == 'event') {
    		if ($this -> session -> has('unlikedEvents')) {
    			$this -> fetchMemberLikes();
    			$this -> filtersBuilder -> addFilter('searchNotId', $this -> session -> get('unlikedEvents'));
    		}
    	}
    	$this -> setTitleType();
    	$this -> setTitleLocation();
    	$this -> setTitleDates();
    	$this -> setTitleTitle();
// _U::dump($this -> filtersBuilder -> getSearchFilters(), true);
// _U::dump($this -> filtersBuilder -> getFormFilters(), true);
// _U::dump($this -> currentGrid);

   		$objectsGridName = '\Frontend\Models\Search\Grid\\' . ucfirst($this -> currentGrid);
   		$objectsGridModel = '\Frontend\Models\Search\Model\\' . ucfirst($this -> currentGrid);
   		$objectsImgModel = '\Frontend\Models\\' . ucfirst($this -> currentGrid) . 'Image';
    	 
    	$objectsGrid = new $objectsGridName($this -> filtersBuilder -> getSearchFilters(), $this -> getDi(), null, ['adapter' => 'dbMaster']);
    	
    	$page = $this -> request -> getQuery('page');
    	empty($page) ?	$objectsGrid -> setPage(1) : $objectsGrid -> setPage($page);
    	
    	if ($this -> filtersBuilder -> getFormFilters()['searchTypeResult'] == 'Map') {
    		$objectsGrid -> setLimit(50);
    		$results = $objectsGrid -> getData();
    		
    		foreach($results['data'] as $id => $object) {
    			$result[$object -> id] = (array)$object;
    			if ($this -> currentGrid == 'event' && $this -> session -> has('likedEvents') && in_array($object -> id, $this -> session -> get('likedEvents'))) {
    				$result[$object -> id]['disabled'] = 'disabled';
    			}
    		
   				$result[$object -> id]['logo'] = (new $objectsImgModel()) -> getLogo($value);
    			$result[$object -> id]['slugUri'] = \Core\Utils\SlugUri::slug($object -> name). '-' . $object -> id;
    			$result[$object -> id]['description'] = trim($object -> description);
    			$result[$object -> id]['cover'] = (new $objectsImgModel()) -> getCover($object);
    		}
    		
    		$result = json_encode($result, JSON_UNESCAPED_UNICODE);
    		$countResults = $results['all_count'];
	   	} else {
	   		$objectsGrid -> setLimit(9);
	   		$objectsGrid -> setSort((new $objectsGridModel) -> getDefaultSorting());
	   		$objectsGrid -> setSortDirection('ASC');
	   		$results = $objectsGrid -> getData();
// _U::dump($results);
	   		foreach($results['data'] as $key => $value) {
	   			if ($this -> currentGrid == 'event' && !empty($likedEvents) && in_array($value -> id, $likedEvents)) {
	   				$value -> disabled = 'disabled';
	   			}
	   			$value -> cover = (new $objectsImgModel()) -> getCover($value);
	   			$result[] = json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), FALSE);
	   		}
	   		
	   		if ($results['all_page'] > 1) {
	   			$this -> view -> setVar('pagination', $results['array_pages']);
	   			$this -> view -> setVar('pageCurrent', $results['page_now']);
	   			$this -> view -> setVar('pageTotal', $results['all_page']);
	   		} else {
	   			$this -> view -> setVar('pageTotal', 0);
	   		}
	   		$countResults = $results['all_count'];
    	}
    	
    	$this -> view -> setVar('list', $result);
    	$this -> view -> setVar('searchGrid', $this -> currentGrid);
    	$this -> view -> setVar('eventsTotal', $countResults);
    	$this -> view -> setVar('searchTypeResult', $this -> filtersBuilder -> getFormFilters()['searchTypeResult']);
    	$this -> view -> setVar('listTitle', implode($this -> pageTitle, ' '));
    	$this -> view -> setVar('urlParams', $this -> request -> getQuery()['_url']);
    	$this -> view -> setVar('userSearch', $this -> filtersBuilder -> getFormFilters());
    	
    	$member_categories = (new MemberFilter()) -> getbyId();
    	if (isset($member_categories['tag'])) 
    		$this -> view -> setVar('tagIds', implode(',', $member_categories['tag']['value']));
    	
    	if (isset($member_categories['category'])) 
    		$this -> view -> setVar('categoryIds', implode(',', $member_categories['category']['value']));
    	
    	$this -> view -> setVar('searchPage', true);
    	
    	if ($this -> filtersBuilder -> getFormFilters()['searchTypeResult'] == 'Map') {
    		$this -> view -> setVar('link_to_list', true);
    		$this -> view -> setVar('searchResult', true);
    		$this -> view -> setVar('searchResultMap', true);
    		 
    		if ((int)$page > 1) {
    			$results['page_now'] < $results['all_page'] ? $res['stop'] = false : $res['stop'] = true;
    			$res['data'] = json_decode($result);
    			$res['page_now'] = $results['page_now'];
    			$res['page_all'] = $results['all_page'];
    			$this -> sendAjax($res);
    			
    			exit();
    			
    		} else {
    			$this -> view -> pick('event/map');
    		}
    		
    	} else {
    		$this -> view -> setVar('searchResultList', true);
    		$page > 1 ? $this -> view -> pick('search/searchListPart') 
    				  : $this -> view -> pick('search/searchList');
    	}
  	 
   	
    }

    
    private function setTitleType()
    {
   		$grids = $this -> currentGrid . 's';
    	
    	$this -> filtersBuilder -> getMemberPreset() 
    				? $this -> pageTitle['type'] = 'Personalised ' . $grids 
    				: $this -> pageTitle['type'] = 'All ' . $grids;
    	
    	return;
    }
    
    
    private function setTitleLocation()
    {
    	$this -> pageTitle['location'] = 'in ' . $this -> filtersBuilder -> getFormFilters()['searchLocationCity'];
    	
    	return;
    }
    
    
    private function setTitleDates()
    {
    	$this -> pageTitle['date'] = 'from '. date('jS F', strtotime($this -> filtersBuilder -> getFormFilters()['searchStartDate']))
    								.' to ' . date('jS F', strtotime($this -> filtersBuilder -> getFormFilters()['searchEndDate']));
    	
    	return;
    }
    
    
    private function setTitleTitle()
    {
    	if (!is_null($this -> filtersBuilder -> getFormFilters()['searchTitle'])) {
    		$this -> pageTitle['title'] = 'for "' . $this -> filtersBuilder -> getFormFilters()['searchTitle'] . '"';
    	}
    	
    	return;
    }
    
    private function showList()
    {
    }
    
    
    private function showMap()
    {
    }
    

    private function showSliders()
    {
    }
    
    
    private function postElemExists($elem) 
    {
    	$resut = false;
    	
    	if (array_key_exists($elem, $this -> postData) && !empty($this -> postData[$elem])) {
    		if (is_string($this -> postData[$elem])) { 
    			$result = trim(strip_tags($this -> postData[$elem]));
	    	} elseif(is_array($this -> postData[$elem])) {
	    		$result = $this -> postData[$elem];
	    	}
    	}
    	
    	return $result;
    }
    
    
    private function setLocationByCity($arg)
    {
    	$requestedLocation = ['city' => substr($arg, 0, strrpos($arg, '-')),
    						  'country' => \Core\Utils\Location::getNameByCode(substr($arg, strrpos($arg, '-')+1))];

    	if (!$this -> session -> has('location') || $this -> session -> get('location') -> search_alias != $requestedLocation['city']) {
	    	$newLocation = (new Location()) -> createOnChange($requestedLocation);

			$this -> filtersBuilder -> addFilter('searchLocation', $newLocation);  
			$this -> filtersBuilder -> applyFilters();
		
	    	$this -> cookies -> get('lastLat') -> delete();
	    	$this -> cookies -> get('lastLng') -> delete();
    	}
    }
    
    
    protected function composeActionUrl()
    {
    	// set %city_name%
    	if ($this -> postElemExists('searchLocationFormattedAddress') && !empty($this -> postData['searchLocationFormattedAddress']))
    		$this -> cookies -> get('lastLat') -> delete();
    		$this -> cookies -> get('lastLng') -> delete();

    	$this -> actionUrl .= '/' . str_replace(' ', '-', strtolower($this -> session -> get('location') -> city))
    												. '-' . strtolower(_L::getCodeByName($this -> session -> get('location') -> country));
    	 
     	// set %personalised%
    	//if ($this -> postElemExists('personalPresetActive') && $this -> postData['personalPresetActive'] == 1) 
    	if ($this -> filtersBuilder -> getMemberPreset()) $this -> actionUrl .= '/personalised';

    	// set %from_date%-%to_date%
		if ($this -> postData['searchStartDate'] == $this -> postData['searchEndDate']) {
			if (strtotime('today') == strtotime($this -> postData['searchStartDate'])) {
				$this -> actionUrl .= '/' . self::$today;
			} elseif (strtotime('tomorrow') == strtotime($this -> postData['searchStartDate'])) {
				$this -> actionUrl .= '/' . self::$tomorrow;
			}
		} else {
			if (strtotime($this -> postData['searchEndDate']) - strtotime($this -> postData['searchStartDate']) == 604800) {
				$this -> actionUrl .= '/' . self::$week;
			} elseif((strtotime($this -> postData['searchStartDate']) == strtotime('next Saturday')) && (strtotime($this -> postData['searchEndDate']) == strtotime('next Sunday'))) {
				$this -> actionUrl .= '/' . self::$weekend;
			} else {
				$this -> actionUrl .= '/' . strtolower(date('jM', strtotime($this -> postData['searchStartDate'])))
						. '-' . strtolower(date('jM', strtotime($this -> postData['searchEndDate'])));
			}
		}	
		
		return true;
    }
    
    
    private function setSearchDateVars($key = 'today')
    {
    	if (!$this -> filtersBuilder -> isFiltersInSession()) {
	    	$dateSearchVariables = _UDT::getDatesVars();
	 
	    	if (isset($dateSearchVariables[$key])) {
	    		$this -> filtersBuilder -> addFilter('searchStartDate', $dateSearchVariables[$key]['start'])
	    							    -> addFilter('searchEndDate', $dateSearchVariables[$key]['end']);
	    		
	    		return true;
	    	} 
	    	
	    	return false;
    	}
    	
    	return true;
    }

    
    private function setSearchDateCustom($start, $end = '')
    {
    	if (!$this -> filtersBuilder -> isFiltersInSession()) {
			$pattern = '/^([0-9]{1,2})(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec){1}/i'; 
	
			preg_match($pattern, $start, $matches);
			!empty($matches) ? $startDate = date('Y-m-d H:i:s', strtotime($matches[0])) 
							 : $starttDate = _UDT::getDefaultStartDate();
	
			preg_match($pattern, $end, $matches);
			!empty($matches) ? $endDate = date('Y-m-d H:i:s', strtotime($matches[0]))
							 : $endDate = date('Y-m-d H:i:s', strtotime($startDate));
			
			$this -> filtersBuilder -> addFilter('searchStartDate', $startDate)
									-> addFilter('searchEndDate', $endDate);
    	}
    			
		return;
    }
    

    /**
     * @Route('/search/resetFilters', methods={'GET'})
     * @Acl(roles={'guest', 'member'})
     */
    public function resetFiltersAction()
    {
    	$this -> filtersBuilder -> resetFilters();
    	$this -> session -> set('location', false);
    	$this -> session -> destroy();

_U::dump('done');
    	$this -> response -> redirect('/');
    }
}