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
		$this -> belongsTo('location_id', '\Objects\Location', 'id', array('alias' => 'location',
																	 	   'baseField' => 'alias'));
		$this -> hasMany('id', '\Objects\Event', 'campaign_id', array('alias' => 'event'));
		$this -> hasMany('id', '\Objects\CampaignContact', 'campaign_id', array('alias' => 'contact'));
	}
}