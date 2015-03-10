<?php 

namespace Frontend\Models;

use Objects\MemberNetwork as MemberNetworkObject;

class MemberNetwork extends MemberNetworkObject
{
	public function addMemberNetwork($member, $userUid, $userName)
	{
		$memberNetwork = new MemberNetwork();
		
		$memberNetwork -> assign(array(
				'member_id' => $member -> id,
				'network_id' => 1,
				'account_uid' => $userUid,
				'account_id' => $userName
		));
		
		$memberNetwork -> save();
		
		return $memberNetwork;
	}
} 