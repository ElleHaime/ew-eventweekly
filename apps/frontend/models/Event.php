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
    use \Core\Traits\Facebook;

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

            $eventLike = EventLike::findFirst('event_id = "' . $eid . '" AND member_id = ' . $uid);
            if ($eventLike) {
                $eventLike->delete();
            }

            $eventGoing = \Frontend\Models\EventMember::findFirst('event_id = "' . $eid . '" AND member_id = ' . $uid);
            if ($eventGoing) {
                $eventGoing->delete();
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
    
    public function getRecurEvents($id)
    {
    	$result = [];
    
    	$shards = $this -> getAvailableShards();
    	foreach ($shards as $cri) {
    		$this -> setShard($cri);
    		$events = self::find(['recurring = "' . $id . '" AND id != "' . $id. '"']);
    
    		if ($events -> count() != 0) {
    			foreach ($events as $val) {
   					$result[$val -> id] = $val -> name;
    			}
    		}
    	}
    
    	return $result;
    }

    public function getCover()
    {
    	$this -> cover = false;
    	
    	if (isset($this -> image)) {
			foreach ($this -> image as $eventImage) {
				if ($eventImage -> type == 'cover') {
					$this -> cover = $eventImage;
	            }
	        }
		} 
		
		return $this;
	}
} 