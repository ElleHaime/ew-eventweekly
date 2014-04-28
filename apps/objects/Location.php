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
	public $latitude;
	public $longitude;
	public $latitudeMin;
	public $longitudeMin;
	public $latitudeMax;
	public $longitudeMax;
	public $parent_id = 0;
	 

	public function initialize()
	{
		parent::initialize();
		
		$this -> hasMany('id', '\Objects\Member', 'location_id', array('alias' => 'member'));
		$this -> hasMany('id', '\Objects\Event', 'location_id', array('alias' => 'event'));
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
								                      'country' => $loc -> country);
			}
		}
		$this -> getCache() -> save('locations', $locationsCache);
	}

	public function checkInCache($argument = [], $scope = [])
	{
		$result = false;

		if (!empty($argument) && !empty($locationsScope)) {
			$this -> checkInCache($argument);
            foreach ($scope as $loc_id => $coords) {
                if ($argument['latitude'] >= $coords['latMin'] && $coords['latMax'] >= $argument['latitude'] &&
                    $argument['longitude'] <= $coords['lonMax'] && $coords['lonMin'] <= $argument['longitude'])
                {
                    $result = $scope[$loc_id];
                    break;
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
								                 'country' => $newLocation->country];
            $this -> getCache() -> delete('locations');
            $this -> getCache() -> save('locations', $locationsScope);
        }
	}


	public function createOnChange($argument = [], $network = 'facebook')
	{
		$isLocationExists = false;
		!is_null($this -> getCache() -> exists('locations')) 
						? $locationsScope = $this -> getCache() -> get('locations')
						: $locationsScope = [];

		if (!empty($argument) && !empty($locationsScope)) {
			$isLocationExists = $this -> checkInCache($argument, $locationsScope);
		}

		if (!$isLocationExists) {
			$geo = $this -> getGeo();
			$isGeoObject = false;
			$newLoc = array();
	
			if (empty($argument)) {
				$argument = $geo -> getLocation();
				if ($argument) {
					$isGeoObject = true;

					if (isset($argument['location_id'])) {
						$isLocationExists = self::findFirst($argument['location_id']);
					}
				}
			}

			if (!$isLocationExists) {

				$query = array();
				if (isset($argument['longitude'])) {
					$query[] = 'longitudeMin <= ' .  (float)$argument['longitude'] . ' AND ' . (float)$argument['longitude'] . ' <= longitudeMax';
				}
				if (isset($argument['latitude'])) {
					$query[] = 'latitudeMin <= ' .  (float)$argument['latitude'] . ' AND ' . (float)$argument['latitude'] . ' <= latitudeMax';
				}
				$query = implode(' and ', $query);

		        if (!empty($query)) {
		            $isLocationExists = self::findFirst($query);
		        } else {
		            $isLocationExists = false;
		        }

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
					
					if (!empty($newLoc)) {
						$this -> assign($newLoc);
						$this -> save();

						$isLocationExists = $this;

						if (isset($newLoc['ip'])) {
							$newIp = new LocationIp();
							$newIp -> assign([
								'location_id' => $isLocationExists -> id,
								'ip' => $newLoc['ip']
							]);
							$newIp -> save();
						}
					}
				}

				if (!empty($newLoc)) {
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

				$this -> addToCache($isLocationExists);
			}
		}
	
		return $isLocationExists;
	} 
}
