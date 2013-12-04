<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class   Event extends Model
{
	public $id;
	public $fb_uid;
	public $fb_creator_uid;
	public $member_id;
	public $campaign_id;
	public $location_id;
	public $venue_id;
	public $name;
	public $description;
	public $tickets_url;
	public $start_date;
	public $end_date;
	public $recurring;
	public $event_status	= 0;
	public $coordinates;
	public $address;
	public $logo;
	public $is_description_full = 0; 


	public function initialize()
	{
		$this -> hasOne('id', '\Objects\Location', 'location_id', array('alias' => 'location'));
		$this -> hasMany('id', '\Objects\EventImage', 'event_id', array('alias' => 'image'));
		$this -> hasMany('id', '\Objects\EventMember', 'event_id', array('alias' => 'memberpart'));
		$this -> hasMany('id', '\Objects\EventCategory', 'event_id', array('alias' => 'event_category'));
	}
	
	public function beforeValidationOnCreate()
	{
	}
	
	public function afterSave()
	{
	}
	
	public function validation()
	{
	}
}