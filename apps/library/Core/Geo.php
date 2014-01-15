<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Thirdparty\Geo\SxGeo as SGeo,
	Core\Utils as _U;


class Geo extends Plugin
{
	const DEFAULT_RADIUS_DIFF = 10;

	protected $_sxgeo		= false;
	protected $_locLonCur	= false;
	protected $_locLatCur	= false;
	protected $_locLonMin	= false;
	protected $_locLatMin	= false;
	protected $_locLonMax 	= false;
	protected $_locLatMax	= false;
	protected $_countryCode = false;
	protected $_userIp 		= false;
	protected $_config 		= false;
	protected $_errors		= array();
	
	public function __construct($dependencyInjector = null)
	{
		if ($dependencyInjector) {
			$this -> _config = $dependencyInjector -> get('config');
		} else {
			include(CONFIG_SOURCE);
			$this -> _config = json_decode(json_encode($cfg_settings), false);
		} 
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

			$this -> _locLatCur = $city['lat'];
			$this -> _locLonCur = $city['lon'];
			$this -> _countryCode = $city['country'];
		} else {
			$this -> _userIp = $this -> getUserIp();
		}
	}

	public function getUserLocation()
	{
		return array('latitude' => $this -> _locLatCur,
					 'longitude' => $this -> _locLonCur);
	}
	
	public function getUserIp()
	{
		return $this -> _userIp;
	}

	public function getLocation($coordinates = array())
	{
		if (empty($coordinates)) {
			$queryParams = $this -> _buildQuery($this -> _locLatCur, $this -> _locLonCur, $this -> _countryCode); 
		} else {
			$queryParams = $this -> _buildQuery($coordinates['latitude'], $coordinates['longitude']); 
		}

		if ($queryParams != '') {	
			$url = 'http://maps.googleapis.com/maps/api/geocode/json?' . $queryParams. '&sensor=false&language=en';
			$result = json_decode(file_get_contents($url));

			if ($result -> status == 'OK' && count($result -> results) > 0) {
				
				foreach($result -> results as $object => $details) {
					if ($details -> types[0] == 'locality') {
						$scope = $details;
					}
				}
                if (!isset($scope)) {
                    foreach($result -> results as $object => $details) {
                        if ($details -> types[0] == 'administrative_area_level_1') {
                            $scope = $details;
                        }
                    }
                }
				if ($scope) {			
					foreach ($scope -> address_components as $obj => $lvl) {
						if ($lvl -> types[0] == 'locality') {
							// city								
							$location['alias'] = $lvl -> long_name;
							$location['city'] = $lvl -> long_name;
						}
						if ($lvl -> types[0] == 'administrative_area_level_1') {
							// state
							$location['state'] = $lvl -> long_name;
						}
						if ($lvl -> types[0] == 'country') {
							// country
							$location['country'] = $lvl -> long_name;
						}
					}

					if (!empty($coordinates)) {
						$location['latitude'] = (float)$coordinates['latitude'];
						$location['longitude'] = (float)$coordinates['longitude'];
					} else {
						$location['latitude'] = (float)$this -> _locLatCur ;
						$location['longitude'] = (float)$this -> _locLonCur;
					}
					if (!empty($result -> results[0] -> geometry)) {
						$location['latitudeMin'] = (float)$scope -> geometry -> bounds -> southwest -> lat;
						$location['longitudeMin'] = (float)$scope -> geometry -> bounds -> southwest -> lng;
						$location['latitudeMax'] = (float)$scope -> geometry -> bounds -> northeast -> lat;
						$location['longitudeMax'] = (float)$scope -> geometry -> bounds -> northeast -> lng;
					}						
					
					return $location;
				} else {
					return false;
				}
				
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
		if ($lat && $lon) {
			$result[] = 'latlng=' . $lat . ',' . $lon;
		}
		
		return implode("&", $result);
	}

	public function getErrors()
	{
		return $this -> _errors;
	}
}
