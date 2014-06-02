<?php

namespace Core;

use Phalcon\Mvc\User\Plugin,
	Phalcon\Mvc\Dispatcher,
	Core\Utils as _U,
	Objects\LocationIp,
	\GeoIp2\WebService\Client;


class Geo extends Plugin
{
	const DEFAULT_RADIUS_DIFF = 10;
	protected $_unitTypes = array('locality',
								  'administrative_area_level_3',
								  'administrative_area_level_2',
								  'administrative_area_level_1',
								  'country');

	protected $_locLonCur				= false;
	protected $_locLatCur				= false;
	protected $_locLonMin				= false;
	protected $_locLatMin				= false;
	protected $_locLonMax 				= false;
	protected $_locLatMax				= false;
	protected $_cityCur 				= false;
	protected $_aliasCur 				= false;
	protected $_countryCur 				= false;
	protected $_stateCur 				= false;
	protected $_countryCode 			= false;	
	protected $_userIp 					= false;
	protected $_config 					= false;
	protected $_isLocationDefault 		= false;
	protected $di 						= false;
	protected $_errors					= array();
	
	public function __construct($dependencyInjector = null)
	{
		if ($dependencyInjector) {
			$this -> _di = $dependencyInjector;
			$this -> _config = $dependencyInjector -> get('config');
			$this -> _fb_config = $dependencyInjector -> get('facebook_config');
		} else {
			include(CONFIG_SOURCE);
			$this -> _config = json_decode(json_encode($cfg_settings), false);
		} 
		
		$this -> setUserIp();
	}
	
	public function setUserIp()
	{
		if ($this -> _fb_config -> debug) {
			$this -> _userIp = '31.172.138.197'; 		// Odessa
		} else {
			$this -> _userIp = $this -> request -> getClientAddress();
		} 
		return;
	}

	
	public function getUserIp()
	{
		return $this -> _userIp;
	}

	public function getFromCache()
	{
		$locExists = LocationIp::findFirst('ip = "' . $this -> _userIp. '"');

		if ($locExists) {
			$location['location_id'] = $locExists -> location_id;			

			return $location;
		} else {
			return false;
		}
	}

	public function setUserLocation()
	{
		try {
	        $client = new Client($this -> _config -> application -> GeoIp2 -> userId, 
	        					 $this -> _config -> application -> GeoIp2 -> licenseKey);
	
	        $record = $client->city($this->_userIp);
	
	        $this -> _locLatCur = $record->location->latitude;
	        $this -> _locLonCur = $record->location->longitude;
	        $this -> _countryCode = $record->country->isoCode;
		} catch (\Exception $e) { 
			$dublin = \Frontend\Models\Location::findFirst('city = "Dublin"');
			
			$this -> _locLatCur = ($dublin -> latitudeMax + $dublin -> latitudeMin)/2;
			$this -> _locLonCur = ($dublin -> longitudeMax + $dublin -> longitudeMin)/2;
			$this -> _locLonMin	= $dublin -> longitudeMin;
			$this -> _locLatMin	= $dublin -> latitudeMin;
			$this -> _locLonMax = $dublin -> longitudeMax;
			$this -> _locLatMax = $dublin -> latitudeMax;
			$this -> _cityCur = $dublin -> city;
			$this -> _stateCur = $dublin -> state;
			$this -> _aliasCur = $dublin -> alias;
			$this -> _countryCur = $dublin -> country;
			$this -> _isLocationDefault = true;
			
			$this -> _di -> get('session') -> set('isLocationDefined', false);
		}
		
        \Core\Logger::logFile('ips');
        \Core\Logger::log($this -> _userIp);
        if (isset($record)) {
            \Core\Logger::log($record);
        }
	}

	public function getUserLocation()
	{
		return array('latitude' => $this -> _locLatCur,
					 'longitude' => $this -> _locLonCur);
	}

	public function getLocation($coordinates = array())
	{
		if (empty($coordinates)) {
			
			if ($location = $this -> getFromCache()) {
				return $location;
			} else {
				$this -> setUserLocation();
				
				if ($this -> _isLocationDefault) {
					$location['ip'] = $this -> _userIp;
					$location['latitude'] = (float)$this -> _locLatCur ;
					$location['longitude'] = (float)$this -> _locLonCur;
					$location['latitudeMin'] = (float)$this -> _locLatMin;
					$location['longitudeMin'] = (float)$this -> _locLonMin;
					$location['latitudeMax'] = (float)$this -> _locLatMax;
					$location['longitudeMax'] = (float)$this -> _locLonMax;
					$location['alias'] = $this -> _aliasCur;
					$location['city'] = $this -> _cityCur;
					$location['state'] = $this -> _stateCur;
					$location['country'] = $this -> _countryCur;
					
					return $location;
				}		
			}
		}

		if (empty($coordinates)) {
			$queryParams = $this -> _buildQuery($this -> _locLatCur, $this -> _locLonCur, $this -> _countryCode); 
		} else {
			$queryParams = $this -> _buildQuery($coordinates['latitude'], $coordinates['longitude']); 
		}

		if ($queryParams != '') {
			$units = array();

			$url = 'http://maps.googleapis.com/maps/api/geocode/json?' . $queryParams. '&sensor=false&language=en';
			$result = json_decode(file_get_contents($url));

			if ($result -> status == 'OK' && count($result -> results) > 0) {
				foreach ($result -> results as $object => $details) {
					$units[$details -> types[0]] = $object;
				}
				if (!isset($units['locality'])) {
					$newArgs = $result -> results[0];
			
					foreach ($newArgs -> address_components as $objNew => $lvlNew) {
						if ($lvlNew -> types[0] == 'locality' || $lvlNew -> types[0] == 'postal_town') {
							$newRequestLoc = str_replace(' ', '+', $lvlNew -> short_name);
							$newComponents[] = 'locality:' . $newRequestLoc;
						}
						if ($lvlNew -> types[0] == 'administrative_area_level_1') {
							$newRequestArea = str_replace(' ', '+', $lvlNew -> short_name);
							$newComponents[] = 'administrative_area:' . $newRequestArea;
						}
						if ($lvlNew -> types[0] == 'country') {
							$newComponents[] = 'country:' . str_replace(' ', '+', $lvlNew -> short_name);
						}
					}
					if (!isset($newRequestLoc) && isset($newRequestArea)) {
						$newComponents[] = 'locality:' . $newRequestArea;
					}
					
					$url = 'http://maps.googleapis.com/maps/api/geocode/json?components=' . implode('|', $newComponents) . '&sensor=false&language=en';
					$result = json_decode(file_get_contents($url));
		
					if ($result -> status == 'OK' && count($result -> results) > 0) {
						foreach ($result -> results as $object => $details) {
							$units[$details -> types[0]] = $object;
						}
					}
				}

				if (isset($units['locality'])) {
					$scope = $result -> results[$units['locality']];
					$baseType = 'locality';
				} elseif (isset($units['administrative_area_level_3'])) {
					$scope = $result -> results[$units['administrative_area_level_3']];
					$baseType = 'administrative_area_level_3';
				} elseif (isset($units['administrative_area_level_2'])) {
					$scope = $result -> results[$units['administrative_area_level_2']];
					$baseType = 'administrative_area_level_2';
				} 
 
				if (isset($scope)) {			
					foreach ($scope -> address_components as $obj => $lvl) {
	
						if ($lvl -> types[0] == $baseType) {
							$location['alias'] = $lvl -> long_name;
							$location['city'] = $lvl -> long_name;
						}
						if ($lvl -> types[0] == 'administrative_area_level_1') {
							$location['state'] = $lvl -> long_name;
						}
						if ($lvl -> types[0] == 'country') {
							$location['country'] = $lvl -> long_name;
						}
					}
   
					if (isset($location['city']) && isset($location['country'])) {
						if (!empty($coordinates)) {
							$location['latitude'] = (float)$coordinates['latitude'];
							$location['longitude'] = (float)$coordinates['longitude'];
						} else {
							$location['ip'] = $this -> _userIp;
							$location['latitude'] = (float)$this -> _locLatCur ;
							$location['longitude'] = (float)$this -> _locLonCur;
						}

						if (!empty($result -> results[0] -> geometry)) {
							$location['latitudeMin'] = (float)$scope -> geometry -> viewport -> southwest -> lat;
							$location['longitudeMin'] = (float)$scope -> geometry -> viewport -> southwest -> lng;
							$location['latitudeMax'] = (float)$scope -> geometry -> viewport -> northeast -> lat;
							$location['longitudeMax'] = (float)$scope -> geometry -> viewport -> northeast -> lng;
						}						

						return $location;
					} 
				} else {
					return false;
				}
				
			} else {
			 	$return = $result -> status;
			}
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
