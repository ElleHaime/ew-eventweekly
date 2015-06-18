<?php 

namespace Frontend\Models;

use Objects\MemberNetwork as MemberNetworkObject,
	Core\Utils as _U;

class MemberNetwork extends MemberNetworkObject
{
	public function addMemberNetwork($member, $userUid, $userName)
	{
		$memberNetwork = new MemberNetworkObject();
		$memberNetwork -> assign(['member_id' => $member -> id,
								  'network_id' => 1,
								  'account_uid' => $userUid,
								  'account_id' => $userName]);
		
		$memberNetwork -> save();
		
		return $memberNetwork;
	}
	
	
	public function addPermissions($base, $publish, $manage)
	{
		$memberNetwork = MemberNetworkObject::findFirst(['member_id = ' . $this -> getDI() -> getShared('session') -> get('memberId')]);
 		
		if ($memberNetwork) {
			$memberNetwork -> permission_base = $base;
			$memberNetwork -> permission_publish = $publish;
			$memberNetwork -> permission_manage = $manage;
			$memberNetwork -> update();
		}
	
		return $memberNetwork;
	}
} 