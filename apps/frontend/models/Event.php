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

	public static $eventStatus = array(0 => 'inactive',
							  		   1 => 'active');


	public static $eventRecurring = array('0' => 'Once',
										  '1' => 'Daily',
										  '7' => 'Weekly');
	protected $locator = false;

	private $conditions = [];

	private $selector = ' AND';

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
        if ($this -> start_date) {
            $tryTime = date('H:i', strtotime($this -> start_date));
            if ($tryTime != '00:00') {
                $this -> start_time = $tryTime;
            } else {
                $this -> start_time = '';
            }
            $tryDate = date('d/m/Y', strtotime($this -> start_date));
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
        }

        $this->slugUri = $this->id.'-'.SUri::slug($this->name);
	}

	public function grabEventsByFbId($token, $eventId)
	{
		$eventObj = self::findFirst('id = ' . $eventId);
		$fbId = $eventObj -> fb_uid;

		$this -> facebook = new Extractor();
		$event = $this -> facebook -> getEventById($fbId, $token);
		$event = $event[0]['fql_result_set'][0];

		return $event;
	}


	public function grabEventsByEwId($eventId)
	{
        $this -> hasManyToMany('id', '\Objects\EventCategory', 'event_id', 'category_id', '\Objects\Category', 'id',  array('alias' => 'event_category'));
		$eventObj = self::findFirst('id = ' . $eventId);

		return $eventObj;
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

        $query .= ' GROUP BY event.id';

		$eventsList = $this -> getModelsManager() -> executeQuery($query);

		return $eventsList;
	}


    public function parseEvent($data)
    {
        $membersList = MemberNetwork::find();
        $eventsList = self::find();
        $locationsList = Location::find();
        $venuesList = Venue::find();
        $locator = new Location();
        $cfg = $this -> getConfig();
        $geo = $this -> getGeo();

        if ($membersList) {
            $membersScope = array();
            foreach ($membersList as $mn) {
                $membersScope[$mn -> account_uid] = $mn -> member_id;
            }
        }

        if ($eventsList) {
            $eventsScope = array();
            foreach ($eventsList as $ev) {
                $eventsScope[$ev -> fb_uid] = array('id' => $ev -> id,
                    'start_date' => $ev -> start_date,
                    'end_date' => $ev -> end_date);
            }
        }

        if ($venuesList) {
            $venuesScope = array();
            foreach ($venuesList as $vn) {
                $venuesScope[$vn -> fb_uid] = array('venue_id' => $vn -> id,
                    'address' => $vn -> address,
                    'location_id' => $vn -> location_id,
                    'latitude' => $vn -> latitude,
                    'longitude' => $vn -> longitude);
            }
        }

        if ($locationsList) {
            $locationsScope = array();
            foreach ($locationsList as $loc) {
                $locationsScope[$loc -> id] = array('latMin' => $loc -> latitudeMin,
                    'lonMin' => $loc -> longitudeMin,
                    'latMax' => $loc -> latitudeMax,
                    'lonMax' => $loc -> longitudeMax,
                    'city' => $loc -> city,
                    'country' => $loc -> country);
            }
        }

        foreach($data as $source => $events) {

            if (!empty($events)) {
                foreach($events as $item => $ev) {
                    if (!isset($eventsScope[$ev['eid']])) {
                        $result = array();
                        $result['fb_uid'] = $ev['eid'];
                        $result['fb_creator_uid'] = $ev['creator'];
                        $ev['description'] = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" target="_blank">$1</a>', $ev['description']);
                        $result['description'] = $ev['description'];
                        $result['name'] = $ev['name'];

                        if (isset($ev['pic_big']) && !empty($ev['pic_big'])) {
                            $ext = explode('.', $ev['pic_big']);
                            $logo = 'fb_' . $ev['eid'] . '.' . end($ext);

                            $ch =  curl_init($ev['pic_big']);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
                            $content = curl_exec($ch);
                            if ($content) {
                                $f = fopen($cfg -> application -> uploadDir . 'img/event/' . $logo, 'wb');
                                fwrite($f, $content);
                                fclose($f);

                                $result['logo'] = $logo;
                            }
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

                        if (isset($membersScope[$ev['creator']])) {
                            $result['member_id'] = $membersScope[$ev['creator']];
                        }

                        $eventLocation = '';

                        if (!empty($ev['venue'])) {
                            if (!isset($venuesScope[$ev['venue']['id']])) {
                                if ($ev['venue']['latitude'] != '' && $ev['venue']['longitude'] != '') {
                                    $result['latitude'] = $ev['venue']['latitude'];
                                    $result['longitude'] = $ev['venue']['longitude'];
                                }

                                // check location by venue coordinates
                                if ($eventLocation == '') {
                                    foreach ($locationsScope as $loc_id => $coords) {
                                        if ($ev['venue']['latitude'] >= $coords['latMin'] && $coords['latMax'] >= $ev['venue']['latitude'] &&
                                            $ev['venue']['longitude'] <= $coords['lonMax'] && $coords['lonMin'] <= $ev['venue']['longitude'])
                                        {
                                            $eventLocation = $loc_id;
                                            break;
                                        }
                                    }
                                }

                                // create new location from coordinates
                                if ($eventLocation == '') {
                                    $loc = $locator -> createOnChange(array('latitude' => $ev['venue']['latitude'],
                                            'longitude' => $ev['venue']['longitude']));
                                    $eventLocation = $loc -> id;

                                    $locationsScope[$loc -> id] = array(
                                        'latMin' => $loc -> latitudeMin,
                                        'lonMin' => $loc -> longitudeMin,
                                        'latMax' => $loc -> latitudeMax,
                                        'lonMax' => $loc -> longitudeMax,
                                        'city' => $loc -> city,
                                        'country' => $loc -> country);
                                }

                                $venueObj = new Venue();
                                $venueObj -> assign(array(
                                        'fb_uid' => $ev['venue']['id'],
                                        'location_id' => $eventLocation,
                                        'name' => $ev['location'],
                                        'address' => $ev['venue']['street'],
                                        'latitude' => $ev['venue']['latitude'],
                                        'longitude' => $ev['venue']['longitude']
                                    ));
                                if ($venueObj -> save()) {
                                    $result['venue_id'] = $venueObj -> id;
                                    $result['address'] = $ev['venue']['street'];
                                    $result['location_id'] = $venueObj -> location_id;

                                    $venuesScope[$venueObj -> fb_uid] = array(
                                        'venue_id' => $venueObj -> id,
                                        'address' => $venueObj -> address,
                                        'location_id' => $venueObj -> location_id,
                                        'latitude' => $venueObj->latitude,
                                        'longitude' => $venueObj->longitude);
                                }
                            } else {
                                $result['venue_id'] = $venuesScope[$ev['venue']['id']]['venue_id'];
                                $result['address'] = $venuesScope[$ev['venue']['id']]['address'];
                                $result['location_id'] = $venuesScope[$ev['venue']['id']]['location_id'];
                                $result['latitude'] = $venuesScope[$ev['venue']['id']]['latitude'];
                                $result['longitude'] = $venuesScope[$ev['venue']['id']]['longitude'];
                            }
                        }


                        $Text = new Text();

                        $Text->addContent($result['name'])
                            ->addContent($result['description'])
                            ->returnTag(true);

                        $categoryzator = new Categoryzator($Text);

                        $newText = $categoryzator->analiz(Categoryzator::MULTI_CATEGORY);

                        $cats = array();
                        $tags = array();

                        foreach ($newText->category as $key => $c) {
                            $cat = Category::findFirst("key = '".$c."'");
                            $cats[$key] = new EventCategoryObject();
                            $cats[$key]->category_id = $cat->id;
                        }

                        foreach ($newText->tag as $c) {
                            foreach ($c as $key => $tag) {
                                $Tag = TagObject::findFirst("key = '".$tag."'");
                                if ($Tag) {
                                    $tags[$key] = new EventTagObject();
                                    $tags[$key]->tag_id = $Tag->id;
                                }
                            }
                        }

                        $result['event_category'] = $cats;
                        $result['event_tag'] = $tags;

                        /*$categoryzator = new Categoryzator($result['name']);
                        $titleText = $categoryzator->analiz(Categoryzator::MULTI_CATEGORY);

                        $categoryzator2 = new Categoryzator($result['description']);
                        $descriptionText = $categoryzator2->analiz(Categoryzator::MULTI_CATEGORY);

                        $titleCategory = $titleText->category;

                        $descriptionCategory = $descriptionText->category;

                        if (!empty($titleCategory) || !empty($descriptionCategory)) {
                            $categories = array_unique(array_merge($titleCategory, $descriptionCategory));

                            // TODO: small dirty code. need refactoring in categoryzator
                            if (count($categories) > 1 && in_array('other', $categories)) {
                                $categories = array_flip($categories);
                                unset($categories['other']);
                                $categories = array_flip($categories);
                            }

                            $cats = array();
                            foreach ($categories as $key => $c) {
                                $cat = Category::findFirst("key = '".$c."'");
                                $cats[$key] = new EventCategoryObject();
                                $cats[$key]->category_id = $cat->id;
                            }

                            $result['event_category'] = $cats;
                        }*/

                        $this->hasMany('id', '\Objects\EventCategory', 'event_id', array('alias' => 'event_category'));
                        $this->hasMany('id', '\Objects\EventTag', 'event_id', array('alias' => 'event_tag'));
                        $eventObj = new self;


                        $eventObj -> assign($result);
                        if ($eventObj -> save()) {
                            $images = new EventImage();
                            $images -> assign(array(
                                    'event_id' => $eventObj -> id,
                                    'image' => $ev['pic_big']
                                ));
                            $images -> save();

                            $data[$source][$item]['id'] = $eventObj -> id;
                            $data[$source][$item]['logo'] = $eventObj -> logo;

                            $eventsScope[$ev['eid']] = $eventObj -> id;
                        }

                    } else {
                        $data[$source][$item]['id'] = $eventsScope[$ev['eid']]['id'];
                        //$data[$source][$item]['logo'] = $eventsScope[$ev['eid']]['logo'];
                        if (!empty($eventsScope[$ev['eid']]['start_date'])) {
                            $data[$source][$item]['start_time'] = date('H:i', strtotime($eventsScope[$ev['eid']]['start_date']));
                            $data[$source][$item]['start_date_nice'] = date('d/m/Y', strtotime($eventsScope[$ev['eid']]['start_date']));
                        }
                        if (!empty($eventsScope[$ev['eid']]['end_date'])) {
                            $data[$source][$item]['end_time'] = date('H:i', strtotime($eventsScope[$ev['eid']]['end_date']));
                            $data[$source][$item]['end_date_nice'] = date('d/m/Y', strtotime($eventsScope[$ev['eid']]['end_date']));
                        }
                    }
                }
            }
        }

        return $data;
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
     * @return $this
     */
    public function addCondition($condition)
    {
	    if (!empty($condition)) {
	    	$this -> conditions[] = (string)$condition;
	    }
	    
	    return $this;
    }

    public function getCreatedEventsCount($uId)
    {
        if ($uId) {
            return self::find(array('member_id = ' . $uId . ' AND event_status = 1'))->count();
        } else {
            return 0;
        }
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
            ->leftJoin('Frontend\Models\EventLike', 'Frontend\Models\EventLike.event_id = Frontend\Models\Event.id')
            ->leftJoin('Objects\EventMember', 'Objects\EventMember.event_id = Frontend\Models\Event.id');

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $prevCondition = $builder->getWhere();

                if (!empty($prevCondition)) {
                    $builder->where($prevCondition.' AND '.$condition);
                }else {
                    $builder->where($condition);
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

            $totalRows = $builder->getQuery()->execute()->count();
            $result->total_pages = (int)ceil($totalRows / $pagination['limit']);
            $result->total_items = $totalRows;

            if ($fetchType === self::FETCH_ARRAY) {
                $result->items = $this->resultToArray($result->items);
            }
        }else {
            $result = $builder->getQuery()->execute();

            if ($fetchType === self::FETCH_ARRAY) {
                $result = $this->resultToArray($result);
            }
        }

        return $result;
    }
} 