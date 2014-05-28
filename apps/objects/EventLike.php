<?php 

namespace Objects;

use Core\Model;

class EventLike extends Model
{
	const
	UNLIKE    	= 0,
	LIKE   		= 1,
	UNPUBLISHED	= 5;
	
	public $id;
    public $event_id;
    public $member_id;
    public $status;
	
	public function initialize()
	{
        parent::initialize();
                
        $this->belongsTo('event_id', '\Object\Event', 'id', array('alias' => 'event_like'));
        $this->belongsTo('member_id', '\Object\Member', 'id', array('alias' => 'event_like'));
    }
}