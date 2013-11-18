<?php 

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Venue extends Model
{
	public $id;
	public $fb_uid;
	public $location_id;
	public $name;
	public $address;	
	public $coordinates;
	public $latitude;
	public $longitude;

	public function initialize()
	{
		$this -> belongsTo('location_id', '\Object\Location', 'id', array('alias' => 'location'));
		$this -> hasMany('event_id', '\Object\Event', 'id', array('alias' => 'event'));
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