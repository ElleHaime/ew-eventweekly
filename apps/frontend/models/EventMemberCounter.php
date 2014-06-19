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
	
	public function syncUnpublished($id)
	{
		$members = $this -> getMembers($id);
		$this -> updateCounters($id, 1, 5);
		$this -> syncCounters($members);
	}
	
	public function syncPublished($id)
	{
		$this -> updateCounters($id, 5, 1);
		$members = $this -> getMembers($id);
		$this -> syncCounters($members);
	}
	
	public function syncDeleted($id)
	{
		$members = $this -> getMembers($id);
		$this -> deleteMembers($id);
		$this -> syncCounters($members);		
	}

	
	private function updateCounters($eventId, $statusFrom, $statusTo)
	{
		$di = $this -> getDI();
		
		$query = new \Phalcon\Mvc\Model\Query("UPDATE Frontend\Models\EventLike
												SET Frontend\Models\EventLike.status = " . $statusTo . "
												WHERE Frontend\Models\EventLike.status = " . $statusFrom . "
												AND Frontend\Models\EventLike.event_id IN (" . $eventId . ")", $this -> getDI());
		$query -> execute();

		
		$query = new \Phalcon\Mvc\Model\Query("UPDATE Frontend\Models\EventMember
												SET Frontend\Models\EventMember.member_status = " . $statusTo . "
												WHERE Frontend\Models\EventMember.member_status = " . $statusFrom . "
												AND Frontend\Models\EventMember.event_id IN  (" . $eventId . ")", $this -> getDI());
		$query -> execute();
	}
	
	private function deleteMembers($id)
	{
		$di = $this -> getDI();
		
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventLike
												WHERE Frontend\Models\EventLike.status = " . \Frontend\Models\EventLike::LIKE . "
												AND Frontend\Models\EventLike.event_id IN (" . $id . ")", $this -> getDI());
		$query -> execute();
		
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventMember
												WHERE Frontend\Models\EventMember.member_status = " . \Frontend\Models\EventMember::JOIN . "
												AND Frontend\Models\EventMember.event_id IN (" . $id . ")", $this -> getDI());
		$query -> execute();
		
		$query = new \Phalcon\Mvc\Model\Query("DELETE FROM Frontend\Models\EventMemberFriend
												WHERE Frontend\Models\EventMemberFriend.event_id IN (" . $id . ")", $this -> getDI());
		$query -> execute();
	}

	private function getMembers($id) 
	{
		$di = $this -> getDI();
		$members = ['liked', 'going', 'friends'];
		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventLike.member_id
												FROM Frontend\Models\EventLike
												WHERE Frontend\Models\EventLike.status = " . \Frontend\Models\EventLike::LIKE . "
												AND Frontend\Models\EventLike.event_id IN (" . $id . ")", $this -> getDI());
		
		$result = $query -> execute();
		if ($result -> count() > 0) {
			foreach ($result as $item) {
				$members['liked'][] = $item -> member_id;
			}
		}
		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventMember.member_id
												FROM Frontend\Models\EventMember
												WHERE Frontend\Models\EventMember.member_status = " . \Frontend\Models\EventMember::JOIN . "
												AND Frontend\Models\EventMember.event_id IN (" . $id . ")", $this -> getDI());
		$result = $query -> execute();
		if ($result -> count() > 0) {
			foreach ($result as $item) {
				$members['going'][] = $item -> member_id;
			}
		}
		
		$query = new \Phalcon\Mvc\Model\Query("SELECT Frontend\Models\EventMemberFriend.member_id
												FROM Frontend\Models\EventMemberFriend
												WHERE Frontend\Models\EventMemberFriend.event_id IN (" . $id . ")", $this -> getDI());
		$result = $query -> execute();
		if ($result -> count() > 0) {
			foreach ($result as $item) {
				$members['friends'][] = $item -> member_id;
			}
		}
		
		return $members;
	}
	
	
	private function syncCounters($members = [])
	{
		$di = $this -> getDI();
		
		if ($members) {
			if (!empty($members['liked'])) {
				foreach ($members['liked'] as $key => $memberId) {
					$query = new \Phalcon\Mvc\Model\Query("SELECT DISTINCT Frontend\Models\EventLike.event_id
											 				FROM  Frontend\Models\EventLike
											 				WHERE Frontend\Models\EventLike.status = " . \Frontend\Models\EventLike::LIKE . "
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
															WHERE Frontend\Models\EventMember.member_status IN (" . \Frontend\Models\EventMember::JOIN . ", " . \Frontend\Models\EventMember::MAYBE . ")
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