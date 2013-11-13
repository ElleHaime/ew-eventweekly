<?php

namespace Objects;

use Core\Model;

class Campaign extends Model
{
	public $id;
	public $member_id;
	public $name;
	public $description;
	public $logo;
	public $address;
	public $location_id;
	
	protected $dependency = array();
	
	public function initialize()
	{
		$this -> belongsTo('member_id', '\Objects\Member', 'id', array('alias' => 'member'));
		$this -> belongsTo('location_id', '\Objects\Location', 'id', array('alias' => 'location'));
		$this -> hasMany('id', '\Objects\Event', 'campaign_id', array('alias' => 'event'));
	}

	public function getDependency()
	{
		$dependency = array(
			'location' => array(
				'type' => 'belongsTo',
				'createOnChange' => true,
				'createOnChangeField' => 'name',
				'createOnChangeRelation' => 'location_id'
			),
			'member' => array(
				'type' => 'belongsTo'
			),
			'event' => array(
				'type' => 'hasMany'
			)
		);

		return $dependency;
	}

	public function beforeValidationOnCreate()
	{
	}
	
	public function afterSave()
	{
	}
	
	public function getContacts()
	{
	}
	
	public function validation()
	{
	}
	
}