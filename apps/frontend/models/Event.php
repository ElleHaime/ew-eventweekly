<?php 

namespace Frontend\Models;

use Categoryzator\Categoryzator;
use Categoryzator\Core\Text;
use Objects\Event as EventObject,
    Core\Utils as _U,
    Thirdparty\Facebook\Extractor,
    Frontend\Models\Location,
    Frontend\Models\Venue,
    Frontend\Models\MemberNetwork,
    Objects\EventImage,
    Objects\EventMember,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\EventMember as EventMemberModel,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\EventLike,
    Objects\EventCategory AS EventCategoryObject,
    Objects\EventTag AS EventTagObject,
    Objects\Tag AS TagObject,
    Phalcon\Mvc\Model\Resultset;
use Core\Utils\SlugUri as SUri;

class Event extends EventObject
{
    use \Core\Traits\ModelConverter;

    const FETCH_OBJECT = 1;

    const FETCH_ARRAY = 2;

    const ORDER_ASC = 3;

    const ORDER_DESC = 4;

    const CONDITION_SIMPLE = 5;

    const CONDITION_COMPLEX = 6;

	public static $eventStatus = array(0 => 'inactive',
							  		   1 => 'active');


	public static $eventRecurring = array('0' => 'Once',
										  '1' => 'Daily',
										  '7' => 'Weekly');
	protected $locator = false;
	private $conditions = [];
    private $defaultConditions = [
        [
            'type' => self::CONDITION_COMPLEX,
            'condition' => '\Frontend\Models\Event.deleted = 0'
        ]
    ];
	private $selector = ' AND';

    public $virtualFields = [
        'slugUri' => 'self->id.\'-\'.\Core\Utils\SlugUri::slug(self -> name)',
//        'start_date_nice' => 'date(\'d/m/Y\', strtotime(self -> start_date))',
//        'end_date_nice' => 'date(\'d/m/Y\', strtotime(self -> end_date))',
//        'start_time' => 'date(\'H:i\', strtotime(self -> start_date))',
//        'end_time' => 'date(\'H:i\', strtotime(self -> end_date))'

    ];

    public function afterDelete()
    {
        $di = $this -> getDi();

        if ($di -> has('session')) {
            $session = $di -> getShared('session');

            $eid = $this->id;
            $uid = $session -> get('memberId');

            $eventLike = EventLike::findFirst('event_id = ' . $eid . ' AND member_id = ' . $uid);
            if ($eventLike) {
                $eventLike->delete();

                $userEventsLiked = EventLike::find( array('member_id = ' . $uid . " AND status = 1") )->count();
                $session -> set('userEventsLiked', $userEventsLiked);
            }

            $eventGoing = EventMemberModel::findFirst('event_id = ' . $eid . ' AND member_id = ' . $uid);
            if ($eventGoing) {
                $eventGoing->delete();

                $userEventsGoing = $session -> get('userEventsGoing') - 1;
                $session -> set('userEventsGoing', $userEventsGoing);
            }
        }
    }
	
	public function afterFetch()
	{
        /*if ($this -> start_date) {
            $tryTime = date('H:i', strtotime($this -> start_date));
            if ($tryTime != '00:00') {
                $this -> start_time = $tryTime;
            } else {
                $this -> start_time = '';
            }
            $tryDate = date('d/m/Y', strtotime($this -> start_date));;
            if ($tryDate != '0000-00-00') {
                $this -> start_date_nice = $tryDate;
                $this -> start_date = $tryDate;
            } else {
                $this -> start_date_nice = '';
                $this -> start_date = '';
            }
        } else {
            $this -> start_time = $this -> start_date_nice = '';
        }

        if ($this -> end_date) {
            $tryTime = date('H:i', strtotime($this -> end_date));
            if ($tryTime != '00:00') {
                $this -> end_time = $tryTime;
            } else {
                $this -> end_time = '';
            }
            $tryDate = date('d/m/Y', strtotime($this -> end_date));
            if ($tryDate != '0000-00-00') {
                $this -> end_date_nice = $tryDate;
                $this -> end_date = $tryDate;
            } else {
                $this -> end_date_nice = '';
                $this -> end_date = '';
            }
        } else {
            $this -> end_time = $this -> end_date_nice = '';
        }*/

        $this->slugUri = $this->id.'-'.SUri::slug($this->name);
	}

    public function beforeUpdate()
    {
        if (strlen($this->start_date) <= 10) {
            $this->start_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->start_date) . ' ' . $this->start_time));
        }

        if (strlen($this->end_date) <= 10) {
            $this->end_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $this->end_date) . ' ' . $this->start_time));
        }
    }

    public function getCreatedEventsCount($uId)
    {
        if ($uId) {
            return self::find(array('member_id = ' . $uId . ' AND deleted = 0')) -> count();
        } else {
            return 0;
        }
    }

    public function grabEventsByFbToken($token, $location)
    {
        $this -> facebook = new Extractor();
        $events = $this -> facebook -> getEventsSimpleByLocation($token, $location);

        return $events;
    }

    public function grabEventsByCoordinatesScale($lat, $lng, $uId)
	{
        $MemberFilter = new MemberFilter();
        $member_categories = $MemberFilter->getbyId($uId);

        $tagCategories = array();
        if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
            $results = Tag::find('id IN (' . implode(',', $member_categories['tag']['value']) . ') GROUP BY category_id')->toArray();
            foreach($results as $tagCategory) {
                $tagCategories[] = $tagCategory['category_id'];
            }
        }

        $query = 'select event.*,
        				event.logo as logo,
        				location.alias as location,
        				event.latitude as location_latitude,
        				event.longitude as location_longitude,
        				venue.latitude as venue_latitude,
        				venue.longitude as venue_longitude,
        				location.latitudeMin as location_latitudeMin,
        				location.latitudeMax as location_latitudeMax,
        				location.longitudeMin as location_longitudeMin,
        				location.longitudeMax as location_longitudeMax,
        				category.*
					from \Frontend\Models\Event as event
					left join \Frontend\Models\Venue as venue on event.venue_id = venue.id
					left join \Frontend\Models\Location as location on event.location_id = location.id
					LEFT JOIN \Frontend\Models\EventCategory AS ec ON (event.id = ec.event_id)
                    LEFT JOIN \Frontend\Models\Category AS category ON (category.id = ec.category_id)
                    LEFT JOIN \Frontend\Models\EventTag AS et ON (event.id = et.event_id)
                    LEFT JOIN \Frontend\Models\Tag AS tag ON (tag.id = et.tag_id)';

        if (!empty($uId)) {
            $query .= 'LEFT JOIN \Frontend\Models\EventLike AS event_like ON (event_like.event_id = event.id and event_like.member_id = '.$uId.')';
        }

        $query .= 'where (location.latitudeMin <= ' . $lat . '
			        	and location.latitudeMax >= ' . $lat . '
			        	and location.longitudeMin <= ' . $lng . '
			        	and location.longitudeMax >= ' . $lng . ')';

        if (array_key_exists('category', $member_categories) && !empty($member_categories['category']['value'])) {
            $member_categories['category']['value'] = array_diff($member_categories['category']['value'], $tagCategories);

            if (count($member_categories['category']['value']) > 0) {
                $query .= ' AND ec.category_id IN ('.implode(',', $member_categories['category']['value']).')';
            }
        }

        if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
            if (array_key_exists('category', $member_categories) && !empty($member_categories['category']['value']) && count($member_categories['category']['value']) > 0) {
                $query .= ' OR';
            } else {
                $query .= ' AND';
            }
            $query .= ' et.tag_id IN ('.implode(',', $member_categories['tag']['value']) .')';
        }

        if (!empty($uId)) {
            $query .= ' AND (event_like.status != 0 OR event_like.status IS NULL)';
        }

        $query .= ' AND event.event_status = 1';
        $query .= ' AND event.deleted = 0';

        $query .= ' GROUP BY event.id';

		$eventsList = $this -> getModelsManager() -> executeQuery($query);

		return $eventsList;
	}


    public function setCondition($condition)
    {
        if (!empty($condition)) {
            $this -> conditions[] = (string)$condition;
        }
       
        return $this;
    }

    public function setSelector($selector)
    {
    	if (!empty($selector)) {
    		$this -> selector = (string)$selector;
    	}
    	 
    	return $this;
    }

    public function listEvent()
    {
        $query = '
                SELECT event.*, category.*, location.*, venue.name AS venue
                FROM \Frontend\Models\Event AS event
                LEFT JOIN \Frontend\Models\EventCategory AS ec ON (event.id = ec.event_id)
                LEFT JOIN \Frontend\Models\Category AS category ON (category.id = ec.category_id)
                LEFT JOIN \Frontend\Models\Location AS location ON (event.location_id = location.id)
                LEFT JOIN \Frontend\Models\Venue AS venue ON (location.id = venue.location_id AND event.fb_creator_uid = venue.fb_uid)
                LEFT JOIN \Frontend\Models\EventLike AS event_like ON (event.id = event_like.event_id AND event_like.status = 1)
                LEFT JOIN \Objects\EventMember AS event_member ON (event.id = event_member.event_id AND event_member.member_status = 1)
            ';

        $this->conditions = array_merge($this->conditions, $this->defaultConditions);

        if (!empty($this -> conditions)) {
            $query .= ' WHERE';
            $count = count($this -> conditions);
            for ($i = 0; $i < $count; $i++) {
                if ($i !== 0) {
                	$query .= $this -> selector;
               	}
                $query .= " " . $this -> conditions[$i];
            }

            $query .= ' GROUP BY event.id';
            $result = $this -> getModelsManager() -> executeQuery($query);
            $result -> setHydrateMode(Resultset::HYDRATE_ARRAYS);

            $result = $result->toArray();
        }
        return $result;
    }


    /**
     * Add condition to model for fetching
     *
     * @param $condition
     * @param int $type
     * @return $this
     */
    public function addCondition($condition, $type = self::CONDITION_COMPLEX)
    {
	    if (!empty($condition)) {
            $cond = [
                'type' => $type,
                'condition' => (string)$condition
            ];
	    	$this -> conditions[] = $cond;
	    }
	    
	    return $this;
    }

    /**
     * Get event by conditions which set through Frontend\Models\Event::addCondition()
     *
     * @param int $fetchType
     * @param int $order
     * @param array $pagination
     * @return array|mixed|\Phalcon\Paginator\Adapter\stdClass
     */
    public function fetchEvents($fetchType = self::FETCH_OBJECT, $order = self::ORDER_DESC, $pagination = [])
    {
        $builder = $this->getModelsManager()->createBuilder();

        $builder->from('Frontend\Models\Event');

        $builder->leftJoin('Frontend\Models\EventCategory', 'Frontend\Models\Event.id = Frontend\Models\EventCategory.event_id')
            ->leftJoin('Frontend\Models\Category', 'Frontend\Models\EventCategory.category_id = Frontend\Models\Category.id')
            ->leftJoin('Frontend\Models\Location', 'Frontend\Models\Event.location_id = Frontend\Models\Location.id')
            ->leftJoin('Frontend\Models\Venue', 'Frontend\Models\Location.id = Frontend\Models\Venue.id AND Frontend\Models\Event.fb_creator_uid = Frontend\Models\Venue.fb_uid')
            ->leftJoin('Objects\EventSite', 'Objects\EventSite.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventMemberFriend', 'Frontend\Models\EventMemberFriend.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventLike', 'Frontend\Models\EventLike.event_id = Frontend\Models\Event.id')
            ->leftJoin('Objects\EventMember', 'Objects\EventMember.event_id = Frontend\Models\Event.id');

        $this->conditions = array_merge($this->conditions, $this->defaultConditions);

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $prevCondition = $builder->getWhere();

                if (!empty($prevCondition)) {
                    if ($condition['type'] === self::CONDITION_COMPLEX) {
                        $builder->where($prevCondition.' AND '.$condition['condition']);
                    }elseif ($condition['type'] === self::CONDITION_SIMPLE) {
                        $builder->where($prevCondition.' '.$condition['condition']);
                    }
                }else {
                    $builder->where($condition['condition']);
                }
            }
        }

        if ($order === self::ORDER_DESC) {
            $builder->orderBy('Frontend\Models\Event.id DESC');
        }elseif ($order === self::ORDER_ASC) {
            $builder->orderBy('Frontend\Models\Event.id ASC');
        }

        $builder->groupBy('Frontend\Models\Event.id');

        if (!empty($pagination)) {
            $paginator = new \Phalcon\Paginator\Adapter\QueryBuilder(array(
                'builder' => $builder,
                'limit'=> $pagination['limit'],
                'page' => $pagination['page']
            ));

            $result = $paginator->getPaginate();

            $totalRows = $builder->getQuery()->execute()->count(); // WTF?
            $result->total_pages = (int)ceil($totalRows / $pagination['limit']);
            $result->total_items = $totalRows;

            if ($fetchType === self::FETCH_ARRAY) {
                $result->items = $this->resultToArray($result->items);
            }
        } else {
            $result = $builder->getQuery()->execute();

            if ($fetchType === self::FETCH_ARRAY) {
                $result = $this->resultToArray($result);
            }
        }

        return $result;
    }


    public function parseNewEvents($data, $returnExists = false, $queryType = false)
    {
        $cfg = $this -> getConfig();
        $newEvents = array();
        $lastParsedEvent = 0;
        $locationsScope = self::$cacheData -> get('locations');

        if (!empty($data)) {
            foreach($data as $item => $ev) {

                if (!self::$cacheData -> exists('fbe_' . $ev['eid']) && 
                    (isset($ev['venue']) && !empty($ev['venue']) || $queryType == 'user_event')) 
                {
                    $result = array();
                    $result['fb_uid'] = $ev['eid'];
                    $result['fb_creator_uid'] = $ev['creator'];
                    $result['description'] = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $ev['description']);
                    $result['name'] = $ev['name'];

                    if (isset($ev['pic_big']) && !empty($ev['pic_big'])) {
                        $ext = explode('.', $ev['pic_big']);
                        $logo = 'fb_' . $ev['eid'] . '.' . end($ext);
                        $result['logo'] = $logo;
                    }

                    if (!empty($ev['start_time'])) {
                        $result['start_date'] = date('Y-m-d', strtotime($ev['start_time']));
                        $result['start_time'] = date('H:i', strtotime($ev['start_time']));
                    }
                    if (!empty($ev['end_time'])) {
                        $result['end_date'] = date('Y-m-d', strtotime($ev['end_time']));
                        $result['end_time'] = date('H:i', strtotime($ev['end_time']));
                    }

                    if (empty($result['end_date']) && !empty($result['start_date'])) {
                        $result['end_date'] = date('Y-m-d H:m:i', strtotime($result['start_date'].' + 1 week'));
                    }

                    if (self::$cacheData -> exists('member_' . $ev['creator'])) {
                        $result['member_id'] = self::$cacheData -> get('member_' . $ev['creator']);
                    }

                    $result['location_id'] = '';
                    if (isset($ev['venue']['id']) && !(self::$cacheData -> exists('venue_' . $ev['venue']['id']))) {

                        if (isset($ev['venue']['latitude']) && isset($ev['venue']['longitude']) && 
                            $ev['venue']['latitude'] != '' && $ev['venue']['longitude'] != '') 
                        {

                            if (!empty($locationsScope)) {
                                foreach ($locationsScope as $loc_id => $coords) {
                                    if ($ev['venue']['latitude'] >= $coords['latMin'] && $coords['latMax'] >= $ev['venue']['latitude'] &&
                                        $ev['venue']['longitude'] <= $coords['lonMax'] && $coords['lonMin'] <= $ev['venue']['longitude'])
                                    {
                                        $result['location_id'] = $loc_id;

                                        if ($ev['venue']['street'] != '') {
                                            $result['latitude'] = $ev['venue']['latitude'];
                                            $result['longitude'] = $ev['venue']['longitude'];
                                        } else {
                                            $result['latitude'] = ($coords['latMin'] + $coords['latMax']) / 2;
                                            $result['longitude'] = ($coords['lonMin'] + $coords['lonMax']) / 2;
                                        }

                                        break;
                                    }
                                }
                            }

                            if ($result['location_id'] == '') {
                                $locator = new Location();
                                $loc = $locator -> createOnChange(array('latitude' => $ev['venue']['latitude'],
                                                                        'longitude' => $ev['venue']['longitude']));
                                $locationsScope[$loc -> id] = array('latMin' => $loc -> latitudeMin,
                                                                    'lonMin' => $loc -> longitudeMin,
                                                                    'latMax' => $loc -> latitudeMax,
                                                                    'lonMax' => $loc -> longitudeMax,
                                                                    'city' => $loc -> city,
                                                                    'country' => $loc -> country);
                                 
                                self::$cacheData -> delete('locations');
                                self::$cacheData -> save('locations', $locationsScope);  

                                $result['location_id'] = $loc -> id;
                                $result['latitude'] = ($loc -> latitudeMin + $loc -> latitudeMax) / 2;
                                $result['longitude'] = ($loc -> longitudeMin + $loc -> longitudeMax) / 2;
                            }                                
                        } 

                        if ($ev['venue']['street'] != '') {
                            $venueObj = new Venue();
                            $venueObj -> assign(array(
                                    'fb_uid' => $ev['venue']['id'],
                                    'location_id' => $result['location_id'],
                                    'name' => $ev['location'],
                                    'address' => $ev['venue']['street'],
                                    'latitude' => $ev['venue']['latitude'],
                                    'longitude' => $ev['venue']['longitude']
                            ));
                            if ($venueObj -> save()) {
                                $result['venue_id'] = $venueObj -> id;
                                $result['address'] = $venueObj -> address;

                                self::$cacheData -> save('venue_' . $venueObj -> fb_uid, 
                                                        array('venue_id' => $venueObj -> id,
                                                              'address' => $venueObj -> address,
                                                              'location_id' => $venueObj -> location_id,
                                                              'latitude' => $venueObj->latitude,
                                                              'longitude' => $venueObj->longitude));
                            }
                        }
                    } elseif (isset($ev['venue']['id']) && self::$cacheData -> exists('venue_' . $ev['venue']['id'])) {
                        $venue = self::$cacheData -> get('venue_' . $ev['venue']['id']);
                        $result['venue_id'] = $venue['venue_id'];
                        $result['address'] = $venue['address'];
                        $result['latitude'] = $venue['latitude'];
                        $result['longitude'] = $venue['longitude'];
                        $result['location_id'] = $venue['location_id'];
                    } else {
                        if (isset($ev['location']) && $ev['location'] != '' && !empty($locationScope)) 
                        {
                            foreach ($locationsScope as $loc_id => $coords) {
                                if (strpos($ev['location'], $coords['city']))
                                {
                                    $result['location_id'] = $loc_id;
                                    $result['latitude'] = ($coords['latMin'] + $coords['latMax']) / 2;
                                    $result['longitude'] = ($coords['lonMin'] + $coords['lonMax']) / 2;

                                    break;
                                }
                            }
                        }
                    }

                    $Text = new Text();
                    $Text -> addContent($result['name'])
                          -> addContent($result['description'])
                          -> returnTag(true);

                    $categoryzator = new Categoryzator($Text);
                    $newText = $categoryzator->analiz(Categoryzator::MULTI_CATEGORY);
                    $cats = array();
                    $tags = array();

                    foreach ($newText->category as $key => $c) {
                        $cat = Category::findFirst("key = '".$c."'");
                        $cats[$key] = new EventCategoryObject();
                        $cats[$key]->category_id = $cat->id;
                    }

                   /* foreach ($newText->tag as $c) {
                        foreach ($c as $key => $tag) {
                            $Tag = TagObject::findFirst("key = '".$tag."'");
                            if ($Tag) {
                                $tags[$key] = new EventTagObject();
                                $tags[$key]->tag_id = $Tag->id;
                            }
                        }
                    }*/

                    $result['event_category'] = $cats;
                    //$result['event_tag'] = $tags;

                    $this -> hasMany('id', '\Objects\EventCategory', 'event_id', array('alias' => 'event_category'));
                    //$this -> hasMany('id', '\Objects\EventTag', 'event_id', array('alias' => 'event_tag'));
                    $eventObj = new self;
                    $eventObj -> assign($result);

                    if ($eventObj -> save()) {
                        if (isset($ev['pic_big']) && !empty($ev['pic_big'])) {
                            $ch =  curl_init($ev['pic_big']);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
                            $content = curl_exec($ch);
                            if ($content) {
                                if (!is_dir($cfg -> application -> uploadDir . 'img/event/'.$eventObj->id)) {
                                    mkdir($cfg -> application -> uploadDir . 'img/event/'.$eventObj->id);
                                }
                                $f = fopen($cfg -> application -> uploadDir . 'img/event/'.$eventObj->id.'/'.$logo, 'wb');
                                fwrite($f, $content);
                                fclose($f);
                            }
                        }

                        $images = new EventImage();
                        $images -> assign(array(
                                'event_id' => $eventObj -> id,
                                'image' => $ev['pic_big']
                            ));
                        $images -> save();
                        self::$cacheData -> save('fbe_' . $ev['eid'], $eventObj -> id);
                        $newEvents[$eventObj -> id] = $eventObj -> fb_uid;

                    }
                } elseif ($returnExists !== false && self::$cacheData -> exists('fbe_' . $ev['eid']) && isset($ev['venue'])) {
                    $newEvents[self::$cacheData -> get('fbe_' . $ev['eid'])] = $ev['eid'];
                } 
            }
        }

        return $newEvents;
    }
} 