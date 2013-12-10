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
			$geo = $this -> getGeo();
			$argument = $geo -> getLocation();
		}
		$query = array();
		
		if (!empty($compare)) {
			foreach ($compare as $key => $value) {
				if ($value == 'latitude' || $value == 'longitude') {
					$query[] = $value . '=' . (float)$argument[$value];
				} else {
					$query[] = $value . ' like "%' . trim($argument[$value]) . '%"';					
				}
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
_U::dump($query, true);		
		$isLocationExists = self::findFirst($query);

		if (!$isLocationExists) {
			if (isset($argument['latitude']) && isset($argument['longitude']))
			{
				$geo = $this -> getGeo();
				$argument = $geo -> getLocation($argument);
_U::dump($argument, true);					
				$argument['latitude'] = (float)$argument['latitude'];
				$argument['longitude'] = (float)$argument['longitude'];
			}

			if (!isset($argument['id']) || empty($argument['id'])) {
				$argument[$network . '_id'] = null;
			} else {
				$argument[$network . '_id'] = $argument['id'];
				unset($argument['id']);
			}
			
			if (isset($argument['name']) || !empty($argument['name'])) {
				$argument['alias'] = $argument['name'];
				unset($argument['name']);
			} 
_U::dump($argument, true);				
			$this -> assign($argument);
			$this -> save();
			
			return $this;
			
		} else {
			return $isLocationExists;
		}
	} 
}
