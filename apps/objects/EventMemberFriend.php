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


	public static function setCache()
	{
		$friendEvents = self::find();
		
		if ($friendEvents) {
			foreach($friendEvents as $item => $event) {
				 if (self::$cacheData -> exists('fb_friend_event_' . $event -> event_id)) {
		            self::$cacheData -> save('fb_friend_event_' . $event -> event_id, $event -> member_id);
		        }
			}
			self::$cacheData -> save('fb_friend_events', 'cached');
		}
	}
}