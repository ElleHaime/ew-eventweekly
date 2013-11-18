<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Phalcon\Mvc\Model\Validator\Uniqueness;

class Member extends Model
{
	public $id;
	public $email;
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
		
		return !$this -> validationHasFailed();		
	}
	
	public function beforeValidationOnCreate()
	{
	}
	
	public function afterSave()
	{
	}
} 