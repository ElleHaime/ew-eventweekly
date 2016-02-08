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
    
    public $postSearchVariables = ['searchLocationField',
							    	'searchLocationLatMin',
							    	'searchLocationLatMax',
							    	'searchLocationLngMin',
							    	'searchLocationLngMax',
							    	'searchLocationFormattedAddress',
							    	'personalPresetActive',
							    	'searchStartDate',
							    	'searchEndDate',
							    	'searchTitle',
							    	'searchTypeResult', 	// map, list
							    	'searchTags',
							    	'searchCategories'];
    
   	protected $pageTitle		= ['type' => 'All events',
   								   'date' => '',
   								   'location' => '',
   								   'title' => ''];
   	
   	protected $postData		= [];
   	protected $queryData		= [];
   	protected $actionUrl		= '';
   	
   	
    
	/**
	 * @Route('/search/addSearchParam', methods={'POST'})
	 * @Acl(roles={'guest', 'member'})
	 */    
    public function addSearchParamAction()
    {
    	$result = ['errors' => '',
    			   'actionUrl' => '',
    			   'status' => 'error'];
		foreach ($this -> postData as $key => $val) {
			if ($value = $this -> postElemExists($key)) {
				$userPreparedSearch[$key] = $value;
			} 
		}
		
		$this -> session -> set('userPreparedSearch', $userPreparedSearch);
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
    	$this -> setSearchPersonalised();
    	if (!$this -> setSearchDateVars($arg1)) $this -> setSearchDateCustom($arg1, $arg2);
    
    	$this -> searchAction();
    }
    

    public function searchAction()
    {
    	_U::dump($this -> session -> get('userPreparedSearch'));
    	
    	(new Cron()) -> createUserTask();
    	 
    	$this -> view -> form = new SearchForm();
    	$postData = $this -> session -> get('userPreparedSearch');
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
    	if (array_key_exists($elem, $this -> postData) && !is_array($this -> postData[$elem]) && in_array($elem, $this -> postSearchVariables)) 
    		$result = trim(strip_tags($this -> postData[$elem]));
    		
    	return $result;
    }
    
    
    private function setLocationByCoordinates()
    {
    	$lat = ($this -> postData['searchLocationLatMin'] + $this -> postData['searchLocationLatMax']) / 2;
    	$lng = ($this -> postData['searchLocationLngMin'] + $this -> postData['searchLocationLngMax']) / 2;
    	$formattedAddress = get_object_vars(json_decode($this -> postData['searchLocationFormattedAddress']));
    	
    	$newLocation = (new Location()) -> createOnChange(['latitude' => $lat, 
    													    'longitude' => $lng, 
    														'city' => $formattedAddress['locality'], 
    														'country' => $formattedAddress['country']]);
    	$this -> session -> set('location', $newLocation);
    	$this -> cookies -> get('lastLat') -> delete();
    	$this -> cookies -> get('lastLng') -> delete();
    }
    
    
    private function setLocationByCity($arg)
    {
    	$city = substr($arg, 0, strrpos($arg, '-'));
    	$country = substr($arg, strrpos($arg, '-')+1);
    	
    	$newLocation = (new Location()) -> createOnChange(['city' => $city,
    														'country' => $country]);
    	$this -> session -> set('location', $newLocation);
    	$this -> cookies -> get('lastLat') -> delete();
    	$this -> cookies -> get('lastLng') -> delete();
    }
    
    
    protected function composeActionUrl()
    {
    	// set %city_name%
    	if ($this -> postElemExists('searchLocationFormattedAddress') && !empty($this -> postData['searchLocationFormattedAddress'])) 
    		$this -> setLocationByCoordinates();
    	$this -> actionUrl .= '/' . strtolower($this -> session -> get('location') -> city)
    												. '-' . strtolower(_L::getCodeByName($this -> session -> get('location') -> country));
    	 
     	// set %personalised%
    	if ($this -> postElemExists('personalPresetActive') && $this -> postData['personalPresetActive'] == 1) 
    		$this -> actionUrl .= '/personalised';

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
    	$dateSearchVariables = ['today' =>
							    	['start' => date('Y-m-d H:i:s', strtotime('today')),
							    	'end' => date('Y-m-d H:i:s', strtotime('tomorrow'))],
					    		'tomorrow' =>
							    	['start' => date('Y-m-d H:i:s', strtotime('tomorrow')),
							    	'end' => date('Y-m-d H:i:s', strtotime('tomorrow + 1 day'))],
						    	'this-week' =>
							    	['start' => date('Y-m-d H:i:s', strtotime('today')),
							    	'end' => date('Y-m-d H:i:s', strtotime('today + 7 days'))],
						    	'this-weekend' =>
							    	['start' => date('Y-m-d H:i:s', strtotime('next Saturday')),
							    	'end' => date('Y-m-d H:i:s', strtotime('next Monday'))],
						    	'whats-on-in' =>
							    	['start' => _UDT::getDefaultStartDate(),
							    	'end' => _UDT::getDefaultEndDate()],
						    	'things-to-do-in' =>
							    	['start' => _UDT::getDefaultStartDate(),
							    	'end' => _UDT::getDefaultEndDate()]
						    	];
    	 
    	if (isset($dateSearchVariables[$key])) {
    		$prepared = $this -> session -> get('userPreparedSearch');
    		$prepared['searchStartDate'] = $dateSearchVariables[$key]['start'];
    		$prepared['searchEndDate'] = $dateSearchVariables[$key]['end'];
    		
    		$this -> session -> set('userPreparedSearch', $prepared);
    		
    		return;
    	} else {
			return false;
    	}
    }

    
    private function setSearchDateCustom($start, $end = '')
    {
    	$prepared = $this -> session -> get('userPreparedSearch');
		$pattern = '/^([0-9]{1,2})(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec){1}/i'; 

		preg_match($pattern, $start, $matches);
		if (!empty($matches)) {
			$prepared['searchStartDate'] = date('Y-m-d H:i:s', strtotime($matches[0]));
		} else {
			$prepared['searchStartDate'] = _UDT::getDefaultStartDate();
		}

		preg_match($pattern, $end, $matches);
		if (!empty($matches)) {
			$prepared['searchEndDate'] = date('Y-m-d H:i:s', strtotime($matches[0] . '+ 1 day'));
		} else {
			$prepared['searchEndDate'] = date('Y-m-d H:i:s', strtotime($prepared['searchStartDate'] . '+ 1 day'));
		}

		$this -> session -> set('userPreparedSearch', $prepared);
		
		return;
    } 	
    
    
    private function setSearchPersonalised($isActive = true)
    {
    	$prepared = $this -> session -> get('userPreparedSearch');
    	$isActive ? $prepared['personalPresetActive'] = 1 : $prepared['personalPresetActive'] = 0;
    	$this -> session -> set('userPreparedSearch', $prepared);
    	
    	return;
    }
}