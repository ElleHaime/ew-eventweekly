<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Thirdparty\Geo\SxGeo as SGeo;


class Geo extends Plugin
{
	const DEFAULT_RADIUS_DIFF = 10;

	protected $_sxgeo		= false;
	protected $_locLon 		= false;
	protected $_locLat 		= false;
	protected $_countryCode = false;
	protected $_userIp 		= false;
	protected $_config 		= false;
	protected $_errors		= array();
	
	public function __construct($dependencyInjector)
	{
		$this -> _dependencyInjector = $dependencyInjector;
		$this -> _config = $this -> _dependencyInjector -> get('config'); 
		$this -> _sxgeo = new SGeo($this -> _config -> application -> geo -> path . 'SxGeoCity.dat'); 
		
		$this -> setUserIp();
		$this -> setUserLocation();
	}
	
	public function setUserIp()
	{
		if ($this -> _config -> application -> debug) {
			$this -> _userIp = '31.172.138.197';
		} else {
			$this -> _userIp = $this -> request -> getClientAddress();
		}
		
		return;
	}

	public function setUserLocation()
	{
		if ($this -> _userIp) {
			$city = $this -> _sxgeo -> getCityFull($this -> _userIp);
						
			$this -> _locLat = $city['lat'];
			$this -> _locLon = $city['lon'];
			$this -> _countryCode = $city['country'];
		} else {
			$this -> _userIp = $this -> getUserIp();
		}
	}
	
	public function getUserIp()
	{
		return $this -> _userIp;
	}
	
	public function getUserCoordinates()
	{
		return array('latitude' => $this -> _locLat,
					 'longitude' => $this -> _locLon);
	}

	public function getLocation($coordinates = array())
	{
		if (empty($coordinates)) {
			$queryParams = $this -> _buildQuery($this -> _locLat, $this -> _locLon, $this -> _countryCode); 
		} else {
			$queryParams = $this -> _buildQuery($coordinates['latitude'], $coordinates['longitude']); 
		}

		if ($queryParams != '') {	
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?' . $queryParams. '&sensor=false&language=en';
			$result = json_decode(file_get_contents($url));
			
			if ($result -> status == 'OK' && count($result -> results) > 0) {
				foreach ($result -> results[0] -> address_components as $level) {
					if ($level -> types[0] == 'country') {
						$location['country'] = $level -> long_name;
					}
			
					if ($level -> types[0] == 'administrative_area_level_1') {
						$location['state'] = $level -> long_name;
					}
			
					if ($level -> types[0] == 'locality') {
						$location['city'] = $location['name'] = $level -> long_name;
					}
			
					if ($level -> types[0] == 'route') {
						$location['street'] = $level -> long_name;
					}
			
					if ($level -> types[0] == 'street_number') {
						$location['street_number'] = $level -> long_name;
					}
				}
				
				if (!empty($result -> results[0] -> geometry)) {
					$location['latitude'] = $result -> results[0] -> geometry -> location -> lat;
					$location['longitude'] = $result -> results[0] -> geometry -> location -> lng;
				}
				
				return $location;
				
			} else {
			 	$return = $result -> status;
			}
		} else {
			return false;
		}
	} 
	
	protected function _buildQuery($lat, $lon, $countryCode = false)
	{
		$result = array();


		if ($countryCode) {
			$result[] = 'region=' . $this -> _countryCode;
		}
		if ($this -> _locLat && $this -> _locLon) {
			$result[] = 'latlng=' . $lat . ',' . $lon;
		}
		
		return implode("&", $result);
	}
	
	
	public function buildCoordinateScale($latitude, $longitude)
	{
		$result = array();

		$result['lonMin'] = $longitude - self::DEFAULT_RADIUS_DIFF / abs(cos(deg2rad($latitude)) * 111.04);
		$result['lonMax'] = $longitude + self::DEFAULT_RADIUS_DIFF / abs(cos(deg2rad($latitude)) * 111.04);

		$result['latMin'] = $latitude - self::DEFAULT_RADIUS_DIFF / 111.04;
		$result['latMax'] = $latitude + self::DEFAULT_RADIUS_DIFF / 111.04;

		return $result;
	}


	public function getErrors()
	{
		return $this -> _errors;
	}
}
