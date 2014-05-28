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

	public function getMemberCounter()
	{
		if ($this -> getDi() -> get('session') -> has('memberId')) {
			$model = EventMemberCounter::findFirst('member_id = ' . $this -> getDi() -> get('session') -> get('memberId'));
			
			if ($model) {
				return $model; 
			}
		}
		
		return false;
	}
	
	public function syncDeleted($id)
	{
		$di = $this -> getDI();
		$members = ['liked', 'going', 'friends'];
		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.member_id
												FROM Frontend\Models\EventLike
												WHERE Frontend\Models\EventLike.status = 1
												AND Frontend\Models\EventLike.event_id = " . $id, $this -> getDI());
		$result = $query -> execute();
		if ($result) {
			foreach ($result as $item) {
				$members['liked'][] = $item -> member_id;
			}
		}
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventLike 
												WHERE Frontend\Models\EventLike.status = 1
												AND Frontend\Models\EventLike.event_id = " . $id, $this -> getDI());
		$query -> execute();

		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventMember.member_id
												FROM Frontend\Models\EventMember
												WHERE Frontend\Models\EventMember.member_status = 1
												AND Frontend\Models\EventMember.event_id = " . $id, $this -> getDI());
		$result = $query -> execute();
		if ($result) {
			foreach ($result as $item) {
				$members['going'][] = $item -> member_id;
			}
		}
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventMember 
												WHERE Frontend\Models\EventMember.member_status = 1
												AND Frontend\Models\EventMember.event_id = " . $id, $this -> getDI());
		$query -> execute();
		
		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventMemberFriend.member_id
												FROM Frontend\Models\EventMemberFriend
												WHERE Frontend\Models\EventMemberFriend.event_id = " . $id, $this -> getDI());
		$result = $query -> execute();
		if ($result) {
			foreach ($result as $item) {
				$members['friends'][] = $item -> member_id;
			}
		}
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventMemberFriend
												WHERE Frontend\Models\EventMemberFriend.event_id = " . $id, $this -> getDI());
		$query -> execute();
		
		$this -> syncMemberCounter($members);
	}
	
	
	public function syncMemberCounter($members = [])
	{
		$di = $this -> getDI();
		
		if ($members) {
			if (!empty($members['liked'])) {
				foreach ($members['liked'] as $key => $memberId) {
					$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventLike.event_id
											 				FROM  Frontend\Models\EventLike
											 				WHERE Frontend\Models\EventLike.status = 1
											 					AND Frontend\Models\EventLike.member_id = " . $memberId, $di);
		 			$liked = $query -> execute() -> count();
					$counters = self::findFirst('member_id = ' . $memberId);
					if ($counters) {
						$counters -> userEventsLiked = $liked;
						$counters -> save();
					} 
						
				}
			}
			
			if (!empty($members['going'])) {
				foreach ($members['going'] as $key => $memberId) {
					$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventMember.event_id
															FROM  Frontend\Models\EventMember
															WHERE Frontend\Models\EventMember.member_status = 1
															AND Frontend\Models\EventMember.member_id = " . $memberId, $di);
					$going = $query -> execute() -> count();
					$counters = self::findFirst('member_id = ' . $memberId);
					if ($counters) {
						$counters -> userEventsGoing = $going;
						$counters -> save();
					}
				}
			}
			
			if (!empty($members['friends'])) {
				foreach ($members['friends'] as $key => $memberId) {
					$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventMemberFriend.event_id
															FROM  Frontend\Models\EventMemberFriend
															WHERE Frontend\Models\EventMemberFriend.member_id = " . $memberId, $di);
					$friends = $query -> execute() -> count();
					$counters = self::findFirst('member_id = ' . $memberId);
					if ($counters) {
						$counters -> userFriendsGoing = $friends;
						$counters -> save();
					}
				}	
			}
		}
	}
} 