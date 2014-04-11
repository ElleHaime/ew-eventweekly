<?php 

namespace Frontend\Models;

use Categoryzator\Categoryzator;
use Categoryzator\Core\Text;
use Objects\Event as EventObject,
    Core\Utils as _U,
    Frontend\Models\Location,
    Frontend\Models\Venue,
    Frontend\Models\MemberNetwork,
    Objects\EventImage,
    Objects\EventMember,
    Frontend\Models\Category,
    Frontend\Models\MemberFilter,
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
    private $order;
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
            return self::find(array('member_id = ' . $uId . ' AND deleted = 0'));
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
    
    public function fetchEventsCounter()
    {
    	
    }
    

    /**
     * Get event by conditions which set through Frontend\Models\Event::addCondition()
     * @param int $fetchType
     * @param int $order
     * @param array $pagination
     * @param bool $applyPersonalization
     * @return array|mixed|\Phalcon\Paginator\Adapter\stdClass
     */
    public function fetchEvents($fetchType = self::FETCH_OBJECT, $order = self::ORDER_ASC, $pagination = [], $applyPersonalization = false, $limit = [])
    {
        $builder = $this->getModelsManager()->createBuilder();

        $builder->from('Frontend\Models\Event');

        $builder->leftJoin('Frontend\Models\EventCategory', 'Frontend\Models\Event.id = Frontend\Models\EventCategory.event_id')
            ->leftJoin('Frontend\Models\Category', 'Frontend\Models\EventCategory.category_id = Frontend\Models\Category.id')
            ->leftJoin('Frontend\Models\Location', 'Frontend\Models\Event.location_id = Frontend\Models\Location.id')
            //->leftJoin('Frontend\Models\Venue', 'Frontend\Models\Location.id = Frontend\Models\Venue.id AND Frontend\Models\Event.fb_creator_uid = Frontend\Models\Venue.fb_uid')
            ->leftJoin('Frontend\Models\Venue', 'Frontend\Models\Event.venue_id = Frontend\Models\Venue.id')
            ->leftJoin('Objects\EventSite', 'Objects\EventSite.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventMemberFriend', 'Frontend\Models\EventMemberFriend.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventLike', 'Frontend\Models\EventLike.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventMember', 'Frontend\Models\EventMember.event_id = Frontend\Models\Event.id')
            ->leftJoin('Frontend\Models\EventTag', 'Frontend\Models\Event.id = Frontend\Models\EventTag.event_id')
            ->leftJoin('Frontend\Models\Tag', 'Frontend\Models\Tag.id = Frontend\Models\EventTag.tag_id');

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
            $member_categories = $MemberFilter->getbyId($uid);

            $tagCategories = array();
            if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
                $results = Tag::find('id IN (' . implode(',', $member_categories['tag']['value']) . ') GROUP BY category_id')->toArray();
                foreach($results as $tagCategory) {
                    $tagCategories[] = $tagCategory['category_id'];
                }
            }

            $prevCondition = $builder->getWhere();
            if (array_key_exists('category', $member_categories) && !empty($member_categories['category']['value'])) {
                $member_categories['category']['value'] = array_diff($member_categories['category']['value'], $tagCategories);

                if (count($member_categories['category']['value']) > 0) {
                    $builder->where($prevCondition. ' AND Frontend\Models\EventCategory.category_id IN ('.implode(',', $member_categories['category']['value']).')');
                }
            }

            $prevCondition = $builder->getWhere();
            if (array_key_exists('tag', $member_categories) && !empty($member_categories['tag']['value'])) {
                if (array_key_exists('category', $member_categories) && !empty($member_categories['category']['value']) && count($member_categories['category']['value']) > 0) {
                    $builder->where($prevCondition . ' OR Frontend\Models\EventTag.tag_id IN ('.implode(',', $member_categories['tag']['value']) .')');
                } else {
                    $builder->where($prevCondition . ' AND Frontend\Models\EventTag.tag_id IN ('.implode(',', $member_categories['tag']['value']) .')');
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