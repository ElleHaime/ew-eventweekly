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

	public static function setCache()
	{
		$locations = self::find();
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
		self::$cacheData -> save('locations', $locationsCache);
	}

	public function createOnChange($arguments = array(), $network = 'facebook')
	{
        $query = array();
        $location = null;

        $getLocation = function($arguments = null) {
            $geo = $this->getGeo();
            if (!empty($arguments)) {
                $res = $geo->getLocation($arguments);
            }else {
                $res = $geo->getLocation();
            }
            return $res;
        };

        // get location by IP
		if (empty($arguments)) {
            $arguments = $getLocation();
		}

        // if specify coords build query
		if (isset($arguments['longitude']) && isset($arguments['latitude'])) {
			$query[] = 'longitudeMin <= ' . (float)$arguments['longitude'] . ' AND ' . (float)$arguments['longitude'] . ' <= longitudeMax';
            $query[] = 'latitudeMin <= ' . (float)$arguments['latitude'] . ' AND ' . (float)$arguments['latitude'] . ' <= latitudeMax';
            $query = implode(' and ', $query);
		}

        // try find location in database
        if (!empty($query)) {
            $location = self::findFirst($query);
        }

        // If no location  in database
        if (empty($location)) {
            $location = new self();
            $newLoc = $getLocation($arguments);
            if (!empty($newLoc)) {
                $location->save($newLoc);
            }else {
                $location = $newLoc;
            }
        }
	
		return $location;
	} 
}
