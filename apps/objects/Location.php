<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U;

class Location extends Model
{
	public $id;
	public $facebook_id;
	public $city;
	public $state;
	public $country;
	public $alias;
	public $coordinates;
	public $latitude;
	public $longitude;
	public $parent_id = 0;
	 

	public function initialize()
	{
		$this -> belongsTo('location_id', '\Objects\Member', 'id');
		$this -> belongsTo('location_id', '\Objects\Event', 'id');
		$this -> hasMany('id', '\Objects\Campaign', 'location_id', array('alias' => 'campaign'));
		$this -> belongsTo('location_id', '\Objects\Venue', 'id', array('alias' => 'venue'));
	}
	
	public function createOnChange($argument = array(), $compare = array(), $network = 'facebook')
	{
		if (empty($argument)) {
			$argument = $this -> geo -> getLocation();
		}
		$query = array();
		
		if (!empty($compare)) {
			foreach ($compare as $key => $value) {
				$query[] = $value . ' like "%' . trim($argument[$value]) . '%"';
			}
		} else {
			if (isset($argument['city'])) {
				$query[] = 'city like "%' . trim($argument['city']) . '%"';
			}
			if (isset($argument['country'])) {
				$query[] .= 'country like "%' . trim($argument['country']) . '%"';
			}
		}
		
		$query = implode(' and ', $query);
		$isLocationExists = self::findFirst($query);

		if (!$isLocationExists) {
			if (!isset($argument['id']) || empty($argument['id'])) {
				$argument['id'] = null;
			}
			
			$this -> assign(array(
				$network . '_id' => $argument['id'],
				'city' => $argument['city'],
				'country' => $argument['country'],
				'latitude' => (float)$argument['latitude'],
				'longitude' => (float)$argument['longitude'],
				'alias' => $argument['name']
			));
			$this -> save();
			
			return $this;
			
		} else {
			return $isLocationExists;
		}
	} 
}
