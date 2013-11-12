<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Event extends Model
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


	public function initialize()
	{
		$this -> hasOne('id', '\Object\Location', 'location_id', array('alias' => 'location'));
		$this -> hasMany('id', '\Object\EventImage', 'event_id', array('alias' => 'image'));
		$this -> belongsTo('member_id', '\Object\Member', 'id', array('alias' => 'member'));
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