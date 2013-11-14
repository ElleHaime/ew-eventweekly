<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Thirdparty\Geo\SxGeo as SGeo;


class Geo extends Plugin
{
	protected $_sxgeo	= false;
	protected $_locLon 	= array();
	protected $_locLat 	= array();
	protected $_userIp 	= false;
	protected $_config 	= false;
	protected $_errors	= array();
	
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
		} else {
			$this -> _userIp = $this -> getUserIp();
		}
	}
	
	public function getUserIp()
	{
		return $this -> _userIp;
	}

	public function getUserLocation($resultSet = array())
	{
		if ($this -> _locLat && $this -> _locLon) {
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $this -> _locLat . ',' . $this -> _locLon . '&sensor=false&language=en';
			$result = json_decode(file_get_contents($url));
		
			if ($result -> status == 'OK') {
				foreach ($result -> results as $item)	{
					foreach ($item -> address_components as $level) {
						if ($level -> types[0] == 'country') {
							$location['country'] = $level -> long_name;
						}
						
						if ($level -> types[0] == 'administrative_area_level_1') {
							$location['region'] = $level -> long_name;
						}
						
						if ($level -> types[0] == 'locality') {
							$location['city'] = $level -> long_name;
						}
						
						if ($level -> types[0] == 'sublocality') {
							$location['sublocality'] = $level -> long_name;
						}
					}
				}
				
				if (!empty($resultSet)) {
					$loc = '';
					foreach ($resultSet as $item => $part) {
						$loc .= $location[$part]  . ', ';						
					}
					$loc = substr($loc, 0, strlen($loc) - 2);
				} else {
					$loc = $location['country'].', ';
					$loc .= $location['region'].', ';
					$loc .= $location['city'];
				}
				
				return $loc;
			}
		} else {
			$this -> _errors[] = $result -> status; 
		} 

	}
	
	public function getErrors()
	{
		return $this -> _errors;
	}
}
