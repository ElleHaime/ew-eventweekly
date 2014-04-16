<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class EventMemberCounter extends Model
{
	public $member_id;
	public $userEventsLiked = 0;
	public $userEventsGoing = 0;
	public $userEventsCreated = 0;
	public $userFriendsGoing = 0;


	public function initialize()
	{
		$this -> belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'counters'));
	}
}