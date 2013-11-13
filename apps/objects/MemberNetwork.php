<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Objects\Member,
	Phalcon\Mvc\Model\Validator\Uniqueness;

class MemberNetwork extends Model
{
	public $id;
	public $member_id;
	public $network_id;
	public $account_uid;
	public $account_id;
	
	public function initialize()
	{
		$this -> belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'member'));
		$this -> belongsTo('network_id', '\Objects\Network', 'id', array('alias' => 'network'));
	}
}