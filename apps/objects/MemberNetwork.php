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
	public $needCache = true;
	
	public function initialize()
	{
		$this -> belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'member'));
		$this -> belongsTo('network_id', '\Objects\Network', 'id', array('alias' => 'network'));
	}

	public static function setCache()
	{
		$members = self::find();

		if ($members) {
			foreach($members as $member) {
				 if (!self::$cacheData -> exists('member_' . $member -> account_uid)) {
		            self::$cacheData -> save('member_' . $member -> account_uid, $member -> member_id);
		        }
			}
			self::$cacheData -> save('fb_members', 'cached');
		}
	}
}