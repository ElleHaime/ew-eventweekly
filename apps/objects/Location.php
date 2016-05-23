<?php

namespace Objects;

use Core\Model,
	Core\Utils as _U,
	Objects\LocationIp;

class Location extends Model
{
	public $id;
	public $facebook_id;
	public $city;
	public $state;
	public $country;
	public $alias;
	public $search_alias;
	public $serviceCity;
	public $serviceState;
	public $serviceCountry;
	public $latitude;
	public $longitude;
	public $latitudeMin;
	public $longitudeMin;
	public $latitudeMax;
	public $longitudeMax;
	public $place_id;
	 

	public function initialize()
	{
		parent::initialize();
		
		$this -> hasMany('id', '\Objects\Member', 'location_id', array('alias' => 'member'));
		$this -> hasMany('id', '\Frontend\Models\Event', 'location_id', array('alias' => 'event'));
		$this -> hasMany('id', '\Objects\Campaign', 'location_id', array('alias' => 'campaign'));
		$this -> hasMany('id', '\Objects\Venue', 'location_id', array('alias' => 'venue'));
	}

	public function setCache()
	{
		$query = new \Phalcon\Mvc\Model\Query("SELECT id, latitudeMin, longitudeMin, latitudeMax, longitudeMax, city, country FROM Objects\Location", $this -> getDI());
		$locations = $query -> execute();
		$locationsCache = array();

		if ($locations) {
			foreach ($locations as $loc) {
				$locationsCache[$loc -> id] = array('latMin' => $loc -> latitudeMin,
								                      'lonMin' => $loc -> longitudeMin,
								                      'latMax' => $loc -> latitudeMax,
								                      'lonMax' => $loc -> longitudeMax,
								                      'city' => $loc -> city,
								                      'country' => $loc -> country, 
													  'place_id' => $loc -> place_id);
			}
		}
		$this -> getCache() -> save('locations', $locationsCache);
	}

	
	public function checkInCache($argument = [])
	{
		$result = false;
		
		if ($this -> getCache() -> exists('locations')) {
			$locationsScope = $this -> getCache() -> get('locations');
			
			if (!empty($argument) && !empty($locationsScope)) {
				//$this -> checkInCache($argument);
	            foreach ($scope as $loc_id => $coords) {
	                if ($argument['latitude'] >= $coords['latMin'] && $coords['latMax'] >= $argument['latitude'] &&
	                    $argument['longitude'] <= $coords['lonMax'] && $coords['lonMin'] <= $argument['longitude'])
	                {
	                    $result = $scope[$loc_id];
	                    break;
	                }
	            }
			}
		}

		return $result;
	}
	

	public function addToCache($newLocation)
	{
		if(is_null($this -> getCache() -> exists('locations'))) {
			$this -> setCache();
		}
		$locationsScope = $this -> getCache() -> get('locations');

        if (!isset($locationsScope[$newLocation->id])) {
            $locationsScope[$newLocation->id] = ['latMin' => $newLocation->latitudeMin,
								                 'lonMin' => $newLocation->longitudeMin,
								                 'latMax' => $newLocation->latitudeMax,
								                 'lonMax' => $newLocation->longitudeMax,
								                 'city' => $newLocation->city,
								                 'country' => $newLocation->country,
            									 'place_id' => $newLocation->place_id];
            //$this -> getCache() -> delete('locations');
            $this -> getCache() -> save('locations', $locationsScope);
        }
	}
	

	public function createOnChange($argument = [], $network = 'facebook')
	{
// _U::dump($argument, true);		
		$isLocationExists = false;
		$saveIp = false;
		
		if (empty($argument)) $saveIp = true;
		//$isLocationExists = $this -> checkInCache($argument);

		if (!$isLocationExists) {
			$geo = $this -> getGeo();
			$isGeoObject = false;
			$newLoc = [];
	
			if (empty($argument)) {
				if ($argument = $geo -> getLocation()) {
// _U::dump($argument);
					$isGeoObject = true;
					if (isset($argument['location_id'])) {
						$isLocationExists = self::findFirst($argument['location_id']);
					}
				}
			}
// _U::dump($isLocationExists -> toArray());			
			if (!$isLocationExists) {
				$query = [];
				if (isset($argument['place_id'])) {
					$query[] = 'place_id = "' . $argument['place_id'] . '"';
				} else {
					if (isset($argument['longitude'])) {
						$query[] = 'longitudeMin <= ' .  (float)$argument['longitude'] . ' AND ' . (float)$argument['longitude'] . ' <= longitudeMax';
					}
					if (isset($argument['latitude'])) {
						$query[] = 'latitudeMin <= ' .  (float)$argument['latitude'] . ' AND ' . (float)$argument['latitude'] . ' <= latitudeMax';
					}
					if (isset($argument['city'])) {
						$query[] = 'city like "%' . $argument['city'] . '%"';
					}
					if (isset($argument['country'])) {
						$query[] = 'country like "%' . $argument['country'] . '%"';
					}
				}
				$query = implode(' and ', $query);
//_U::dump($query, true);
		        if (!empty($query)) {
		            $isLocationExists = self::findFirst($query);
		        } else {
		            $isLocationExists = false;
		        }
//_U::dump($isLocationExists, true);
				if (!$isLocationExists) {
					!$isGeoObject ? $newLoc = $geo -> getLocation($argument) : $newLoc = $argument;
//_U::dump($newLoc);
					if ($newLoc) {
						if (!isset($argument['id']) || empty($argument['id'])) {
							$newLoc[$network . '_id'] = null;
						} else {
							$newLoc[$network . '_id'] = $argument['id'];
						}
					}
					
					if (!empty($newLoc)) {
						$checkExistense = self::findFirst('place_id = "' . $newLoc['place_id'] . '"');
						if ($checkExistense) {
							$isLocationExists = $checkExistense;
						} else {
							$this -> assign($newLoc);
							$this -> save();
							$isLocationExists = $this;
						}

						if (isset($newLoc['ip'])) {
							$newIp = new LocationIp();
							$newIp -> saveNewIp($isLocationExists -> id, $newLoc['ip']);
						}
					} 
				} else {
					if ($saveIp !== false) {
						$newIp = new LocationIp();
						$newIp -> saveNewIp($isLocationExists -> id, $geo -> getUserIp());
					}
				}
				$isLocationExists -> latitudeMin = (float)$isLocationExists -> latitudeMin;
				$isLocationExists -> latitudeMax = (float)$isLocationExists -> latitudeMax;
				$isLocationExists -> longitudeMin = (float)$isLocationExists -> longitudeMin;
				$isLocationExists -> longitudeMax = (float)$isLocationExists -> longitudeMax;
				$isLocationExists -> latitude = ($isLocationExists -> latitudeMin + $isLocationExists -> latitudeMax)/2;
				$isLocationExists -> longitude = ($isLocationExists -> longitudeMin + $isLocationExists -> longitudeMax)/2;

//				$this -> addToCache($isLocationExists);
			} else {
				$isLocationExists -> latitude = ($isLocationExists -> latitudeMin + $isLocationExists -> latitudeMax)/2;
				$isLocationExists -> longitude = ($isLocationExists -> longitudeMin + $isLocationExists -> longitudeMax)/2;
			} 
		}
	
		return $isLocationExists;
	} 
}
