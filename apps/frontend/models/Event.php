<?php 

namespace Frontend\Models;

use Categoryzator\Core\CategoryzatorException;

use Categoryzator\Categoryzator,
    Categoryzator\Core\Text,
    Objects\Event as EventObject,
    Core\Utils as _U,
    Frontend\Models\Location,
    Frontend\Models\Venue,
    Frontend\Models\MemberNetwork,
    Objects\EventImage,
    Objects\Total,
    Objects\EventMember,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
    Frontend\Models\EventMemberFriend,
    Frontend\Models\EventLike,
    Objects\EventCategory AS EventCategoryObject,
    Objects\EventTag AS EventTagObject,
    Objects\Tag AS TagObject,
    Phalcon\Mvc\Model\Resultset,
    Core\Utils\SlugUri as SUri;


class Event extends EventObject
{
    use \Core\Traits\ModelConverter;
    use \Sharding\Core\Env\Phalcon;

    const FETCH_OBJECT = 1;
    const FETCH_ARRAY = 2;
    const ORDER_ASC = 3;
    const ORDER_DESC = 4;
    const CONDITION_SIMPLE = 5;
    const CONDITION_COMPLEX = 6;
    const DEF_FIELD = 'id';

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
    private $order;
    private $pagination = false;
    private $personalization = false;
    
    private $fetchType = self::FETCH_OBJECT;
    
    public $virtualFields = [
        'slugUri' => '\Core\Utils\SlugUri::slug(self->name).\'-\'.self->id',
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

            $eventGoing = \Frontend\Models\EventMember::findFirst('event_id = ' . $eid . ' AND member_id = ' . $uid);
            if ($eventGoing) {
                $eventGoing->delete();

                $userEventsGoing = $session -> get('userEventsGoing') - 1;
                $session -> set('userEventsGoing', $userEventsGoing);
            }
        }
    }
    
    public function setCacheTotal()
    {
    	$evTotal = Total::findFirst('entity = "event"');
    	$this -> getCache() -> save('eventsGTotal', $evTotal -> total);
    }
	
	public function afterFetch()
	{
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
        	$query = new \Phalcon\Mvc\Model\Query(
        			"SELECT Frontend\Models\Event.id, Frontend\Models\Event.fb_uid
	        		FROM Frontend\Models\Event
        			WHERE Frontend\Models\Event.deleted = 0
	        			AND Frontend\Models\Event.member_id = " . $uId,
        			$this -> getDI());
        	$event = $query -> execute();
        	return $event;
        } else {
            return 0;
        }
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
     * Add order condition
     *
     * @param $orderBy
     * @return $this
     */
    public function addOrder($orderBy)
    {
        if (!empty($orderBy)) {
            $this->order = $orderBy;
        }

        return $this;
    }
    
    public function setStart($start = 0)
    {
    	$this -> start = $start;
    	return $this;
    }
    
    public function setOffset($offset = 0)
    {
    	$this -> offset = $offset;
    	return $this;
    }
    
    
    /**
     * Set pagination
     */
    public function addPagination($pagination = false)
    {
    	if ($pagination) {
    		$this->pagination = true;
    	}
    
    	return $this;
    }
    
    /**
     * Set personalization
     */
    public function addPersonalization($personalization = false)
    {
    	if ($personalization) {
    		$this->personalization = true;
    	}
    
    	return $this;
    }
    
    
    /**
     * Set result format
     */
    public function setResultFormat($format)
    {
    	if ($format == self::FETCH_ARRAY) {
    		$this->fetchType = self::FETCH_ARRAY;
    	} else {
    		$this->fetchType = self::FETCH_OBJECT;
    	}
    
    	return $this;
    }
    
    public function setLogo($event, $logo) 
    {
    	$event -> logo = $logo;
    }
    
    
    public function fetchEvents($fetchType = self::FETCH_OBJECT, $order = self::ORDER_ASC, $pagination = [], $applyPersonalization = false, $limit = [], 
    								$memberFriend = false, $memberGoing = false, $memberLike = false, $needVenue = false, $needLocation = false, $eventTag = false, $categorySet = [])
    {
        $builder = $this->getModelsManager()->createBuilder();

        $builder->from('Frontend\Models\Event');
        $builder->leftJoin('Frontend\Models\EventCategory', 'Frontend\Models\Event.id = Frontend\Models\EventCategory.event_id')
	            ->leftJoin('Frontend\Models\Category', 'Frontend\Models\EventCategory.category_id = Frontend\Models\Category.id');
            
       	if ($memberFriend) {
       		$builder -> leftJoin('Frontend\Models\EventMemberFriend', 'Frontend\Models\EventMemberFriend.event_id = Frontend\Models\Event.id');
       	}
       	if ($memberGoing) {
       		$builder -> leftJoin('Frontend\Models\EventMember', 'Frontend\Models\EventMember.event_id = Frontend\Models\Event.id');
       	}
       	if ($memberLike) {
       		$builder -> leftJoin('Frontend\Models\EventLike', 'Frontend\Models\EventLike.event_id = Frontend\Models\Event.id');
       	} 
       	if ($eventTag || $applyPersonalization) {
       		$builder -> leftJoin('Frontend\Models\EventTag', 'Frontend\Models\Event.id = Frontend\Models\EventTag.event_id')
					 -> leftJoin('Frontend\Models\Tag', 'Frontend\Models\Tag.id = Frontend\Models\EventTag.tag_id');
       	}

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

        if ($applyPersonalization) {
            $di = $this->getDi();
            $session = $di->getShared('session');
            $uid = $session->get('memberId');

            $MemberFilter = new MemberFilter();
            
            if (empty($categorySet)) {
            	$member_categories = $MemberFilter->getbyId($uid);
            } else {
            	$member_categories = $MemberFilter->compareById($uid, $categorySet);
            }
//_U::dump($member_categories);           
            $tagCategories = array();
            if (array_key_exists('category', $member_categories) && !empty($member_categories['category']['value'])) {

	            if (count($member_categories['category']['value']) > 0) {
	                	$category = new Category();
	                	$defaultCategories = $category -> getDefaultIdsAsString();
	                	$extraCats = array_intersect($member_categories['category']['value'], explode(',', $defaultCategories));

						if (!empty($extraCats)) {
							if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
								$builder->where($prevCondition. ' AND (Frontend\Models\EventCategory.category_id IN ('.implode(',', $extraCats).')');
							} else {
								$builder->where($prevCondition. ' AND Frontend\Models\EventCategory.category_id IN ('.implode(',', $extraCats).')');
							}
						} 
	            }
				
				$prevCondition = $builder->getWhere();
				if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
					if (!empty($extraCats)) {
						$builder->where($prevCondition . ' OR Frontend\Models\EventTag.tag_id IN ('.implode(',', $member_categories['tag']['value']) .'))');
					} else {
						$builder->where($prevCondition . ' AND Frontend\Models\EventTag.tag_id IN ('.implode(',', $member_categories['tag']['value']) .')');
					}
				}
            }
        }

        if (empty($this->order)) {
            if ($order === self::ORDER_DESC) {
                $builder->orderBy('Frontend\Models\Event.start_date DESC');
            }elseif ($order === self::ORDER_ASC) {
                $builder->orderBy('Frontend\Models\Event.start_date ASC');
            }
        }else {
            $builder->orderBy($this->order);
        }
        
		if (!empty($limit)) {
        	$builder -> limit($limit['limit'], $limit['start']);
        }
        $builder->groupBy('Frontend\Models\Event.id');
        
		/*$f = fopen('/var/www/EventWeekly/var/logs/bububu.txt', 'a+');
		fwrite($f, $builder -> getPhql());
		fwrite($f, "\n\r\n\r");
		fclose($f);        */
//_U::dump($builder -> getPhql());

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
        } else {
            $result = $builder->getQuery()->execute();

            if ($fetchType === self::FETCH_ARRAY) {
                $result = $this->resultToArray($result);
            }
        }

        return $result;
    }
} 