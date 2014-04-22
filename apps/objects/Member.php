<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Phalcon\Mvc\Model\Validator\Uniqueness;

class Member extends Model
{
	public $id;
	public $email;
    public $extra_email;
	public $pass;
	public $phone;
	public $name;
	public $address;
	public $location_id;
	public $role;
	public $logo;
	public $auth_type = 'email';
	
	public function initialize()
	{
		$this -> hasOne('location_id', '\Objects\Location', 'id', array('alias' => 'location'));
		$this -> hasMany('id', '\Objects\Campaign', 'member_id', array('alias' => 'campaign'));
		$this -> hasMany('id', '\Objects\Event', 'member_id', array('alias' => 'event'));
		$this -> hasOne('id', '\Objects\MemberNetwork', 'member_id', array('alias' => 'network'));
		$this -> hasOne('id', '\Objects\EventMember', 'member_id', array('alias' => 'eventpart'));
		$this -> hasOne('id', '\Objects\EventMemberFriend', 'member_id', array('alias' => 'eventfriendpart'));
        $this -> hasMany('id', '\Objects\MemberFilter', 'member_id', array('alias' => 'member_filter'));
        $this -> hasMany('id', '\Objects\EventLike', 'member_id', array('alias' => 'event_like'));
        $this -> hasOne('id', '\Objects\EventMemberCounter', 'member_id', array('alias' => 'counters'));
	}
	
	public function getDependency()
	{
		$dependency = array(
				'location' => array(
					'type' => 'hasOne',
					'createOnChange' => true,
					'createOnChangeField' => 'alias',
					'createOnChangeRelation' => 'location_id'
				),
				'campaign' => array(
					'type' => 'hasMany'
				),
				'event' => array(
					'type' => 'hasMany'
				)
		);
	
		return $dependency;
	}
	
	public function validation()
	{
		$this -> validate(new Uniqueness(array(
				'field' => 'email',
				'message' => 'Email must be unique'
		)));

		if ($this -> validationHasFailed() == true) {
			return false;
		} else {
			return true;
		}	
	}
	
	public function fullDelete()
	{
		$this -> getRelated('event_like') -> delete();
		$this -> getRelated('eventpart') -> delete();
		$this -> getRelated('eventfriendpart') -> delete();
		$this -> getRelated('counters') -> delete();
		$this -> getRelated('network') -> delete();
		$this -> getRelated('member_filter') -> delete();
		
		return;
	}
} 