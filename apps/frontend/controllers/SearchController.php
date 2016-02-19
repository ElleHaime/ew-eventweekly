<?php

namespace Frontend\Controllers;

use Frontend\Models\EventImage;

use Frontend\Models\MemberFilter,
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
    	
//     	$this -> postData = ['searchTitle' => 'Bububu',
//     						 'searchLocationFormattedAddress' => ['locality' => 'Dublin',
//     						 									  'administrative_area_level_2' => 'Dublin City',
//     						 									  'administrative_area_level_1' => 'Dublin',
//     						 									  'country' => 'Ireland'],
//     						 'searchStartDate' => '2016-02-17',
//     						 'searchEndDate' => '2016-02-25',
//     						 'searchTypeResult' => 'List',
//     						 'searchCategories' => ['6' => 'on'],
//     						 'searchTags' => ['1' => 'on', '2' => 'on',  '3' => 'on',  '5' => 'on',  '83' => 'on',  '84' => 'on', '85' => 'on']];

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
     * @Route('/{location:[A-Za-z\-]+}/{arg:(this-week)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{arg:(this-weekend)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{dateDay:[a-z0-9]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/{dateStart:[a-z0-9]+}-{dateEnd:[a-z0-9]+}', methods={'GET'})
     * @Acl(roles={'guest','member'});
     */
    public function globalSearchAction($location, $arg1, $arg2 = '')
    {
    	$this -> setLocationByCity($location);
    	if (!$this -> setSearchDateVars($arg1)) $this -> setSearchDateCustom($arg1, $arg2);

    	$this -> searchAction();
    }
    

    /**
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(whats-on-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(things-to-do-in)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(today)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(tomorrow)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(this-week)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{arg:(this-weekend)}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{dateDay:[a-z0-9]+}', methods={'GET'})
     * @Route('/{location:[A-Za-z\-]+}/personalised/{dateStart:[a-z0-9]+}-{dateEnd:[a-z0-9]+}', methods={'GET'})
     * @Acl(roles={'guest','member'});
     */
    public function personalisedSearchAction($location, $arg1, $arg2 = '')
    {
    	$this -> setLocationByCity($location);
    	$this -> filtersBuilder -> addFilter('personalPresetActive', 1);
    	if (!$this -> setSearchDateVars($arg1)) $this -> setSearchDateCustom($arg1, $arg2);
    
    	$this -> searchAction();
    }
    

    protected function searchAction()
    {
    	$countResults = 0;
    	$likedEvents = $unlikedEvents = [];
    	$this -> view -> form = new SearchForm();
// _U::dump($this -> request -> getQuery());
// _U::dump($this -> filtersBuilder -> getFormFilters()['searchTitle']);    	
// _U::dump($this -> filtersBuilder -> getFormFilters());

    	if ($this -> filtersBuilder -> getMemberPreset()) {
    		$this -> pageTitle['type'] = 'Personalised events';
    		
    		if ($this -> session -> has('unlikedEvents')) {
    		$this -> fetchMemberLikes();
    			$this -> filtersBuilder -> addfilter('searchNotId', $this -> session -> get('unlikedEvents'));
    		}
    	}
    	$this -> pageTitle['location'] = 'in ' . $this -> filtersBuilder -> getFormFilters()['searchLocationCity'];
    	$this -> pageTitle['date'] = 'from '. date('jS F', strtotime($this -> filtersBuilder -> getSearchFilters()['searchStartDate'])) 
    								.' to ' . date('jS F', strtotime($this -> filtersBuilder -> getSearchFilters()['searchEndDate']));
    	
    	if (!is_null($this -> filtersBuilder -> getFormFilters()['searchTitle'])) {
    		$this -> pageTitle['title'] = 'for "' . $this -> filtersBuilder -> getFormFilters()['searchTitle'] . '"';
    	}
    	
    	$eventGrid = new \Frontend\Models\Search\Grid\Event($this -> filtersBuilder -> getSearchFilters(), 
    														 $this -> getDi(), null, ['adapter' => 'dbMaster']);
    	$page = $this -> request -> getQuery('page');
    	empty($page) ?	$eventGrid -> setPage(1) : $eventGrid -> setPage($page);
    	
    	if ($this -> filtersBuilder -> getFormFilters()['searchTypeResult'] == 'Map') {
    		$eventGrid -> setLimit(50);
    		$results = $eventGrid -> getData();
    		
    		foreach($results['data'] as $id => $event) {
    			$result[$event -> id] = (array)$event;
    			if ($this -> session -> has('likedEvents') && in_array($event -> id, $this -> session -> get('likedEvents'))) {
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
    		$countResults = $results['all_count'];
	   	} else {
	   		$eventGrid -> setLimit(9);
	   		$eventGrid -> setSort('start_date');
	   		$eventGrid -> setSortDirection('ASC');
	   		$results = $eventGrid -> getData();

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
	   		$countResults = $results['all_count'];
    	}
    	
    	$this -> view -> setVar('list', $result);
    	$this -> view -> setVar('eventsTotal', $countResults);
    	$this -> view -> setVar('listTitle', implode($this -> pageTitle, ' '));
    	$this -> view -> setVar('urlParams', $this -> request -> getQuery()['_url']);
    	$this -> view -> setVar('userSearch', $this -> filtersBuilder -> getFormFilters());
    	
    	$member_categories = (new MemberFilter()) -> getbyId();
    	if (isset($member_categories['tag'])) 
    		$this -> view -> setVars('tagIds', implode(',', $member_categories['tag']['value']));
    	
    	if (isset($member_categories['category'])) 
    		$this -> view -> setVars('categoryIds', implode(',', $member_categories['category']['value']));
    	
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
    		$page > 1 ? $this -> view -> pick('event/eventListPart') : $this -> view -> pick('event/eventList');
    	}
    	 
    	
    }

    
    protected function showList()
    {
    }
    
    
    protected function showMap()
    {
    }
    

    protected function showSliders()
    {
    }
    
    
    private function postElemExists($elem) 
    {
    	$resut = false;
    	if (array_key_exists($elem, $this -> postData) 
    						&& !is_array($this -> postData[$elem]) 
    						&& !empty($this -> postData[$elem])) 
    		$result = trim(strip_tags($this -> postData[$elem]));
    		
    	return $result;
    }
    
    
    private function setLocationByCity($arg)
    {
    	$newLocation = (new Location()) -> createOnChange(['city' => substr($arg, 0, strrpos($arg, '-')),
    														'country' => substr($arg, strrpos($arg, '-')+1)]);
		$this -> filtersBuilder -> addFilter('searchLocation', $newLocation);    	

    	$this -> cookies -> get('lastLat') -> delete();
    	$this -> cookies -> get('lastLng') -> delete();
    }
    
    
    protected function composeActionUrl()
    {
    	// set %city_name%
    	if ($this -> postElemExists('searchLocationFormattedAddress') && !empty($this -> postData['searchLocationFormattedAddress']))
    		$this -> cookies -> get('lastLat') -> delete();
    		$this -> cookies -> get('lastLng') -> delete();

    	$this -> actionUrl .= '/' . strtolower($this -> session -> get('location') -> city)
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
    	$dateSearchVariables = _UDT::getDatesVars();
    	 
    	if (isset($dateSearchVariables[$key])) {
    		$this -> filtersBuilder -> addFilter('searchStartDate', $dateSearchVariables[$key]['start'])
    							    -> addFilter('searchEndDate', $dateSearchVariables[$key]['end']);
    		
    		return true;
    	} 
    	
    	return false;
    }

    
    private function setSearchDateCustom($start, $end = '')
    {
		$pattern = '/^([0-9]{1,2})(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec){1}/i'; 

		preg_match($pattern, $start, $matches);
		!empty($matches) ? $startDate = date('Y-m-d H:i:s', strtotime($matches[0])) 
						 : $starttDate = _UDT::getDefaultStartDate();

		preg_match($pattern, $end, $matches);
		!empty($matches) ? $endDate = date('Y-m-d H:i:s', strtotime($matches[0] . '+ 1 day'))
						 : $endDate = date('Y-m-d H:i:s', strtotime($startDate . '+ 1 day'));
		
		$this -> filtersBuilder -> addFilter('searchStartDate', $startDate)
								-> addFilter('searchEndDate', $endDate);
		
		
		return;
    } 	
}