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
	public $latitude;  	// decimal (10, 8)
	public $longitude;	// decimal (11, 8)

	public function initialize()
	{
		$this -> belongsTo('location_id', '\Object\Location', 'id', array('alias' => 'location'));
		$this -> hasMany('event_id', '\Object\Event', 'id', array('alias' => 'event'));
	}
	
	public function createOnChange($argument = array())
	{
		$isVenueExists = false;
		
		if (!empty($argument)) {
			if (isset($argument['latitude']) && isset($argument['longitude'])) {
				// find by coordinates
				$query = 'latitude = ' . (float)$argument['latitude'] . ' and longitude = ' . (float)$argument['longitude'];
				$isVenueExists = self::findFirst($query);
			} elseif (isset($argument['address'])) {
				$query = 'address like "%' . $argument['address'] . '%"';
				$isVenueExists = self::findFirst($query);
			}
		}

		if (!$isVenueExists) {
			$this -> assign($argument);
			$this -> save();
			
			return $this;
		} else {
			return  $isVenueExists;
		}
	}

}