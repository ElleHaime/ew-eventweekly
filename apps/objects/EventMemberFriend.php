<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventMemberFriend extends Model
{

	public $id;
	public $event_id;
	public $member_id;


	public function initialize()
	{
		$this -> hasMany('event_id', '\Object\Event', 'id', array('alias' => 'eventfriendart'));
		$this -> hasMany('member_id', '\Object\Member', 'id', array('alias' => 'memberfriendpart'));
	}
}