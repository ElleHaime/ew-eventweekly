<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Objects\Member,
	Phalcon\Mvc\Model\Validator\Uniqueness;

class Network extends Model
{
	public $id;
	public $name		= 'facebook';
	public $appkey;
	public $appsecret;
	public $is_active 	= 1;
	
	public function initialize()
	{
		parent::initialize();
				
		$this -> hasOne('id', '\Objects\MemberNetwork', 'network_id', array('alias' => 'member'));
	}
}