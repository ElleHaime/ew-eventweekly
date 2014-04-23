<?php

namespace Frontend\Models;

use Objects\EventMemberCounter as EventMemberCounterObject,
	Frontend\Models\MemberNetwork,
	Frontend\Models\Event,
	Frontend\Models\EventLike,
	Frontend\Models\EventMember,
	Frontend\Models\EventMemberFriend,
	Core\Utils as _U;


class EventMemberCounter extends EventMemberCounterObject
{
	public function syncMemberCounter()
	{
		$di = $this -> getDI();
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\MemberNetwork.member_id 
												FROM Frontend\Models\MemberNetwork", $this -> getDI());
		$members = $query -> execute();
	 
		if ($members) {
		 	foreach ($members as $member) {
		 		$memberId = $member -> member_id;
_U::dump($memberId, true);		 		
		 		$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\Event.id
		 													FROM  Frontend\Models\Event
		 													WHERE Frontend\Models\Event.member_id = " . $memberId, $di);
		 		$created = $query -> execute() -> count(); 
		 		
		 		$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventMember.event_id
											 				FROM  Frontend\Models\EventMember
											 				WHERE Frontend\Models\EventMember.member_status = 1
		 														AND Frontend\Models\EventMember.member_id = " . $memberId, $di);
		 		$going = $query -> execute() -> count();
		 		
		 		$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventLike.event_id
											 				FROM  Frontend\Models\EventLike
											 				WHERE Frontend\Models\EventLike.status = 1
											 					AND Frontend\Models\EventLike.member_id = " . $memberId, $di);
		 		$liked = $query -> execute() -> count();
		 		
		 		$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventMemberFriend.event_id
											 				FROM  Frontend\Models\EventMemberFriend
											 				WHERE Frontend\Models\EventMemberFriend.member_id = " . $memberId, $di);
		 		$friends = $query -> execute() -> count();
		 		
		 		$counters = self::findFirst('member_id = ' . $memberId);
		 		if ($counters) {
		 			$upCounter = $counters;
		 		} else {
		 			$upCounter = new self;
		 			$upCounter -> assign(['member_id' => $memberId]);
		 		}

		 		$upCounter -> assign(['userEventsCreated' => $created,
					 					'userEventsLiked' => $liked,
					 					'userEventsGoing' => $going,
					 					'userFriendsGoing' => $friends]);
		 		$upCounter -> save();
		 	}
		}
		
		echo 'Counters synced';
		die();
	}
} 