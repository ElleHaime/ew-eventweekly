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
	public $latitude;
	public $longitude;
	public $latitudeMin;
	public $longitudeMin;
	public $latitudeMax;
	public $longitudeMax;
	public $parent_id = 0;
	 

	public function initialize()
	{
		$this -> hasMany('id', '\Objects\Member', 'location_id', array('alias' => 'member'));
		$this -> hasMany('id', '\Objects\Event', 'location_id', array('alias' => 'event'));
		$this -> hasMany('id', '\Objects\Campaign', 'location_id', array('alias' => 'campaign'));
		$this -> hasMany('id', '\Objects\Venue', 'location_id', array('alias' => 'venue'));
	}
	
	
	public function createOnChange($argument = array(), $network = 'facebook')
	{
		$isGeoObject = false;
		$newLoc = false;
		
		if (empty($argument)) {
			$geo = $this -> getGeo();
			$argument = $geo -> getLocation();
			if ($argument) {
				$isGeoObject = true;
			}
		}
		$query = array();
//_U::dump($argument);		
		if (isset($argument['longitude'])) {
			$query[] = 'longitudeMin <= ' .  (float)$argument['longitude'] . ' AND ' . (float)$argument['longitude'] . ' <= longitudeMax';
		}
		if (isset($argument['latitude'])) {
			$query[] = 'latitudeMin <= ' .  (float)$argument['latitude'] . ' AND ' . (float)$argument['latitude'] . ' <= latitudeMax';
		}

		$query = implode(' and ', $query);
		$isLocationExists = self::findFirst($query);

		if (!$isLocationExists) {
			if (!$isGeoObject) {
				if (isset($argument['longitude']) && isset($argument['latitude'])) {
					$newLoc = $geo -> getLocation($argument);
				}				
			} else {
				$newLoc = $argument;
			}
			
			if ($newLoc) {
				if (!isset($argument['id']) || empty($argument['id'])) {
					$newLoc[$network . '_id'] = null;
				} else {
					$newLoc[$network . '_id'] = $argument['id'];
				}
			}
			
			$this -> assign($newLoc);
			$this -> save();
			
			$isLocationExists = $this;
		}

		if ($newLoc !== false) {
			$isLocationExists -> latitude = $newLoc['latitude'];
			$isLocationExists -> longitude = $newLoc['longitude'];
		} else {
			$isLocationExists -> latitude = (float)$argument['latitude'];
			$isLocationExists -> longitude = (float)$argument['longitude'];
		}
		$isLocationExists -> latitudeMin = (float)$isLocationExists -> latitudeMin;
		$isLocationExists -> latitudeMax = (float)$isLocationExists -> latitudeMax;
		$isLocationExists -> longitudeMin = (float)$isLocationExists -> longitudeMin;
		$isLocationExists -> longitudeMax = (float)$isLocationExists -> longitudeMax;
//_U::dump($isLocationExists);		
		return $isLocationExists;
	} 
}
