<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventMember extends Model
{
	const
	JOIN    	= 1,
	MAYBE   	= 1,
	DECLINE 	= 3,
	UNPUBLISHED	= 5;

	public $id;
	public $event_id;
	public $member_id;
	public $member_status;

	public function initialize()
	{
		parent::initialize();
				
		$this -> belongsTo('event_id', '\Object\Event', 'id', array('alias' => 'eventpart'));
		$this -> belongsTo('member_id', '\Object\Member', 'id', array('alias' => 'eventpart'));
	}
}